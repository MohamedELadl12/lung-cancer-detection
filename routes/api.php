<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;





Route::controller(AuthController::class)->group(function () {
    Route::post('/register','store');
    Route::post('/login','login')->name('login');
    Route::post('/logout','logout');
    Route::get('/auth/google/callback','authGoogleCallback');
    Route::get('/auth/google','authGoogle');
});


Route::controller(UserController::class)->group(function () {
    
    Route::middleware(['auth:api','verifiedEmail'])->group(function () {
        Route::get('/user','show');
        Route::put('/user','update');
        Route::delete('/user','destroy');
    });
    Route::post('/verfiyEmail','verifyEmail')->middleware('auth:api');
    Route::get('newOtp'.'newOtp')->middleware('auth:api');
});


Route::controller(TipController::class)->group(function () {
    Route::middleware(['auth:api','verifiedEmail'])->group(function () {
        Route::get('/tips','GetAll');
        Route::get('/tip/{id}','GetTip');
        Route::post('/tip','createTip');
        Route::put('/tip','updateTip');
        Route::delete('/tip/{id}','deleteTip');

    });
    
});
