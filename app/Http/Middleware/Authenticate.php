<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('company') || $request->is('company/*')) {
                return route('company.login');
            }

            if ($request->is('lawyer') || $request->is('lawyer/*')) {
                return route('lawyer.login');
            }

            return route('landing');
        }
    }
}
