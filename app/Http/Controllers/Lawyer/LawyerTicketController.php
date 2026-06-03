<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\AiSuggestion;
use App\Models\Category;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Services\GeminiTextToSpeechService;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LawyerTicketController extends Controller
{
    public function index(Request $request)
    {
        $lawyerId = Auth::guard('lawyer')->id();

        $query = Ticket::query()
            ->with(['worker', 'company', 'category', 'latestMessage', 'messages'])
            ->where('lawyer_id', $lawyerId)
            ->latest('last_message_at')
            ->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $ticketNumber = ltrim(preg_replace('/\D+/', '', $search) ?? '', '0');

            $query->where(function ($q) use ($search, $ticketNumber) {
                if ($ticketNumber !== '') {
                    $q->where('id', $ticketNumber);
                }

                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('title_original', 'like', "%{$search}%")
                    ->orWhere('title_translated', 'like', "%{$search}%")
                    ->orWhere('last_message_preview', 'like', "%{$search}%")
                    ->orWhereHas('worker', function ($workerQuery) use ($search) {
                        $workerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('iqama_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id') && $request->company_id !== 'all') {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(10)->withQueryString();
        $companies = Company::assignedToLawyer($lawyerId)
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'email']);

        $categories = Category::query()
            ->select('categories.id', 'categories.name')
            ->join('lawyers_categories', 'categories.id', '=', 'lawyers_categories.category_id')
            ->where('lawyers_categories.lawyer_id', $lawyerId)
            ->where('categories.status', Category::STATUS_ACTIVE)
            ->distinct()
            ->orderBy('categories.name')
            ->get();

        $stats = [
            'total' => Ticket::where('lawyer_id', $lawyerId)->count(),
            'open' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'in_progress')->count(),
            'closed' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'closed')->count(),
        ];

        return view('lawyer.tickets.index', compact('tickets', 'stats', 'companies', 'categories'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeLawyerTicket($ticket);

        $ticket->load([
            'worker.position',
            'worker.preferredLanguage',
            'worker.preferedLanguage',
            'company',
            'lawyer',
            'category',
            'messages.attachments',
            'messages.aiSuggestions',
        ]);

        $messages = $ticket->messages()
            ->with(['attachments', 'aiSuggestions'])
            ->orderBy('message_order')
            ->orderBy('id')
            ->get();

        $this->ensureAiSuggestions($ticket, $messages);
        $messages->load(['attachments', 'aiSuggestions']);

        return view('lawyer.tickets.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeLawyerTicket($ticket);
        abort_if($ticket->status === 'closed', 422, 'لا يمكن الرد على تذكرة مغلقة.');

        $validated = $request->validate([
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],
            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],
            'is_ai_generated' => ['nullable', 'boolean'],
            'ai_suggestion_id' => ['nullable', 'integer', 'exists:ai_suggestions,id'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $lawyer = Auth::guard('lawyer')->user();
        $ticket->loadMissing('worker');

        $originalLanguage = $lawyer->preferred_language ?? 'ar';
        $translatedLanguage = $ticket->worker?->preferredLanguageCode() ?? ($validated['translated_language'] ?? null);
        $translatedMessage = $this->translateMessage(
            $validated['message_original'],
            $translatedLanguage,
            $originalLanguage,
            $validated['message_translated'] ?? null
        );

        $message = DB::transaction(function () use ($request, $ticket, $validated, $lawyer, $originalLanguage, $translatedLanguage, $translatedMessage) {
            $lastOrder = TicketMessage::where('ticket_id', $ticket->id)
                ->lockForUpdate()
                ->max('message_order');

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'lawyer',
                'sender_id' => $lawyer->id,
                'message_order' => ($lastOrder ?? 0) + 1,
                'message_original' => $validated['message_original'],
                'message_translated' => $translatedMessage,
                'original_language' => $originalLanguage,
                'translated_language' => $translatedMessage ? $translatedLanguage : ($validated['translated_language'] ?? null),
                'is_ai_generated' => $validated['is_ai_generated'] ?? false,
            ]);

            $this->storeAttachments($request, $message);

            $ticket->update([
                'status' => 'in_progress',
                'closed_at' => null,
                'last_message_preview' => Str::limit($validated['message_original'], 120),
                'last_message_at' => now(),
            ]);

            return $message;
        });

        $this->storeAiSuggestionAudio($message, $ticket, $validated, $translatedMessage);

        SystemNotifier::notifyTicketChange(
            ticket: $ticket->fresh(['worker', 'company', 'lawyer']),
            type: 'ticket_message_created',
            title: 'تم إضافة رد من المحامي',
            body: "تم إضافة رد جديد على التذكرة رقم {$ticket->id}.",
            actor: $lawyer,
            data: ['ticket_id' => $ticket->id, 'sender_type' => 'lawyer']
        );

        return back()->with('toast_success', __('tickets.messages.reply_sent'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorizeLawyerTicket($ticket);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'pending', 'in_progress', 'closed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
        ]);

        $data = [
            'status' => $validated['status'],
        ];

        if (! empty($validated['priority'])) {
            $data['priority'] = $validated['priority'];
        }

        if ($validated['status'] === 'closed') {
            $data['closed_at'] = now();
        } else {
            $data['closed_at'] = null;
        }

        $ticket->update($data);

        SystemNotifier::notifyTicketChange(
            ticket: $ticket->fresh(['worker', 'company', 'lawyer']),
            type: 'ticket_status_updated',
            title: 'تم تحديث حالة تذكرة',
            body: "تم تحديث حالة التذكرة رقم {$ticket->id} إلى {$validated['status']}.",
            actor: Auth::guard('lawyer')->user(),
            data: ['ticket_id' => $ticket->id, 'status' => $validated['status']]
        );

        return back()->with('toast_success', __('tickets.messages.status_updated'));
    }

    public function close(Ticket $ticket)
    {
        $this->authorizeLawyerTicket($ticket);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        SystemNotifier::notifyTicketChange(
            ticket: $ticket->fresh(['worker', 'company', 'lawyer']),
            type: 'ticket_closed',
            title: 'تم إغلاق تذكرة',
            body: "تم إغلاق التذكرة رقم {$ticket->id}.",
            actor: Auth::guard('lawyer')->user(),
            data: ['ticket_id' => $ticket->id, 'action' => 'closed']
        );

        return back()->with('toast_success', __('tickets.messages.closed'));
    }

    private function authorizeLawyerTicket(Ticket $ticket): void
    {
        abort_if(
            (int) $ticket->lawyer_id !== (int) Auth::guard('lawyer')->id(),
            403
        );
    }

    private function translateMessage(string $text, ?string $targetLanguage, ?string $sourceLanguage = null, ?string $fallback = null): ?string
    {
        $text = trim($text);
        $targetLanguage = is_string($targetLanguage) ? trim($targetLanguage) : null;
        $sourceLanguage = is_string($sourceLanguage) ? trim($sourceLanguage) : null;

        if ($text === '' || ! $targetLanguage) {
            return $fallback;
        }

        if ($sourceLanguage && $sourceLanguage === $targetLanguage) {
            return $fallback ?: $text;
        }

        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return $fallback;
        }

        try {
            $models = array_values(array_unique(array_filter([
                config('services.gemini.model', 'gemini-2.5-flash'),
                'gemini-2.5-flash',
                'gemini-flash-latest',
            ])));
            $timeout = (int) config('services.gemini.timeout', 20);

            foreach ($models as $model) {
                $response = Http::timeout($timeout)
                    ->retry(2, 300, null, false)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => "Translate the following lawyer message from {$sourceLanguage} to {$targetLanguage}. Preserve meaning, names, numbers, dates, and legal or employment terms. Return only the translated message without explanations.\n\nMessage:\n{$text}",
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                        ],
                    ]);

                if ($response->failed()) {
                    Log::warning('Gemini lawyer ticket message translation failed.', [
                        'model' => $model,
                        'target_language' => $targetLanguage,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    continue;
                }

                $translated = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                if ($translated !== '') {
                    return $translated;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Gemini lawyer ticket message translation exception.', [
                'target_language' => $targetLanguage,
                'message' => $exception->getMessage(),
            ]);
        }

        return $fallback;
    }

    private function ensureAiSuggestions(Ticket $ticket, $messages): void
    {
        $lawyerLanguage = Auth::guard('lawyer')->user()?->preferred_language ?? 'ar';

        foreach ($messages as $message) {
            if ($message->sender_type !== 'worker') {
                continue;
            }

            $currentSuggestion = $message->aiSuggestions->last();

            if ($currentSuggestion && ! $this->shouldRefreshGenericAiSuggestion($currentSuggestion)) {
                continue;
            }

            $suggestion = $this->generateAiReplySuggestion($ticket, $message, $lawyerLanguage);

            if (! $suggestion) {
                continue;
            }

            if ($currentSuggestion) {
                $currentSuggestion->update([
                    'suggested_reply' => $suggestion,
                    'suggested_language' => $lawyerLanguage,
                    'status' => 'pending',
                ]);

                continue;
            }

            AiSuggestion::create([
                'message_id' => $message->id,
                'suggested_reply' => $suggestion,
                'suggested_language' => $lawyerLanguage,
                'status' => 'pending',
            ]);
        }
    }

    private function shouldRefreshGenericAiSuggestion(AiSuggestion $suggestion): bool
    {
        if ($suggestion->status !== 'pending') {
            return false;
        }

        $reply = trim((string) $suggestion->suggested_reply);

        if ($reply === '') {
            return true;
        }

        $hasSaudiLaborBasis = Str::contains($reply, [
            'نظام العمل السعودي',
            'لائحته التنفيذية',
            'Saudi Labor Law',
            'Implementing Regulations',
        ]);

        $hasPracticalAction = Str::contains($reply, [
            'إرفاق',
            'المستندات',
            'سجلات',
            'الحضور',
            'كشف الحساب',
            'تحويل الراتب',
            'حماية الأجور',
            'مدد',
            'attendance',
            'bank',
            'documents',
            'Mudad',
            'Wage Protection',
        ]);

        return ! ($hasSaudiLaborBasis && $hasPracticalAction);
    }

    private function generateAiReplySuggestion(Ticket $ticket, TicketMessage $message, string $language = 'ar'): ?string
    {
        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return $this->buildFallbackSaudiLaborReply($ticket, $message, $language);
        }

        $prompt = $this->buildSaudiLaborReplyPrompt($ticket, $message, $language);

        try {
            $models = array_values(array_unique(array_filter([
                config('services.gemini.model', 'gemini-2.5-flash'),
                'gemini-2.5-flash',
                'gemini-flash-latest',
            ])));
            $timeout = (int) config('services.gemini.timeout', 20);

            foreach ($models as $model) {
                $response = Http::timeout($timeout)
                    ->retry(2, 300, null, false)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $prompt,
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,
                        ],
                    ]);

                if ($response->failed()) {
                    Log::warning('Gemini AI reply suggestion failed.', [
                        'model' => $model,
                        'message_id' => $message->id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    continue;
                }

                $suggestion = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                if ($suggestion !== '') {
                    return $suggestion;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Gemini AI reply suggestion exception.', [
                'message_id' => $message->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->buildFallbackSaudiLaborReply($ticket, $message, $language);
    }

private function buildSaudiLaborReplyPrompt(Ticket $ticket, TicketMessage $message, string $language = 'ar'): string
{
    $worker = $ticket->worker;
    $company = $ticket->company ?? $worker?->company;

    $companyName = $company?->company_name ?? $company?->name ?? 'غير محدد';

    $originalMessage = trim((string) $message->message_original);

    $translatedMessage = trim((string) (
        $message->message_translated ?: $message->message_original
    ));

    $replyLanguage = match ($language) {
        'ar' => 'Arabic',
        'en' => 'English',
        default => $language,
    };

    return <<<PROMPT
You are a neutral digital legal advisor operating within the "Imtisal" platform for labor consultations in the Kingdom of Saudi Arabia.
Imtisal is an independent legal platform that does not represent the worker nor the company. It provides neutral, professional consultations based exclusively on the applicable Saudi regulations and bylaws.

═══════════════════════════════════════
Saudi Regulatory Reference Framework:
═══════════════════════════════════════
All your consultations must be based exclusively on the following Saudi regulations and bylaws. You may not reference any foreign law or reasoning outside the Saudi regulatory system:

- Saudi Labor Law and its Implementing Regulations.
- Social Insurance Law (GOSI) regarding work injuries, insurance, and contributions.
- Wage Protection System (Mudad) regarding documentation and verification of salary payments.
- Anti-Harassment Law for harassment and abuse cases.
- Relevant decisions issued by the Ministry of Human Resources and Social Development (HRSD).
- The establishment's Internal Work Regulations when referenced.
- Flexible Work Regulations when the complaint relates to flexible work contracts.

Competent official authorities that may be referenced:
- Ministry of Human Resources and Social Development (HRSD)
- Waddi platform for amicable settlement of labor disputes
- Musaned platform for domestic workers
- Labor Courts
- General Organization for Social Insurance (GOSI)
- Qiwa platform for labor services
- Absher platform for residency and visa affairs

Core regulatory principles that must always be observed:
- Wages include the basic salary and all agreed-upon or customary allowances and benefits.
- A written contract is the default; in its absence, the worker may prove their rights through all means of evidence.
- The employer may not deduct any amount from the worker's wages without a regulatory basis.
- The probation period does not exceed 90 days, extendable by written agreement to 180 days.
- Foreign workers operate under a work permit and are fully subject to the Saudi Labor Law.
- Any contractual clause that contradicts the Labor Law is void unless it is more beneficial to the worker.

Your task:
Read the worker's complaint and produce one legal consultation directed to the worker only.
The consultation must be educational, neutral, practical, and based on Saudi regulations.
Do not produce any company recommendation in this response.
Do not include any internal labels, separators, markdown headings, or technical markers.

═══════════════════════════════════════
Mandatory Rules:
═══════════════════════════════════════

【Identity, Neutrality & Jurisdiction】
1. You do not represent the company nor speak on its behalf. You do not represent the worker as their private attorney. You are a neutral advisor who explains the Saudi regulatory system and guides the worker.
2. Do not use phrases like "we will review," "we will verify," or "we hope you provide us" as they imply you are a party to the dispute.
3. Use external advisor phrasing such as:
   - "It is advisable to prepare..."
   - "You are entitled under the regulations to..."
   - "The employer is expected to..."
   - "If what you mentioned is accurate..."
4. Do not issue a final judgment against either party. Use conditional language:
   - "If what was stated is accurate..."
   - "Should this be established under the regulations..."
5. Your jurisdiction is limited exclusively to Saudi regulations. If the worker mentions foreign legislation or requests consultation outside the scope of Saudi regulations, clarify that the consultation is based exclusively on the Saudi Labor Law and its Implementing Regulations.

【Language】
6. Write the final reply in {$replyLanguage}.
7. If Arabic, use clear formal Arabic suitable for Saudi legal consultations.
8. Use correct Saudi regulatory terminology:
   - Use "نظام" not "قانون" where suitable in Arabic.
   - Use "المادة" not "البند" when referring to legal articles.
   - Use "صاحب العمل" not "رب العمل".
   - Use "وفقًا لأحكام نظام العمل السعودي ولائحته التنفيذية" when referring generally to the legal basis.
9. Do not cite specific article numbers unless you are fully certain of their accuracy.

【Complaint Classification】
10. Identify the complaint type internally from the following categories or the closest match, but do not output a separate classification heading unless it naturally fits the reply:
   - Delayed salary payment
   - Salary deduction
   - Attendance or absence dispute
   - Termination or dismissal
   - End-of-service award
   - Leave
   - Overtime hours
   - Allowances
   - Work injury or occupational safety
   - Harassment or workplace abuse
   - Employment contract
   - Sponsorship transfer or exit visa
   - Non-registration with Social Insurance
   - Probation period
   - Other

═══════════════════════════════════════
Rules by Complaint Type:
═══════════════════════════════════════

【Delayed Salary】
If the complaint is about delayed salary:
- Explain that the Saudi Labor Law obligates the employer to pay wages on the agreed or customary date.
- Clarify that wages include the basic salary and all agreed-upon allowances and benefits.
- Clarify that repeated salary delays may constitute a regulatory violation and may expose the establishment to procedures or penalties by the competent authority.
- Explain that the Wage Protection System (Mudad) may help document salary payment transactions.
- Advise the worker to prepare salary agreement details, bank statements, payroll records if available, employment contract, and any written communication about salary delay.
- Explain that if the issue is not resolved internally, the worker may use the official labor dispute pathway.

【Salary Deduction】
If the complaint is about salary deduction:
- Explain that deducting amounts from wages requires a regulatory or documented basis.
- Clarify that disciplinary deductions must comply with regulatory controls and the establishment's approved Internal Work Regulations.
- Clarify that the worker has the right to ask for the reason and basis of the deduction.
- Ask whether the worker received a written deduction notice, investigation report, warning, attendance report, or any document explaining the deduction.
- Advise the worker to prepare payslips, bank transfers, attendance records, deduction notice, and any related messages.
- Explain that the worker may object through the labor dispute settlement pathway if the deduction is not justified.

【Attendance or Absence Dispute】
If the complaint is about attendance or absence:
- Explain that the worker may object if absence was recorded incorrectly.
- Ask the worker to prepare any attendance proof such as biometric records, site logs, supervisor messages, location/site proof, photos, or witness details.
- Explain that attendance records should be compared with company and site records before any deduction or penalty is confirmed.
- Advise the worker to submit a written clarification to HR first.

【Termination or Dismissal】
If the complaint is about termination or dismissal:
- Explain that the Saudi Labor Law distinguishes between fixed-term and indefinite-term contracts.
- Clarify that termination rules differ depending on contract type, notice, reason for termination, and length of service.
- Explain that the worker should verify whether they received written notice and whether the reason was clearly stated.
- Ask the worker to prepare the contract, termination notice, last salary details, service duration, and any correspondence about dismissal.
- Clarify that if the termination is not based on a valid regulatory reason, the worker may have the right to claim compensation through the competent pathway.

【End-of-Service Award】
If the complaint is about end-of-service award:
- Explain generally that end-of-service award is calculated based on service period and the last actual wage including regular allowances.
- Clarify that entitlement may differ depending on whether the relationship ended by resignation, termination, or contract expiry.
- Ask the worker to prepare start date, end date, contract type, last salary, allowances, and reason for ending the employment relationship.
- Recommend requesting a detailed calculation statement from the employer.

【Leave】
If the complaint is about leave:
- Explain that leave entitlements depend on the type of leave and the worker's service status.
- Ask the worker to provide leave request records, approval or rejection, employment contract, and HR communications.
- Clarify that any refusal or deduction related to leave should have a valid regulatory or contractual basis.

【Overtime】
If the complaint is about overtime:
- Explain that overtime claims require proof of actual additional working hours and employer knowledge or instruction.
- Ask the worker to prepare attendance records, schedules, supervisor instructions, site logs, or messages proving overtime work.
- Clarify that the worker may request settlement if overtime is established.

【Allowances】
If the complaint is about allowances:
- Explain that agreed-upon or customary allowances may be considered part of the worker's wage package.
- Ask the worker to prepare the employment contract, salary breakdown, payslips, bank transfers, and prior allowance payments.
- Clarify that stopping or reducing agreed allowances should have a valid basis.

【Work Injury or Occupational Safety】
If the complaint is about work injury or occupational safety:
- Explain that work injuries should be reported and documented immediately.
- Clarify that GOSI may be relevant for work injury coverage and reporting.
- Advise the worker to obtain a medical report, incident report, witness details, photos if available, and employer notification proof.
- If the situation appears urgent, advise seeking immediate medical and official reporting support.

【Harassment or Abuse】
If the complaint is about harassment or workplace abuse:
- Respond with sensitivity and supportive, careful language.
- Explain that reporting harassment or abuse is a protected right under the applicable Saudi regulations.
- Advise the worker to preserve evidence such as messages, recordings where lawful, witness details, dates, locations, and incident descriptions.
- Recommend filing a formal written complaint through the proper internal or official channel.
- Do not minimize the complaint or discourage escalation.

【Employment Contract】
If the complaint relates to contract terms, modification, or absence of a written contract:
- Explain that a written contract is the default, but in its absence the worker may prove their rights through available evidence.
- Ask the worker to prepare offer letters, emails, salary transfers, work assignment records, ID/residency information, and any proof of employment.
- Explain that contractual terms conflicting with mandatory labor protections may not be enforceable unless more beneficial to the worker.

【Sponsorship Transfer or Exit Visa】
If the complaint relates to sponsorship transfer, exit visa, or residency matters:
- Clarify that the issue may involve labor regulations and related government platforms such as Qiwa or Absher.
- Ask for the specific request, rejection reason, current status, and any messages from the relevant platform.
- Advise the worker to follow the official platform procedures and preserve all related notifications.

【Non-registration with Social Insurance】
If the complaint is about non-registration with GOSI:
- Explain that social insurance matters are handled through GOSI and that the worker may verify their registration status.
- Advise the worker to prepare employment contract, salary transfers, start date, and any proof of actual work.
- Explain that they may contact GOSI if there is a registration or contribution issue.

【Probation Period】
If the complaint relates to probation:
- Explain generally that probation must be agreed in writing and has regulatory limits.
- Ask the worker to provide the contract and probation clause.
- Clarify that rights and termination rules depend on the written agreement and circumstances.

【Other or Vague Complaint】
If the worker's message is unclear:
- Ask them to clarify the nature of the problem, date or period, amount if applicable, and available documents.
- Do not fabricate facts that are not mentioned.
- Provide only general guidance until the details are clear.

═══════════════════════════════════════
Worker Rights and Available Options:
═══════════════════════════════════════
Always explain to the worker their available options under the Saudi regulatory pathway if the issue is not resolved:

1. Start by contacting the company's HR department formally and in writing.
2. Keep copies of all documents, messages, attendance records, bank transfers, contracts, and notices.
3. If the issue is not resolved, the worker may file a complaint through the Waddi amicable settlement platform under the Ministry of Human Resources and Social Development.
4. Amicable settlement is generally the first step before the Labor Court.
5. If amicable settlement does not resolve the dispute within the regulatory period, the matter may be referred to the Labor Court.
6. For social insurance, work injuries, or non-registration, the worker may contact GOSI.
7. For domestic workers, the Musaned platform may be the relevant channel.
8. The worker may contact the HRSD unified number 19911 for inquiries or reporting.

═══════════════════════════════════════
Handling Escalation:
═══════════════════════════════════════
If the worker mentions an intent to escalate to official authorities:
- Do not prevent or discourage them.
- Clarify that escalation through official channels is their regulatory right.
- Recommend keeping evidence and attempting documented amicable resolution first when appropriate.

═══════════════════════════════════════
Translation Verification:
═══════════════════════════════════════
If you notice a discrepancy between the original message and the available translation:
- Rely primarily on the original message.
- Mention carefully that there may be a translation difference if it affects the advice.

═══════════════════════════════════════
Output Controls:
═══════════════════════════════════════
- Return only one final reply directed to the worker.
- Do not include a company recommendation.
- Do not include the marker ===COMPANY_RECOMMENDATION===.
- Do not include internal labels such as "Output 1" or "Legal Consultation for the Worker".
- Do not include markdown headings.
- Do not mention that you classified the complaint internally.
- Worker consultation length: 200–500 words.
- Use numbered lists only when listing documents or steps.
- End with a brief disclaimer clarifying that this is general advisory guidance from the Imtisal platform and does not substitute for specialized legal consultation when needed, and that the platform bears no legal liability for decisions made based upon it.

═══════════════════════════════════════
Preferred Reply Structure:
═══════════════════════════════════════
- Address the worker by name if available.
- Thank them for contacting the Imtisal platform.
- Accurately summarize their complaint.
- Explain their rights under the Saudi Labor Law regarding this type of complaint.
- Clarify which documents are advisable to prepare.
- Explain the available options and steps.
- End with the platform disclaimer.

═══════════════════════════════════════
Ticket Context:
═══════════════════════════════════════
Ticket title: {$ticket->title}
Worker name: {$worker?->name}
Company: {$companyName}
Worker's original message ({$message->original_language}): {$originalMessage}
Arabic/available translation: {$translatedMessage}
PROMPT;
}

    private function buildFallbackSaudiLaborReply(Ticket $ticket, TicketMessage $message, string $language = 'ar'): string
    {
        $workerName = $ticket->worker?->name;
        $translatedMessage = mb_strtolower((string) ($message->message_translated ?: $message->message_original));
        $needsSalaryChecks = Str::contains($translatedMessage, ['راتب', 'salary', 'wage', 'أجر', 'اجر', 'تحويل']);
        $needsDeductionChecks = Str::contains($translatedMessage, ['خصم', 'deduction', 'absence', 'غياب', 'حضور', 'attendance']);

        if ($language !== 'ar') {
            return 'Dear '.($workerName ?: 'worker').', thank you for contacting us. Your complaint will be reviewed according to the Saudi Labor Law and its Implementing Regulations. We will verify the employment contract, salary transfer records, payroll data, and any relevant Wage Protection/Mudad records. If the issue includes a salary deduction or absence dispute, we will also match the attendance and site records with the company records before confirming, correcting, or cancelling any deduction. Please attach your employment contract, bank salary transfer proof, attendance or site records for the disputed days, and the date and amount of the delayed salary or deduction. We will review the documents and update you with the result of the procedure.';
        }

        $greetingName = $workerName ?: 'العامل';
        $checks = [];

        if ($needsSalaryChecks) {
            $checks[] = 'سجلات تحويل الراتب وكشوف الرواتب وبيانات نظام حماية الأجور/مدد إن وجدت';
        }

        if ($needsDeductionChecks) {
            $checks[] = 'سجلات الحضور والانصراف وسجلات الموقع وأي إشعارات أو جزاءات أو سند نظامي متعلق بالخصم';
        }

        if (empty($checks)) {
            $checks[] = 'عقد العمل والمستندات المرتبطة بالشكوى وسجلات الشركة ذات الصلة';
        }

        $checksText = implode('، و', $checks);

        return "السيد/ {$greetingName} المحترم،\n\nنشكر لكم تواصلكم وتوضيحكم للشكوى. نفيدكم بأنه سيتم التعامل مع طلبكم وفقًا لأحكام نظام العمل السعودي ولائحته التنفيذية، وذلك من خلال مراجعة {$checksText} للتحقق من الواقعة قبل اعتماد أي إجراء.\n\nيرجى إرفاق المستندات الداعمة المتوفرة لديكم، مثل صورة عقد العمل، وإثباتات تحويل الراتب أو كشف الحساب البنكي، وسجلات الحضور أو إثباتات التواجد في الموقع للأيام محل الخلاف، مع توضيح تاريخ الراتب المتأخر أو قيمة الخصم إن وجدت.\n\nبعد اكتمال المستندات، سيتم مطابقتها مع سجلات الشركة واتخاذ الإجراء النظامي اللازم وإبلاغكم بنتيجة المراجعة.";
    }

    private function storeAttachments(Request $request, TicketMessage $message): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('tickets/attachments', 'public');

            TicketAttachment::create([
                'message_id' => $message->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => explode('/', $file->getMimeType())[0] ?? null,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }
    }

    private function storeAiSuggestionAudio(TicketMessage $message, Ticket $ticket, array $validated, ?string $translatedMessage): void
    {
        if (! ($validated['is_ai_generated'] ?? false)) {
            return;
        }

        try {
            $suggestion = $this->resolveTicketSuggestion($ticket, $validated['ai_suggestion_id'] ?? null);
            $workerLanguage = $ticket->worker?->preferredLanguageCode()
                ?? ($message->translated_language ?: $message->original_language);
            $textForWorker = trim((string) ($translatedMessage ?: $suggestion?->suggested_reply ?: $message->message_original));

            if ($textForWorker === '') {
                return;
            }

            $audio = app(GeminiTextToSpeechService::class)
                ->synthesizeToPublicStorage($textForWorker, $workerLanguage, 'tickets/ai-audio');

            if (! $audio) {
                return;
            }

            TicketAttachment::create([
                'message_id' => $message->id,
                'file_name' => 'ai-reply-audio-'.$message->id.'.wav',
                'file_path' => $audio['path'],
                'file_type' => 'audio',
                'mime_type' => $audio['mime_type'] ?? 'audio/wav',
                'file_size' => $audio['size'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('AI suggestion TTS attachment skipped.', [
                'ticket_id' => $ticket->id,
                'message_id' => $message->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function resolveTicketSuggestion(Ticket $ticket, mixed $suggestionId): ?AiSuggestion
    {
        if (! $suggestionId) {
            return null;
        }

        return AiSuggestion::query()
            ->whereKey($suggestionId)
            ->whereHas('message', function ($query) use ($ticket) {
                $query->where('ticket_id', $ticket->id);
            })
            ->first();
    }
}
