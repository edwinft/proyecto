<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\OrderWebController;

Route::get('/', fn() => redirect('/orders'));
Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderWebController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderWebController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderWebController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/pay', [OrderWebController::class, 'pay'])->name('orders.pay');
