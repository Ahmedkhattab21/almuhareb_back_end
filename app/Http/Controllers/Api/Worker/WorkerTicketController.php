<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\TicketRating;
use App\Services\SystemNotifier;
use App\Services\WorkerLocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkerTicketController extends Controller
{
    public function index(Request $request, WorkerLocalizationService $localization)
    {
        $worker = $request->user();

        $query = Ticket::query()
            ->with([
                'company:id,company_name,email,phone',
                'lawyer:id,name,email,phone',
                'category:id,name',
                'latestMessage',
                'rating',
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
        $tickets->getCollection()->transform(function (Ticket $ticket) {
            $rate = $ticket->rating;

            $ticket->setAttribute('rate', $rate ? [
                'id' => $rate->id,
                'rating' => $rate->rating,
                'message' => $rate->message,
                'created_at' => $rate->created_at?->toISOString(),
                'updated_at' => $rate->updated_at?->toISOString(),
            ] : null);

            unset($ticket->rating);

            return $ticket;
        });

        return response()->json([
            'status' => true,
            'message' => $localization->api('tickets_fetched', [], $worker, $request),
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

    public function store(Request $request, WorkerLocalizationService $localization)
    {
        $worker = $request->user();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],

            'message_original' => ['nullable', 'string', 'required_without:attachments'],
            'message_translated' => ['nullable', 'string'],

            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],

            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('status', Category::STATUS_ACTIVE)),
            ],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'long' => ['nullable', 'numeric', 'between:-180,180'],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:20480'],
        ]);

        $companyId = $worker->company_id ?? null;

        $lawyerId = $this->assignedLawyerForCategory($companyId, (int) $validated['category_id']);

        if (! $lawyerId) {
            return response()->json([
                'status' => false,
                'message' => $localization->api('ticket_category_unassigned', [], $worker, $request),
            ], 422);
        }

        $originalLanguage = $worker->preferredLanguageCode() ?? ($validated['original_language'] ?? null);
        $audioTranscript = $this->extractAudioTranscriptsFromRequest($request);
       $messageText = $this->appendAudioTranscript($validated['message_original'] ?? '', $audioTranscript);
        $translatedMessage = $this->translateToArabic(
            $messageText,
            $validated['message_translated'] ?? null
        );
        $titleOriginal = $validated['title'] ?? Str::limit($messageText, 80);
        $translatedTitle = $this->translateToArabic($titleOriginal);

        $ticket = DB::transaction(function () use ($request, $worker, $validated, $companyId, $lawyerId, $translatedMessage, $originalLanguage, $titleOriginal, $translatedTitle, $messageText) {
            $ticket = Ticket::create([
                'worker_id' => $worker->id,
                'company_id' => $companyId,
                'lawyer_id' => $lawyerId,
                'category_id' => $validated['category_id'],
                'lat' => $validated['lat'] ?? null,
                'long' => $validated['long'] ?? null,

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

            $this->storeAttachments($request, $message);

            return $ticket;
        });

        $ticket->load([
            'worker:id,name,email,phone',
            'company:id,company_name,email,phone',
            'lawyer:id,name,email,phone',
            'category:id,name',
            'messages.attachments',
        ]);

        SystemNotifier::notifyTicketChange(
            ticket: $ticket,
            type: 'ticket_created',
            title: 'تم إنشاء استشارة جديدة',
            body: "تم إنشاء استشارة جديدة رقم {$ticket->id} بواسطة العامل {$worker->name}.",
            actor: $worker,
            data: ['ticket_id' => $ticket->id, 'action' => 'created']
        );

        return response()->json([
            'status' => true,
            'message' => $localization->api('ticket_created', [], $worker, $request),
            'data' => [
                'ticket' => $ticket,
            ],
        ], 201);
    }

    public function show(Request $request, Ticket $ticket, WorkerLocalizationService $localization)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        $ticket->load([
            'worker:id,name,email,phone',
            'company:id,company_name,email,phone',
            'lawyer:id,name,email,phone',
            'category:id,name',
            'rating',
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
            'message' => $localization->api('ticket_details_fetched', [], $request->user(), $request),
            'data' => [
                'ticket' => $ticket,
                'messages' => $messages,
            ],
        ]);
    }

    public function reply(Request $request, Ticket $ticket, WorkerLocalizationService $localization)
    {
        $this->authorizeWorkerTicket($request, $ticket);
        abort_if($ticket->status === 'closed', 422, 'لا يمكن الرد على استشارة مغلقة.');

        $worker = $request->user();

        $validated = $request->validate([
            'message_original' => ['nullable', 'string', 'required_without:attachments'],
            'message_translated' => ['nullable', 'string'],

            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:20480'],
        ]);

        $originalLanguage = $worker->preferredLanguageCode() ?? ($validated['original_language'] ?? null);
        $audioTranscript = $this->extractAudioTranscriptsFromRequest($request);
       $messageText = $this->appendAudioTranscript($validated['message_original'] ?? '', $audioTranscript);
        $translatedMessage = $this->translateToArabic(
            $messageText,
            $validated['message_translated'] ?? null
        );

        $message = DB::transaction(function () use ($request, $ticket, $worker, $validated, $translatedMessage, $originalLanguage, $messageText) {
            $lastOrder = TicketMessage::where('ticket_id', $ticket->id)
                ->lockForUpdate()
                ->max('message_order');

            $messageOrder = ($lastOrder ?? 0) + 1;

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,

                'sender_type' => 'worker',
                'sender_id' => $worker->id,

                'message_order' => $messageOrder,

                'message_original' => $messageText,
                'message_translated' => $translatedMessage,

                'original_language' => $originalLanguage,
                'translated_language' => $translatedMessage ? 'ar' : ($validated['translated_language'] ?? null),

                'is_ai_generated' => false,
            ]);

            $this->storeAttachments($request, $message);

            $ticket->update([
                'status' => 'pending',
                'closed_at' => null,
                'last_message_preview' => Str::limit($messageText, 120),
                'last_message_at' => now(),
            ]);

            return $message;
        });

        $message->load('attachments');

        SystemNotifier::notifyTicketChange(
            ticket: $ticket->fresh(['worker', 'company', 'lawyer']),
            type: 'ticket_message_created',
            title: 'تم إضافة رد من العامل',
            body: "تم إضافة رد جديد على الاستشارة رقم {$ticket->id}.",
            actor: $worker,
            data: ['ticket_id' => $ticket->id, 'sender_type' => 'worker']
        );

        return response()->json([
            'status' => true,
            'message' => $localization->api('ticket_reply_sent', [], $worker, $request),
            'data' => [
                'message' => $message,
            ],
        ], 201);
    }

    public function close(Request $request, Ticket $ticket, WorkerLocalizationService $localization)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        return response()->json([
            'status' => false,
            'message' => $localization->api('ticket_close_forbidden', [], $request->user(), $request),
        ], 403);
    }

    public function reopen(Request $request, Ticket $ticket, WorkerLocalizationService $localization)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        if ($ticket->status !== 'closed') {
            return response()->json([
                'status' => false,
                'message' => $localization->api('ticket_not_closed', [], $request->user(), $request),
            ], 422);
        }

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'last_message_at' => now(),
        ]);

        SystemNotifier::notifyTicketChange(
            ticket: $ticket->fresh(['worker', 'company', 'lawyer']),
            type: 'ticket_reopened',
            title: 'تمت إعادة فتح استشارة',
            body: "تمت إعادة فتح الاستشارة رقم {$ticket->id} بواسطة العامل.",
            actor: $request->user(),
            data: ['ticket_id' => $ticket->id, 'action' => 'reopened']
        );

        return response()->json([
            'status' => true,
            'message' => $localization->api('ticket_reopened', [], $request->user(), $request),
            'data' => [
                'ticket' => $ticket->fresh([
                    'company:id,company_name,email,phone',
                    'lawyer:id,name,email,phone',
                    'category:id,name',
                    'rating',
                ]),
            ],
        ]);
    }

    public function rate(Request $request, Ticket $ticket, WorkerLocalizationService $localization)
    {
        $this->authorizeWorkerTicket($request, $ticket);

        $worker = $request->user();

        if ($ticket->status !== 'closed') {
            return response()->json([
                'status' => false,
                'message' => $localization->api('ticket_rating_requires_closed', [], $worker, $request),
            ], 422);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $rating = DB::transaction(function () use ($ticket, $worker, $validated) {
            $rating = TicketRating::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'worker_id' => $worker->id,
                    'company_id' => $ticket->company_id,
                    'lawyer_id' => $ticket->lawyer_id,
                    'rating' => $validated['rating'],
                    'message' => $validated['message'] ?? null,
                ]
            );

            $this->refreshLawyerRating($ticket->lawyer_id);

            return $rating;
        });

        SystemNotifier::notifyTicketRating($rating->fresh(['ticket.worker', 'ticket.lawyer', 'worker', 'lawyer']), $worker);

        return response()->json([
            'status' => true,
            'message' => $localization->api('ticket_rated', [], $worker, $request),
            'data' => [
                'rating' => $rating->fresh(['ticket:id,title,status', 'lawyer:id,name,rating']),
            ],
        ], 201);
    }

    private function assignedLawyerForCategory(?int $companyId, int $categoryId): ?int
    {
        if (! $companyId) {
            return null;
        }

        if (! DB::getSchemaBuilder()->hasTable('lawyers_categories')) {
            return null;
        }

        return DB::table('lawyers_categories')
            ->join('lawyers', 'lawyers.id', '=', 'lawyers_categories.lawyer_id')
            ->where('lawyers_categories.company_id', $companyId)
            ->where('lawyers_categories.category_id', $categoryId)
            ->where('lawyers.status', 'active')
            ->orderBy('lawyers.id')
            ->value('lawyers.id');
    }

    private function authorizeWorkerTicket(Request $request, Ticket $ticket): void
    {
        $worker = $request->user();

        abort_if((int) $ticket->worker_id !== (int) $worker->id, 403, 'غير مصرح لك بالوصول لهذه الاستشارة.');
    }

    private function refreshLawyerRating(?int $lawyerId): void
    {
        if (! $lawyerId) {
            return;
        }

        $average = TicketRating::query()
            ->where('lawyer_id', $lawyerId)
            ->avg('rating');

        DB::table('lawyers')
            ->where('id', $lawyerId)
            ->update([
                'rating' => round((float) $average, 1),
                'updated_at' => now(),
            ]);
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

    private function storeAttachments(Request $request, TicketMessage $message): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (! $file) {
                continue;
            }

            $mimeType = (string) $file->getMimeType();
            $fileType = explode('/', $mimeType)[0] ?? null;

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
    }

    private function appendAudioTranscript(?string $message, string $audioTranscript): string
    {
        $message = trim((string) $message);
        $audioTranscript = trim($audioTranscript);

        if ($audioTranscript === '') {
            return $message;
        }

        return trim($message."\n\nنص المقطع الصوتي:\n".$audioTranscript);
    }

private function extractAudioTranscriptsFromRequest(Request $request): string
{
    if (! $request->hasFile('attachments')) {
        Log::info('No attachments found in request for audio transcription.');

        return '';
    }

    $audioTranscripts = [];

    foreach ($request->file('attachments') as $file) {
        if (! $file) {
            continue;
        }

        $originalName = $file->getClientOriginalName();
        $clientMime = (string) $file->getClientMimeType();
        $serverMime = (string) $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        Log::info('Checking attachment for audio transcription.', [
            'original_name' => $originalName,
            'client_mime' => $clientMime,
            'server_mime' => $serverMime,
            'extension' => $extension,
            'size' => $file->getSize(),
            'real_path' => $file->getRealPath(),
        ]);

        if (! $this->isAudioFile($file)) {
            Log::warning('Attachment skipped because it is not detected as audio.', [
                'original_name' => $originalName,
                'client_mime' => $clientMime,
                'server_mime' => $serverMime,
                'extension' => $extension,
            ]);

            continue;
        }

        $geminiMimeType = $this->resolveGeminiAudioMimeType($file);

        Log::info('Audio attachment will be sent to Gemini.', [
            'original_name' => $originalName,
            'gemini_mime_type' => $geminiMimeType,
        ]);

        $transcript = $this->transcribeAudio(
            $file->getRealPath(),
            $geminiMimeType
        );

        Log::info('Gemini audio transcript result.', [
            'transcript' => $transcript,
        ]);

        if ($transcript !== null && trim($transcript) !== '') {
            $audioTranscripts[] = trim($transcript);
        }
    }

    return trim(implode("\n\n", $audioTranscripts));
}

private function isAudioFile($file): bool
{
    $clientMime = strtolower((string) $file->getClientMimeType());
    $serverMime = strtolower((string) $file->getMimeType());
    $extension = strtolower($file->getClientOriginalExtension());

    $audioExtensions = [
        'wav',
        'mp3',
        'm4a',
        'mp4',
        'aac',
        'ogg',
        'webm',
        'flac',
    ];

    if (str_starts_with($clientMime, 'audio/')) {
        return true;
    }

    if (str_starts_with($serverMime, 'audio/')) {
        return true;
    }

    if (in_array($extension, $audioExtensions, true)) {
        return true;
    }

    return false;
}

private function resolveGeminiAudioMimeType($file): string
{
    $extension = strtolower($file->getClientOriginalExtension());
    $clientMime = strtolower((string) $file->getClientMimeType());
    $serverMime = strtolower((string) $file->getMimeType());

    return match ($extension) {
        'wav' => 'audio/wav',
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'mp4' => 'audio/mp4',
        'aac' => 'audio/aac',
        'ogg' => 'audio/ogg',
        'webm' => 'audio/webm',
        'flac' => 'audio/flac',
        default => str_starts_with($clientMime, 'audio/')
            ? $clientMime
            : (str_starts_with($serverMime, 'audio/') ? $serverMime : 'audio/wav'),
    };
}

private function transcribeAudio(string $filePath, string $mimeType): ?string
{
    if (! is_file($filePath)) {
        Log::warning('Audio file path is not valid.', [
            'file_path' => $filePath,
        ]);

        return null;
    }

    $apiKey = config('services.gemini.api_key');

    if (! $apiKey) {
        Log::warning('Gemini API key is missing.');

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

        // WAV محتاج وقت أطول من النص العادي
        $timeout = (int) config('services.gemini.timeout', 120);

        foreach ($models as $model) {
            Log::info('Sending audio to Gemini for transcription.', [
                'model' => $model,
                'mime_type' => $mimeType,
                'file_size' => filesize($filePath),
            ]);

            $response = Http::timeout($timeout)
                ->retry(2, 500, null, false)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Transcribe this audio in the original spoken language. Preserve names, numbers, dates, and labor/legal terms. Return only the transcript text without explanations or translation.',
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

            Log::info('Gemini ticket audio transcription response.', [
                'model' => $model,
                'transcript' => $transcript,
            ]);

            if ($transcript !== '') {
                return $transcript;
            }
        }
    } catch (\Throwable $exception) {
        Log::warning('Gemini ticket audio transcription exception.', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    return null;
}
}
