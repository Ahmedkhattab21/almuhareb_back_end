<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('lawyer')->check()) {
            return redirect()->route('lawyer.dashboard');
        }

        return view('lawyer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'password.required' => __('messages.password_required'),
        ]);

        $credentials = $request->only('email', 'password');

        $remember = $request->boolean('remember');

        if (Auth::guard('lawyer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $lawyer = Auth::guard('lawyer')->user();

            if (($lawyer->status ?? 'active') !== 'active') {
                Auth::guard('lawyer')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->with('toast_error', __('messages.account_inactive'));
            }

            return redirect()
                ->route('lawyer.dashboard')
                ->with('toast_success', __('messages.login_success'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('toast_error', __('messages.invalid_credentials'));
    }

    public function logout(Request $request)
    {
        Auth::guard('lawyer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('lawyer.login')
            ->with('toast_success', __('messages.logout_success'));
    }
}
