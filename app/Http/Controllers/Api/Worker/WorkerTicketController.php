<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkerTicketController extends Controller
{
    public function index(Request $request)
    {
        $worker = $request->user();

        $query = Ticket::query()
            ->with([
                'company:id,company_name,name,email,phone',
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

        $ticket = DB::transaction(function () use ($request, $worker, $validated, $companyId, $lawyerId) {
            $messageText = $validated['message_original'];

            $ticket = Ticket::create([
                'worker_id' => $worker->id,
                'company_id' => $companyId,
                'lawyer_id' => $lawyerId,

                'title' => $validated['title'] ?? Str::limit($messageText, 80),

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
                'message_translated' => $validated['message_translated'] ?? null,

                'original_language' => $validated['original_language'] ?? null,
                'translated_language' => $validated['translated_language'] ?? null,

                'is_ai_generated' => false,
            ]);

            $this->storeAttachments($request, $message);

            return $ticket;
        });

        $ticket->load([
            'worker:id,name,email,phone',
            'company:id,company_name,name,email,phone',
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
            'company:id,company_name,name,email,phone',
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

        $worker = $request->user();

        $validated = $request->validate([
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],

            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],

            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        $message = DB::transaction(function () use ($request, $ticket, $worker, $validated) {
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
                'message_translated' => $validated['message_translated'] ?? null,

                'original_language' => $validated['original_language'] ?? null,
                'translated_language' => $validated['translated_language'] ?? null,

                'is_ai_generated' => false,
            ]);

            $this->storeAttachments($request, $message);

            $ticket->update([
                'status' => $ticket->status === 'closed' ? 'open' : $ticket->status,
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

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إغلاق التذكرة بنجاح.',
            'data' => [
                'ticket' => $ticket,
            ],
        ]);
    }

    private function authorizeWorkerTicket(Request $request, Ticket $ticket): void
    {
        $worker = $request->user();

        abort_if((int) $ticket->worker_id !== (int) $worker->id, 403, 'غير مصرح لك بالوصول لهذه التذكرة.');
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
