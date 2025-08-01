<?php

use App\Http\Controllers\api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('hmac.auth')->group(function () {
    Route::post('transactions', [TransactionController::class, 'transactions']);
    Route::get('transactions', [TransactionController::class, 'index']);
});
