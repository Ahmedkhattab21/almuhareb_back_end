<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('welcome', [WelcomeController::class,'welcome']);
// Route::get('user', [UserController::class,'index']);

 Route::post('/login', [AuthController::class, 'login']);
Route::post('/register-admin', [AuthController::class, 'registerAdmin']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

});
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::post('/companies', [AuthController::class, 'addCompany']);
    Route::post('/lawyers', [AuthController::class, 'addLawyer']);
    Route::post('/company-supervisors', [AuthController::class, 'addCompanySupervisor']);
});

/*
|--------------------------------------------------------------------------
| Company Supervisor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:company_supervisor'])->prefix('company')->group(function () {
    Route::post('/workers', [AuthController::class, 'addWorker']);
});







