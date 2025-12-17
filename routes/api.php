<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Models\Payment;

/*
|--------------------------------------------------------------------------
| API Routes (PAKAI API KEY - TANPA SESSION)
|--------------------------------------------------------------------------
*/
Route::middleware('api.key')->group(function () {

    // Booking API
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

    // Create Invoice (Xendit)
    Route::post('/payments', [PaymentController::class, 'createInvoice']);
});

/*
|--------------------------------------------------------------------------
| XENDIT WEBHOOK (TANPA API KEY)
|--------------------------------------------------------------------------
| Endpoint ini dipanggil LANGSUNG oleh Xendit
| Status sukses = SETTLED atau PAID
*/
Route::post('/xendit/webhook', function (Request $request) {

    // Validasi callback token
    if ($request->header('x-callback-token') !== env('XENDIT_WEBHOOK_SECRET')) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $invoiceId = $request->id;
    $status = $request->status; // SETTLED, PAID, EXPIRED, dll

    $payment = Payment::where('invoice_id', $invoiceId)->first();

    if (!$payment) {
        return response()->json(['message' => 'Payment not found'], 404);
    }

    /**
     * PEMBAYARAN BERHASIL
     */
    if (in_array($status, ['PAID', 'SETTLED'])) {

        $payment->update([
            'status' => 'PAID'
        ]);

        $payment->booking->update([
            'status' => 'BOOKED'
        ]);
    }

    /**
     * PEMBAYARAN GAGAL / EXPIRED
     */
    if ($status === 'EXPIRED') {

        $payment->update([
            'status' => 'EXPIRED'
        ]);

        $payment->booking->update([
            'status' => 'CANCELLED'
        ]);
    }

    return response()->json([
        'message' => 'Webhook processed',
        'xendit_status' => $status
    ]);
});
