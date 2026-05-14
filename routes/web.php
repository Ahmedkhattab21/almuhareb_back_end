<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Company\Auth\CompanyLoginController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Company\CompanyLawyerController;
use App\Http\Controllers\Company\CompanyPositionController;
use App\Http\Controllers\Company\CompanyWorkerController;
use App\Http\Controllers\Lawyer\LawyerLoginController;
use App\Http\Controllers\LawyerDashboardController;
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
        Route::resource('lawyers', LawyerController::class);

        Route::resource('positions', PositionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->names('positions');
    });
});

Route::prefix('company')
    ->name('company.')
    ->group(function () {
        Route::middleware('guest:company')->group(function () {
            Route::get('/login', [CompanyLoginController::class, 'showLoginForm'])
                ->name('login');

            Route::post('/login', [CompanyLoginController::class, 'login'])
                ->name('login.submit');
        });

        Route::middleware('auth:company')->group(function () {
            Route::get('/dashboard', [CompanyDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('/logout', [CompanyLoginController::class, 'logout'])
                ->name('logout');

            Route::resource('workers', CompanyWorkerController::class);
            Route::resource('positions', CompanyPositionController::class);

            Route::get('/lawyer', [CompanyLawyerController::class, 'show'])
    ->name('lawyer.show');
        });
    });

Route::prefix('lawyer')
    ->name('lawyer.')
    ->group(function () {
        Route::middleware('guest:lawyer')->group(function () {
            Route::get('/login', [LawyerLoginController::class, 'showLoginForm'])
                ->name('login');

            Route::post('/login', [LawyerLoginController::class, 'login'])
                ->name('login.submit');
        });

        Route::middleware('auth:lawyer')->group(function () {
            Route::get('/dashboard', [LawyerDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('/logout', [LawyerLoginController::class, 'logout'])
                ->name('logout');
        });
    });

// Home Redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
});
