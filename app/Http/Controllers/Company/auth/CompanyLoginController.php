<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CompanyLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('company.auth.login');
    }

    public function loginSuccess()
    {
        return view('company.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => __('company_auth.email_required'),
            'email.email' => __('company_auth.email_invalid'),
            'password.required' => __('company_auth.password_required'),
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember');

        if (Auth::guard('company')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $company = Auth::guard('company')->user();

            if (($company->status ?? 'active') !== 'active') {
                Auth::guard('company')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('company.login')
                    ->withInput($request->only('email'))
                    ->with('toast_error', __('company_auth.account_inactive'));
            }

            return redirect()
                ->route('company.login.success')
                ->with('toast_success', __('company_auth.login_success'))
                ->with('redirect_url', route('company.dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->with('toast_error', __('company_auth.login_failed'));
    }

    //hello
    public function logout(Request $request)
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('company.login')
            ->with('toast_success', __('company_auth.logout_success'));
    }
}
