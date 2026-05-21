<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkerTicketController extends Controller
{
    public function index(Request $request)
    {
        $worker = $request->user();

        $query = Ticket::query()
            ->with([
                'company:id,company_name,email,phone',
                'lawyer:id,name,email,phone',
                'latestMessage',
            ])
            ->where('worker_id', $worker->id)
            ->latest('last_message_at')
            ->latest('id');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('last_message_preview', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'status' => true,
            'message' => 'تم جلب التذاكر بنجاح.',
            'data' => [
                'tickets' => $tickets->items(),
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $worker = $request->user();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],

            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],

            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],

            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $companyId = $worker->company_id ?? null;

        $company = $companyId ? Company::find($companyId) : null;

        $lawyerId = $company->lawyer_id ?? null;

        $translatedMessage = $this->translateToArabic(
            $validated['message_original'],
            $validated['message_translated'] ?? null
        );

        $originalLanguage = $worker->preferredLanguageCode() ?? ($validated['original_language'] ?? null);
        $titleOriginal = $validated['title'] ?? Str::limit($validated['message_original'], 80);
        $translatedTitle = $this->translateToArabic($titleOriginal);

        $ticket = DB::transaction(function () use ($request, $worker, $validated, $companyId, $lawyerId, $translatedMessage, $originalLanguage, $titleOriginal, $translatedTitle) {
            $messageText = $validated['message_original'];

            $ticket = Ticket::create([
                'worker_id' => $worker->id,
                'company_id' => $companyId,
                'lawyer_id' => $lawyerId,

                'title' => $titleOriginal,
                'title_original' => $titleOriginal,
                'title_translated' => $translatedTitle,
                'title_original_language' => $originalLanguage,
                'title_translated_language' => $translatedTitle ? 'ar' : null,

                'status' => 'open',
                'priority' => $validated['priority'] ?? 'medium',

                'last_message_preview' => Str::limit($messageText, 120),
                'last_message_at' => now(),
            ]);

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,

                'sender_type' => 'worker',
                'sender_id' => $worker->id,

                'message_order' => 1,

                'message_original' => $messageText,
                'message_translated' => $translatedMessage,

                'original_language' => $originalLanguage,
                'translated_language' => $translatedMessage ? 'ar' : ($validated['translated_language'] ?? null),

                'is_ai_generated' => false,
            ]);

            $audioTranscript = $this->storeAttachments($request, $message);

            if ($audioTranscript !== '') {
                $message->update([
                    'message_translated' => $this->appendAudioTranscript(
                        $message->message_translated,
                        $audioTranscript
                    ),
                    'translated_language' => 'ar',
                ]);
            }

            return $ticket;
        });

        $ticket->load([
            'worker:id,name,email,phone',
            'company:id,company_name,email,phone',
            'lawyer:id,name,email,phone',
            'messages.attachments',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء التذكرة بنجاح.',
            'data' => [
                'ticket' => $ticket,
            ],
        ], 201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        $ticket->load([
            'worker:id,name,email,phone',
            'company:id,company_name,email,phone',
            'lawyer:id,name,email,phone',
        ]);

        $messages = $ticket->messages()
            ->with([
                'attachments',
                'aiSuggestions',
            ])
            ->orderBy('message_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب تفاصيل التذكرة بنجاح.',
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages,
            ],
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeWorkerTicket($request, $ticket);
        abort_if($ticket->status === 'closed', 422, 'لا يمكن الرد على تذكرة مغلقة.');

        $worker = $request->user();

        $validated = $request->validate([
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],

            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $translatedMessage = $this->translateToArabic(
            $validated['message_original'],
            $validated['message_translated'] ?? null
        );

        $originalLanguage = $worker->preferredLanguageCode() ?? ($validated['original_language'] ?? null);

        $message = DB::transaction(function () use ($request, $ticket, $worker, $validated, $translatedMessage, $originalLanguage) {
            $lastOrder = TicketMessage::where('ticket_id', $ticket->id)
                ->lockForUpdate()
                ->max('message_order');

            $messageOrder = ($lastOrder ?? 0) + 1;

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,

                'sender_type' => 'worker',
                'sender_id' => $worker->id,

                'message_order' => $messageOrder,

                'message_original' => $validated['message_original'],
                'message_translated' => $translatedMessage,

                'original_language' => $originalLanguage,
                'translated_language' => $translatedMessage ? 'ar' : ($validated['translated_language'] ?? null),

                'is_ai_generated' => false,
            ]);

            $audioTranscript = $this->storeAttachments($request, $message);

            if ($audioTranscript !== '') {
                $message->update([
                    'message_translated' => $this->appendAudioTranscript(
                        $message->message_translated,
                        $audioTranscript
                    ),
                    'translated_language' => 'ar',
                ]);
            }

            $ticket->update([
                'status' => 'pending',
                'closed_at' => null,
                'last_message_preview' => Str::limit($validated['message_original'], 120),
                'last_message_at' => now(),
            ]);

            return $message;
        });

        $message->load('attachments');

        return response()->json([
            'status' => true,
            'message' => 'تم إرسال الرد بنجاح.',
            'data' => [
                'message' => $message,
            ],
        ], 201);
    }

    public function close(Request $request, Ticket $ticket)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        abort(403, 'إغلاق التذكرة متاح للمحامي فقط.');
    }

    public function reopen(Request $request, Ticket $ticket)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        if ($ticket->status !== 'closed') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إعادة فتح التذكرة لأنها ليست مغلقة.',
            ], 422);
        }

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تمت إعادة فتح التذكرة بنجاح.',
            'data' => [
                'ticket' => $ticket->fresh([
                    'company:id,company_name,email,phone',
                    'lawyer:id,name,email,phone',
                ]),
            ],
        ]);
    }

    private function authorizeWorkerTicket(Request $request, Ticket $ticket): void
    {
        $worker = $request->user();

        abort_if((int) $ticket->worker_id !== (int) $worker->id, 403, 'غير مصرح لك بالوصول لهذه التذكرة.');
    }

    private function translateToArabic(string $text, ?string $fallback = null): ?string
    {
        $text = trim($text);

        if ($text === '') {
            return $fallback;
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
                'gemini-2.0-flash',
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
                                        'text' => "Translate the following worker message to Arabic. Preserve meaning, names, numbers, dates, and legal or employment terms. Return only the Arabic translation without explanations.\n\nMessage:\n{$text}",
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                        ],
                    ]);

                if ($response->failed()) {
                    Log::warning('Gemini ticket message translation failed.', [
                        'model' => $model,
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
            Log::warning('Gemini ticket message translation exception.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return $fallback;
    }

    private function storeAttachments(Request $request, TicketMessage $message): string
    {
        if (! $request->hasFile('attachments')) {
            return '';
        }

        $audioTranscripts = [];

        foreach ($request->file('attachments') as $file) {
            if (! $file) {
                continue;
            }

            $mimeType = (string) $file->getMimeType();
            $fileType = explode('/', $mimeType)[0] ?? null;

            if ($fileType === 'audio') {
                $transcript = $this->transcribeAudioToArabic($file->getRealPath(), $mimeType);

                if ($transcript !== null && $transcript !== '') {
                    $audioTranscripts[] = $transcript;
                }
            }

            $path = $file->store('tickets/attachments', 'public');

            TicketAttachment::create([
                'message_id' => $message->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $fileType,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return trim(implode("\n\n", $audioTranscripts));
    }

    private function appendAudioTranscript(?string $message, string $audioTranscript): string
    {
        $message = trim((string) $message);
        $audioTranscript = trim($audioTranscript);

        return trim($message . "\n\nنص المقطع الصوتي:\n" . $audioTranscript);
    }

    private function transcribeAudioToArabic(string $filePath, string $mimeType): ?string
    {
        if (! is_file($filePath)) {
            return null;
        }

        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return null;
        }

        try {
            $audioData = base64_encode((string) file_get_contents($filePath));
            $models = array_values(array_unique(array_filter([
                config('services.gemini.model', 'gemini-2.5-flash'),
                'gemini-2.5-flash',
                'gemini-flash-latest',
                'gemini-2.0-flash',
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
                                        'text' => 'استخرج نص هذا المقطع الصوتي وترجمه إلى العربية. حافظ على الأسماء والأرقام والتواريخ والمصطلحات العمالية والقانونية. أعد النص العربي فقط بدون شرح.',
                                    ],
                                    [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data' => $audioData,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                        ],
                    ]);

                if ($response->failed()) {
                    Log::warning('Gemini ticket audio transcription failed.', [
                        'model' => $model,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    continue;
                }

                $transcript = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                if ($transcript !== '') {
                    return $transcript;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Gemini ticket audio transcription exception.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return null;
    }
}
