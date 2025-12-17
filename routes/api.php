<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes (API KEY AUTH - TANPA SESSION)
|--------------------------------------------------------------------------
*/

Route::middleware('api.key')->group(function () {

    // Booking API
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    // Payment API
    Route::post('/payments', [PaymentController::class, 'createInvoice']);
    Route::post('/payment', [PaymentController::class, 'webhook']);
});

/*
|--------------------------------------------------------------------------
| Xendit Webhook (TIDAK pakai API Key)
|--------------------------------------------------------------------------
*/
Route::post('/xendit/webhook', function (Request $request) {

    $callbackToken = $request->header('x-callback-token');

    if ($callbackToken !== env('XENDIT_WEBHOOK_SECRET')) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $payment = \App\Models\Payment::where('invoice_id', $request->id)->first();

    if ($payment && $request->status === 'PAID') {
        $payment->update(['status' => 'PAID']);
        $payment->booking->update(['status' => 'PAID']);
    }

    return response()->json(['message' => 'Webhook received']);
});
