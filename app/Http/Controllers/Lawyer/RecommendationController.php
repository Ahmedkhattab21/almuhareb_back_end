<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use App\Models\Ticket;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecommendationController extends Controller
{
    public function index(Request $request)
    {
        $lawyerId = Auth::guard('lawyer')->id();

        $query = Recommendation::query()
            ->with(['ticket.category', 'worker', 'company'])
            ->where('lawyer_id', $lawyerId)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('worker', fn ($workerQuery) => $workerQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn ($companyQuery) => $companyQuery->where('company_name', 'like', "%{$search}%"));
            });
        }

        $recommendations = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Recommendation::where('lawyer_id', $lawyerId)->count(),
            'today' => Recommendation::where('lawyer_id', $lawyerId)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
        ];

        return view('lawyer.recommendations.index', compact('recommendations', 'stats'));
    }

    public function create(Request $request)
    {
        $lawyerId = Auth::guard('lawyer')->id();

        $tickets = Ticket::query()
            ->with(['worker', 'company', 'category'])
            ->where('lawyer_id', $lawyerId)
            ->latest('last_message_at')
            ->latest('id')
            ->limit(100)
            ->get();

        $selectedTicket = null;

        if ($request->filled('ticket_id')) {
            $selectedTicket = Ticket::query()
                ->with(['worker', 'company', 'category'])
                ->where('lawyer_id', $lawyerId)
                ->findOrFail($request->integer('ticket_id'));
        }

        return view('lawyer.recommendations.create', compact('tickets', 'selectedTicket'));
    }

    public function store(Request $request)
    {
        $lawyerId = Auth::guard('lawyer')->id();

        $validated = $request->validate([
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $ticket = Ticket::query()
            ->where('lawyer_id', $lawyerId)
            ->findOrFail($validated['ticket_id']);

        abort_if(! $ticket->company_id || ! $ticket->worker_id, 422, 'لا يمكن إنشاء توصية بدون شركة وعامل مرتبطين بالتذكرة.');

        $attachmentData = [];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentData = [
                'attachment' => $file->store('recommendations/attachments', 'public'),
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_mime' => $file->getMimeType(),
                'attachment_size' => $file->getSize(),
            ];
        }

        $recommendation = Recommendation::create(array_merge([
            'ticket_id' => $ticket->id,
            'worker_id' => $ticket->worker_id,
            'company_id' => $ticket->company_id,
            'lawyer_id' => $lawyerId,
            'title' => $validated['title'],
            'description' => $validated['description'],
        ], $attachmentData));

        SystemNotifier::notifyRecommendationCreated(
            recommendation: $recommendation,
            actor: Auth::guard('lawyer')->user()
        );

        return redirect()
            ->route('lawyer.recommendations.show', $recommendation)
            ->with('toast_success', 'تم إرسال التوصية للشركة بنجاح.');
    }

    public function show(Recommendation $recommendation)
    {
        abort_if((int) $recommendation->lawyer_id !== (int) Auth::guard('lawyer')->id(), 403);

        $recommendation->load(['ticket.category', 'worker', 'company']);

        return view('lawyer.recommendations.show', compact('recommendation'));
    }

    public function destroy(Recommendation $recommendation)
    {
        abort_if((int) $recommendation->lawyer_id !== (int) Auth::guard('lawyer')->id(), 403);

        if ($recommendation->attachment) {
            Storage::disk('public')->delete($recommendation->attachment);
        }

        $recommendation->delete();

        return redirect()
            ->route('lawyer.recommendations.index')
            ->with('toast_success', 'تم حذف التوصية بنجاح.');
    }
}
