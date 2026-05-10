<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

        public function loginSuccess()
        {
            return view('admin.auth.login');
        }

  public function login(Request $request)
  {
      $request->validate([
          'email' => ['required', 'email'],
          'password' => ['required'],
      ]);

      $credentials = [
          'email' => $request->email,
          'password' => $request->password,
      ];

      if (Auth::guard('admin')->attempt($credentials)) {
          $request->session()->regenerate();

          return redirect()
              ->route('admin.login.success')
              ->with('toast_success', __('auth.login_success'))
              ->with('redirect_url', route('admin.dashboard'));
      }

      return back()
      ->withInput($request->only('email'))
      ->with('toast_error', __('auth.login_failed'));
  }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
    ->route('admin.login')
    ->with('toast_success', __('auth.logout_success'));
    }
}
