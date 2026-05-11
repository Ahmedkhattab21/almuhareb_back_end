<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\WorkerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Language Switch

Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['ar', 'en'])) {
        abort(404);
    }

    session(['locale' => $locale]);

    return back();
})->name('lang.switch');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
            ->name('login');

        Route::post('/login', [AdminLoginController::class, 'login'])
            ->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/login-success', [AdminLoginController::class, 'loginSuccess'])
             ->name('login.success');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/logout', [AdminLoginController::class, 'logout'])
            ->name('logout');

        Route::resource('lawyers', LawyerController::class)
            ->except(['show']);

        Route::resource('companies', CompanyController::class);
        Route::resource('workers', WorkerController::class);

    });
});

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

// Home Redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
});
