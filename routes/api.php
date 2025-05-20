<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ManagerController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('leads')->group(function () {
    Route::get('/', [LeadController::class, 'index']);
    Route::post('/', [LeadController::class, 'store']);
    Route::patch('/{lead}/assign', [LeadController::class, 'assign']);
    Route::patch('/{lead}/status', [LeadController::class, 'updateStatus']);
});

Route::get('/managers', [ManagerController::class, 'index']);
