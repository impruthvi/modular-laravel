<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\CheckoutController;

Route::as('order::')->middleware('auth')->group(function () {
    Route::post('checkout', CheckoutController::class)->name('checkout');
});
