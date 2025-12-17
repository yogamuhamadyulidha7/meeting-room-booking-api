<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE INVOICE (DIPANGGIL OLEH APLIKASI)
    |--------------------------------------------------------------------------
    */
    public function createInvoice(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|integer|min:10000'
        ]);

        $booking = Booking::findOrFail($data['booking_id']);

        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $api = new InvoiceApi();

        $invoiceRequest = new CreateInvoiceRequest([
            'external_id' => 'booking-' . $booking->id,
            'amount' => $data['amount'],
            'currency' => 'IDR',
            'description' => 'Pembayaran Booking Ruang Meeting',
            'invoice_duration' => 3600,
        ]);

        try {
            $invoice = $api->createInvoice($invoiceRequest);
        } catch (XenditSdkException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getFullError()
            ], 500);
        }

        Payment::create([
            'booking_id' => $booking->id,
            'invoice_id' => $invoice['id'],
            'invoice_url' => $invoice['invoice_url'],
            'amount' => $data['amount'],
            'status' => 'PENDING'
        ]);

        return response()->json([
            'message' => 'Invoice berhasil dibuat',
            'payment_url' => $invoice['invoice_url']
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | XENDIT WEBHOOK (WAJIB ADA)
    |--------------------------------------------------------------------------
    */
    public function webhook(Request $request)
    {
        // Validasi token webhook
        $callbackToken = $request->header('x-callback-token');

        if ($callbackToken !== env('XENDIT_WEBHOOK_SECRET')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Cari payment berdasarkan invoice ID
        $payment = Payment::where('invoice_id', $request->id)->first();

        if ($payment && $request->status === 'PAID') {
            $payment->update([
                'status' => 'PAID'
            ]);

            if ($payment->booking) {
                $payment->booking->update([
                    'status' => 'PAID'
                ]);
            }
        }

        return response()->json([
            'message' => 'Webhook received'
        ]);
    }
}
