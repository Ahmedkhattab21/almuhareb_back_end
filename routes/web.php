<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AppPageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompanyNewsController as AdminCompanyNewsController;
use App\Http\Controllers\Admin\ContactTicketController as AdminContactTicketController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LawyerController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RecommendationController as AdminRecommendationController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Company\Auth\CompanyLoginController;
use App\Http\Controllers\Company\CompanyDashboardController;
use App\Http\Controllers\Company\CompanyLawyerController;
use App\Http\Controllers\Company\CompanyNewsController;
use App\Http\Controllers\Company\CompanyPositionController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Company\CompanyRecommendationController;
use App\Http\Controllers\Company\CompanyTicketController;
use App\Http\Controllers\Company\CompanyWorkerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\Lawyer\CompanyController as LawyerCompanyController;
 use App\Http\Controllers\Lawyer\LawyerProfileController;
use App\Http\Controllers\Lawyer\LawyerLoginController;
use App\Http\Controllers\Lawyer\LawyerTicketController;
use App\Http\Controllers\Lawyer\RecommendationController as LawyerRecommendationController;
use App\Http\Controllers\Lawyer\WorkerController as LawyerWorkerController;
use App\Http\Controllers\Lawyer\LawyerDashboardController;
use App\Http\Controllers\SystemNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

        Route::get('/profile', [AdminProfileController::class, 'show'])
            ->name('profile.show');

        Route::put('/profile', [AdminProfileController::class, 'update'])
            ->name('profile.update');

        Route::get('/search', [GlobalSearchController::class, 'admin'])
            ->name('search');

        Route::resource('lawyers', LawyerController::class)
            ->except(['show']);

        Route::resource('companies', CompanyController::class);
        Route::resource('company-news', AdminCompanyNewsController::class)
            ->parameters(['company-news' => 'companyNews']);
        Route::resource('categories', CategoryController::class)
            ->except(['show']);
        Route::get('app-pages/privacy-policy', [AppPageController::class, 'privacyPolicy'])
            ->name('app-pages.privacy-policy');
        Route::get('app-pages/about-app', [AppPageController::class, 'aboutApp'])
            ->name('app-pages.about-app');
        Route::resource('app-pages', AppPageController::class);
        Route::get('workers/import', [WorkerController::class, 'importForm'])
            ->name('workers.import');
        Route::post('workers/import', [WorkerController::class, 'import'])
            ->name('workers.import.store');
        Route::get('workers/import/template', [WorkerController::class, 'importTemplate'])
            ->name('workers.import.template');
        Route::resource('workers', WorkerController::class);
        Route::resource('lawyers', LawyerController::class);

        Route::resource('positions', PositionController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->names('positions');

        Route::resource('tickets', AdminTicketController::class)->only(['index', 'show']);
        Route::resource('recommendations', AdminRecommendationController::class)->only(['index', 'show']);
        Route::resource('contact-tickets', AdminContactTicketController::class)
            ->only(['index', 'show'])
            ->parameters(['contact-tickets' => 'contactTicket']);

        Route::post('tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])
            ->name('tickets.reply');

        Route::patch('tickets/{ticket}/status', [AdminTicketController::class, 'updateStatus'])
            ->name('tickets.status');

        Route::post('tickets/{ticket}/close', [AdminTicketController::class, 'close'])
            ->name('tickets.close');


                Route::get('/notifications', [SystemNotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [SystemNotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    Route::post('/notifications/{id}/read', [SystemNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/notifications/{id}/open', [SystemNotificationController::class, 'open'])
        ->name('notifications.open');
    });

    Route::get('/', function () {
        return auth('admin')->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });

    Route::fallback(function () {
        return auth('admin')->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
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
            Route::get('/login-success', [CompanyLoginController::class, 'loginSuccess'])
               ->name('login.success');

            Route::get('/dashboard', [CompanyDashboardController::class, 'index'])
                ->name('dashboard');

            Route::post('/logout', [CompanyLoginController::class, 'logout'])
                ->name('logout');

            Route::get('/profile', [CompanyProfileController::class, 'show'])
                ->name('profile.show');

            Route::put('/profile', [CompanyProfileController::class, 'update'])
                ->name('profile.update');

            Route::get('/search', [GlobalSearchController::class, 'company'])
                ->name('search');

            Route::get('workers/import', [CompanyWorkerController::class, 'importForm'])
                ->name('workers.import');
            Route::post('workers/import', [CompanyWorkerController::class, 'import'])
                ->name('workers.import.store');
            Route::get('workers/import/template', [CompanyWorkerController::class, 'importTemplate'])
                ->name('workers.import.template');
            Route::resource('workers', CompanyWorkerController::class);
            Route::resource('positions', CompanyPositionController::class);
            Route::resource('company-news', CompanyNewsController::class)
                ->parameters(['company-news' => 'companyNews']);

            Route::get('/lawyer', [CompanyLawyerController::class, 'show'])
    ->name('lawyer.show');

            Route::resource('tickets', CompanyTicketController::class)->only(['index', 'show']);
            Route::resource('recommendations', CompanyRecommendationController::class)->only(['index', 'show']);

            Route::post('tickets/{ticket}/reply', [CompanyTicketController::class, 'reply'])
                ->name('tickets.reply');

            Route::post('tickets/{ticket}/close', [CompanyTicketController::class, 'close'])
                ->name('tickets.close');


                    Route::get('/notifications', [SystemNotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [SystemNotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    Route::post('/notifications/{id}/read', [SystemNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/notifications/{id}/open', [SystemNotificationController::class, 'open'])
        ->name('notifications.open');
        });

        Route::get('/', function () {
            return auth('company')->check()
                ? redirect()->route('company.dashboard')
                : redirect()->route('company.login');
        });

        Route::fallback(function () {
            return auth('company')->check()
                ? redirect()->route('company.dashboard')
                : redirect()->route('company.login');
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
            Route::get('/login-success', [LawyerLoginController::class, 'loginSuccess'])
                 ->name('login.success');

            Route::get('/dashboard', [LawyerDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/profile', [LawyerProfileController::class, 'show'])
                ->name('profile.show');

            Route::put('/profile', [LawyerProfileController::class, 'update'])
                ->name('profile.update');

            Route::get('/search', [GlobalSearchController::class, 'lawyer'])
                ->name('search');

            Route::resource('companies', LawyerCompanyController::class)
                ->only(['index', 'show']);

            Route::resource('workers', LawyerWorkerController::class)
                ->only(['index', 'show']);

            Route::post('/logout', [LawyerLoginController::class, 'logout'])
                ->name('logout');

            Route::resource('tickets', LawyerTicketController::class)->only(['index', 'show']);
            Route::resource('recommendations', LawyerRecommendationController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

            Route::post('tickets/{ticket}/reply', [LawyerTicketController::class, 'reply'])
                ->name('tickets.reply');

            Route::post('tickets/{ticket}/ai-suggestions/{suggestion}/audio', [LawyerTicketController::class, 'generateSuggestionAudio'])
                ->name('tickets.ai-suggestions.audio');

            Route::patch('tickets/{ticket}/status', [LawyerTicketController::class, 'updateStatus'])
                ->name('tickets.status');

            Route::post('tickets/{ticket}/close', [LawyerTicketController::class, 'close'])
                ->name('tickets.close');

                  Route::get('/notifications', [SystemNotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/read-all', [SystemNotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');

    Route::post('/notifications/{id}/read', [SystemNotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/notifications/{id}/open', [SystemNotificationController::class, 'open'])
        ->name('notifications.open');
        });

        Route::get('/', function () {
            return auth('lawyer')->check()
                ? redirect()->route('lawyer.dashboard')
                : redirect()->route('lawyer.login');
        });

        Route::fallback(function () {
            return auth('lawyer')->check()
                ? redirect()->route('lawyer.dashboard')
                : redirect()->route('lawyer.login');
        });
    });

// Home Redirect
Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/lang/{locale}', function ($locale) {
    abort_unless(in_array($locale, ['ar', 'en']), 404);

    Session::put('locale', $locale);

    return back();
})->name('lang.switch');
Route::get('/about', function () {
    return view('pages.static', [
        'page' => 'about',
    ]);
})->name('pages.about');

Route::get('/privacy-policy', function () {
    return view('pages.static', [
        'page' => 'privacy',
    ]);
})->name('pages.privacy');

Route::get('/terms-of-use', function () {
    return view('pages.static', [
        'page' => 'terms',
    ]);
})->name('pages.terms');
Route::get('/contact-us', function () {
    return view('pages.contact');
})->name('pages.contact');

Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.submit');

Route::post('/contact-us-legacy-disabled', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'email' => ['required', 'email', 'max:150'],
        'phone' => ['nullable', 'string', 'max:30'],
        'company' => ['nullable', 'string', 'max:150'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    // هنا بعدين ممكن نخزن الرسالة في الداتابيز أو نبعتها Email

    return back()->with('toast_success', __('landing.contact_page.success_message'));
})->name('contact.submit.legacy');
