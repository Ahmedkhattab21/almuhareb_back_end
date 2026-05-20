<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\AiSuggestion;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
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
            ->with(['worker', 'company', 'latestMessage'])
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

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(10)->withQueryString();
        $companies = Company::assignedToLawyer($lawyerId)
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'email']);

        $stats = [
            'total' => Ticket::where('lawyer_id', $lawyerId)->count(),
            'open' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'in_progress')->count(),
            'closed' => Ticket::where('lawyer_id', $lawyerId)->where('status', 'closed')->count(),
        ];

        return view('lawyer.tickets.index', compact('tickets', 'stats', 'companies'));
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

        DB::transaction(function () use ($request, $ticket, $validated, $lawyer, $originalLanguage, $translatedLanguage, $translatedMessage) {
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
        });

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

        return back()->with('toast_success', __('tickets.messages.status_updated'));
    }

    public function close(Ticket $ticket)
    {
        $this->authorizeLawyerTicket($ticket);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

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
        $company = $ticket->company;
        $companyName = $company?->company_name ?? $company?->name;
        $originalMessage = trim((string) $message->message_original);
        $translatedMessage = trim((string) ($message->message_translated ?: $message->message_original));

        return <<<PROMPT
You are an assistant specialized in drafting official replies to worker complaints inside the Kingdom of Saudi Arabia.

Your reply must be a practical first-step solution, not a generic acknowledgement. It must be based on Saudi Labor Law and its Implementing Regulations in general terms.

Mandatory rules:
1. Read the worker message and identify the core labor issue, such as delayed salary, salary deduction, absence/attendance dispute, contract issue, financial dues, administrative penalty, or another labor dispute.
2. Draft the reply in {$language}. If {$language} is Arabic, use clear formal Arabic suitable for official Saudi workplace correspondence.
3. Do not write a vague reply like "we will review your complaint" only. The reply must explain what will be checked, what documents are needed, what procedure will happen, and the next step.
4. If the complaint mentions delayed salary, state that salary payment must be verified against the agreed or usual payment date, bank transfers, payroll records, and the Wage Protection/Mudad records where applicable.
5. If the complaint mentions salary deduction, state that deductions must have a documented lawful basis, and the company must verify attendance records, site records, notices, penalties, approvals, or any legal basis before confirming the deduction.
6. If the worker says they were present or have attendance proof, ask them to attach attendance/site records and state that these will be matched against company and site records before approving, correcting, or cancelling the deduction.
7. Be professional, neutral, reassuring, legally careful, and solution-oriented.
8. Do not admit company liability before verification. Do not reject the complaint without review. Do not promise a specific final outcome.
9. Do not cite article numbers unless you are fully certain. Prefer the phrase "وفقًا لأحكام نظام العمل السعودي ولائحته التنفيذية" or its equivalent in the requested language.
10. End with clear requested documents or a clear next action.
11. Return only the final reply text. Do not include explanations, labels, markdown, or bullet points unless they improve the official reply.

Preferred structure:
- Address the worker by name if available.
- Thank them for contacting.
- Summarize their complaint accurately.
- Explain the Saudi labor procedure/checks that will happen.
- Request the necessary documents.
- Close with a clear follow-up step.

Ticket context:
Ticket title: {$ticket->title}
Worker name: {$worker?->name}
Company: {$companyName}
Worker original message ({$message->original_language}): {$originalMessage}
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
            return 'Dear ' . ($workerName ?: 'worker') . ', thank you for contacting us. Your complaint will be reviewed according to the Saudi Labor Law and its Implementing Regulations. We will verify the employment contract, salary transfer records, payroll data, and any relevant Wage Protection/Mudad records. If the issue includes a salary deduction or absence dispute, we will also match the attendance and site records with the company records before confirming, correcting, or cancelling any deduction. Please attach your employment contract, bank salary transfer proof, attendance or site records for the disputed days, and the date and amount of the delayed salary or deduction. We will review the documents and update you with the result of the procedure.';
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
}
