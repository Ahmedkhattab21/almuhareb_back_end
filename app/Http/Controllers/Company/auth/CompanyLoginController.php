<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('company')->check()) {
            return redirect()->route('company.dashboard');
        }

        return view('company.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('company')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()
                ->route('company.login')
                ->with('redirect_url', route('company.dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => __('company_auth.failed'),
            ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('company.login');
    }
}
