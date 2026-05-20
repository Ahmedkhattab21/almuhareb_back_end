<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::query()
            ->with([
                'worker',
                'company',
                'lawyer',
                'latestMessage',
            ])
            ->latest('last_message_at')
            ->latest('id');

        if (Auth::guard('company')->check()) {
            $query->where('company_id', Auth::guard('company')->id());
        }

        if (Auth::guard('lawyer')->check()) {
            $query->where('lawyer_id', Auth::guard('lawyer')->id());
        }

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
                    ->orWhere('last_message_preview', 'like', "%{$search}%")
                    ->orWhereHas('worker', function ($workerQuery) use ($search) {
                        $workerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('iqama_number', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeTicketAccess($ticket);

        $ticket->load([
            'worker',
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

        return view('tickets.show', compact('ticket', 'messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'worker_id' => ['required', 'integer', Rule::exists('workers', 'id')],
            'title' => ['nullable', 'string', 'max:255'],
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],
            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high', 'urgent'])],
        ]);

        $worker = Worker::findOrFail($validated['worker_id']);

        $companyId = Auth::guard('company')->check()
            ? Auth::guard('company')->id()
            : ($worker->company_id ?? null);

        $company = $companyId ? Company::find($companyId) : null;

        $lawyerId = Auth::guard('lawyer')->check()
            ? Auth::guard('lawyer')->id()
            : ($company->lawyer_id ?? null);

        [$senderType, $senderId] = $this->currentSender($worker->id);

        $ticket = DB::transaction(function () use ($validated, $worker, $companyId, $lawyerId, $senderType, $senderId) {
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

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => $senderType,
                'sender_id' => $senderId,
                'message_order' => 1,
                'message_original' => $messageText,
                'message_translated' => $validated['message_translated'] ?? null,
                'original_language' => $validated['original_language'] ?? null,
                'translated_language' => $validated['translated_language'] ?? null,
                'is_ai_generated' => false,
            ]);

            return $ticket;
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('toast_success', __('tickets.messages.created'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeTicketAccess($ticket);
        abort_if($ticket->status === 'closed', 422, 'لا يمكن الرد على تذكرة مغلقة.');

        $validated = $request->validate([
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],
            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],
            'is_ai_generated' => ['nullable', 'boolean'],
        ]);

        [$senderType, $senderId] = $this->currentSender();

        DB::transaction(function () use ($ticket, $validated, $senderType, $senderId) {
            $lastOrder = TicketMessage::where('ticket_id', $ticket->id)
                ->lockForUpdate()
                ->max('message_order');

            $messageOrder = ($lastOrder ?? 0) + 1;

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => $senderType,
                'sender_id' => $senderId,
                'message_order' => $messageOrder,
                'message_original' => $validated['message_original'],
                'message_translated' => $validated['message_translated'] ?? null,
                'original_language' => $validated['original_language'] ?? null,
                'translated_language' => $validated['translated_language'] ?? null,
                'is_ai_generated' => $validated['is_ai_generated'] ?? false,
            ]);

            $ticket->update([
                'status' => $this->statusAfterReply($senderType),
                'closed_at' => null,
                'last_message_preview' => Str::limit($validated['message_original'], 120),
                'last_message_at' => now(),
            ]);
        });

        return back()
            ->with('toast_success', __('tickets.messages.reply_sent'));
    }

    public function close(Ticket $ticket)
    {
        $this->authorizeTicketAccess($ticket);
        abort_if(! Auth::guard('lawyer')->check(), 403, 'إغلاق التذكرة متاح للمحامي فقط.');
        abort_if((int) $ticket->lawyer_id !== (int) Auth::guard('lawyer')->id(), 403);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()
            ->with('toast_success', __('tickets.messages.closed'));
    }

    private function currentSender(?int $fallbackWorkerId = null): array
    {
        if (Auth::guard('admin')->check()) {
            return ['admin', Auth::guard('admin')->id()];
        }

        if (Auth::guard('company')->check()) {
            return ['company', Auth::guard('company')->id()];
        }

        if (Auth::guard('lawyer')->check()) {
            return ['lawyer', Auth::guard('lawyer')->id()];
        }

        if ($fallbackWorkerId) {
            return ['worker', $fallbackWorkerId];
        }

        abort(403);
    }

    private function statusAfterReply(string $senderType): string
    {
        return $senderType === 'worker' ? 'pending' : 'in_progress';
    }

    private function authorizeTicketAccess(Ticket $ticket): void
    {
        if (Auth::guard('admin')->check()) {
            return;
        }

        if (Auth::guard('company')->check()) {
            abort_if((int) $ticket->company_id !== (int) Auth::guard('company')->id(), 403);
            return;
        }

        if (Auth::guard('lawyer')->check()) {
            abort_if((int) $ticket->lawyer_id !== (int) Auth::guard('lawyer')->id(), 403);
            return;
        }

        abort(403);
    }
}
