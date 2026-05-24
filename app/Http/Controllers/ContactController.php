<?php

namespace App\Http\Controllers;

use App\Models\ContactTicket;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactTicket::create([
            ...$validated,
            'status' => ContactTicket::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('toast_success', __('landing.contact_page.success_message'));
    }
}
