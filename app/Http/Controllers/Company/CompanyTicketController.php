<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyTicketController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::guard('company')->id();

        $query = Ticket::query()
            ->with(['worker', 'lawyer', 'latestMessage'])
            ->where('company_id', $companyId)
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
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Ticket::where('company_id', $companyId)->count(),
            'open' => Ticket::where('company_id', $companyId)->where('status', 'open')->count(),
            'in_progress' => Ticket::where('company_id', $companyId)->where('status', 'in_progress')->count(),
            'closed' => Ticket::where('company_id', $companyId)->where('status', 'closed')->count(),
        ];

        return view('company.tickets.index', compact('tickets', 'stats'));
    }

    public function show(Ticket $ticket)
    {
        $this->authorizeCompanyTicket($ticket);

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

        return view('company.tickets.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $this->authorizeCompanyTicket($ticket);
        abort_if($ticket->status === 'closed', 422, 'لا يمكن الرد على تذكرة مغلقة.');

        $validated = $request->validate([
            'message_original' => ['required', 'string'],
            'message_translated' => ['nullable', 'string'],
            'original_language' => ['nullable', 'string', 'max:10'],
            'translated_language' => ['nullable', 'string', 'max:10'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $ticket, $validated) {
            $lastOrder = TicketMessage::where('ticket_id', $ticket->id)
                ->lockForUpdate()
                ->max('message_order');

            $message = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'company',
                'sender_id' => Auth::guard('company')->id(),
                'message_order' => ($lastOrder ?? 0) + 1,
                'message_original' => $validated['message_original'],
                'message_translated' => $validated['message_translated'] ?? null,
                'original_language' => $validated['original_language'] ?? 'ar',
                'translated_language' => $validated['translated_language'] ?? null,
                'is_ai_generated' => false,
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

    public function close(Ticket $ticket)
    {
        $this->authorizeCompanyTicket($ticket);

        abort(403, 'إغلاق التذكرة متاح للمحامي فقط.');
    }

    private function authorizeCompanyTicket(Ticket $ticket): void
    {
        abort_if(
            (int) $ticket->company_id !== (int) Auth::guard('company')->id(),
            403
        );
    }

    private function storeAttachments(Request $request, TicketMessage $message): void
    {
        if (!$request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            if (!$file) {
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
