<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('lawyer.auth.login');
    }

    public function loginSuccess()
    {
        return view('lawyer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => __('lawyer_auth.email_required'),
            'email.email' => __('lawyer_auth.email_invalid'),
            'password.required' => __('lawyer_auth.password_required'),
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        if (Auth::guard('lawyer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $lawyer = Auth::guard('lawyer')->user();

            if (($lawyer->status ?? 'active') !== 'active') {
                Auth::guard('lawyer')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('lawyer.login')
                    ->withInput($request->only('email'))
                    ->with('toast_error', __('lawyer_auth.account_inactive'));
            }

            return redirect()
                ->route('lawyer.login.success')
                ->with('toast_success', __('lawyer_auth.login_success'))
                ->with('redirect_url', route('lawyer.dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('toast_error', __('lawyer_auth.login_failed'));
    }

    public function logout(Request $request)
    {
        Auth::guard('lawyer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('lawyer.login')
            ->with('toast_success', __('lawyer_auth.logout_success'));
    }
}
