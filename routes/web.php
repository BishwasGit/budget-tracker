<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PersonController;

Route::get('/', [BalanceController::class, 'index'])->name('balance.index');
Route::post('/add-balance', [BalanceController::class, 'addBalance'])->name('balance.add');

Route::resource('people', PersonController::class);
Route::resource('transactions', TransactionController::class);

Route::post('/transactions/{transaction}/mark-paid', [TransactionController::class, 'markPaid'])->name('transactions.mark-paid');
