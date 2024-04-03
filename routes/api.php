<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PasswordController;

Route::post('register', [RegisterController::class, 'register']);
Route::post('verify-code', [RegisterController::class, 'verify_code']);
Route::get('resend-code', [RegisterController::class, 'resend_code']);

Route::post('login', [LoginController::class, 'login']);

Route::post('forgot-password', [PasswordController::class, 'forgot_password']);
Route::post('forgot-password/verify-code', [PasswordController::class, 'verify_code']);
Route::post('forgot-password/reset-password', [PasswordController::class, 'reset_password']);
Route::get('forgot-password/resend-code', [PasswordController::class, 'resend_code']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('logout', [LoginController::class, 'logout']);
});
