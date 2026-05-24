<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactTicket;
use Illuminate\Http\Request;

class ContactTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactTicket::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(12)->withQueryString();
        $stats = [
            'total' => ContactTicket::count(),
            'new' => ContactTicket::where('status', ContactTicket::STATUS_NEW)->count(),
            'read' => ContactTicket::where('status', ContactTicket::STATUS_READ)->count(),
        ];

        return view('admin.contact-tickets.index', compact('tickets', 'stats'));
    }

    public function show(ContactTicket $contactTicket)
    {
        if ($contactTicket->status === ContactTicket::STATUS_NEW) {
            $contactTicket->update([
                'status' => ContactTicket::STATUS_READ,
                'read_at' => now(),
            ]);
        }

        return view('admin.contact-tickets.show', compact('contactTicket'));
    }
}
