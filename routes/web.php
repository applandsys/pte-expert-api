<?php


use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "PTE EXPERT BACKEND";
});

Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::post('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

Route::get('/orders/{tran_id}', [PaymentController::class, 'getOrder']);

