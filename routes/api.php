<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Worker\WorkerAuthController;
use App\Http\Controllers\Api\Worker\WorkerTicketController;
use App\Http\Controllers\Api\Worker\WorkerTicketStatsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Worker Mobile App Routes
|--------------------------------------------------------------------------
*/

Route::prefix('worker')
    ->name('api.worker.')
    ->group(function () {

        Route::post('/login/request-code', [WorkerAuthController::class, 'requestCode'])
            ->name('login.request_code');

        Route::post('/login/verify-code', [WorkerAuthController::class, 'verifyCode'])
            ->name('login.verify_code');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [WorkerAuthController::class, 'me']);
            Route::post('/logout', [WorkerAuthController::class, 'logout']);


            Route::get('/tickets/stats', [WorkerTicketController::class, 'stats']);
            Route::get('/tickets', [WorkerTicketController::class, 'index']);
            Route::post('/tickets', [WorkerTicketController::class, 'store']);
            Route::get('/tickets/{ticket}', [WorkerTicketController::class, 'show']);
            Route::post('/tickets/{ticket}/reply', [WorkerTicketController::class, 'reply']);
            Route::post('/tickets/{ticket}/close', [WorkerTicketController::class, 'close']);
            Route::post('/tickets/{ticket}/reopen', [WorkerTicketController::class, 'reopen']);


        });
    });


/*
|--------------------------------------------------------------------------
| Old / Testing Routes
|--------------------------------------------------------------------------
*/

// Route::get('welcome', [WelcomeController::class, 'welcome']);
// Route::get('user', [UserController::class, 'index']);

// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register-admin', [AuthController::class, 'registerAdmin']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
//     Route::post('/logout', [AuthController::class, 'logout']);
// });
