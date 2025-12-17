<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\XenditSdkException;

class BookingController extends Controller
{
    /**
     * Store a newly created booking.
     */

    /**
     * @OA\Post(
     *   path="/api/bookings",
     *   tags={"Bookings"},
     *   summary="Melakukan booking ruang meeting",
     *   description="Endpoint untuk melakukan booking ruang meeting dan menghasilkan payment link melalui Xendit.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"meeting_room_id","booked_by","booking_date","start_time","end_time"},
     *       @OA\Property(property="meeting_room_id", type="integer", example=1),
     *       @OA\Property(property="booked_by", type="string", example="Andi Wijaya"),
     *       @OA\Property(property="booking_date", type="string", format="date", example="2025-12-20"),
     *       @OA\Property(property="start_time", type="string", example="09:00"),
     *       @OA\Property(property="end_time", type="string", example="11:00")
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response=201,
     *     description="Booking berhasil dibuat",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Booking berhasil"),
     *       @OA\Property(
     *         property="booking",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=10),
     *         @OA\Property(property="meeting_room_id", type="integer", example=1),
     *         @OA\Property(property="booked_by", type="string", example="Andi Wijaya"),
     *         @OA\Property(property="booking_date", type="string", example="2025-12-20"),
     *         @OA\Property(property="start_time", type="string", example="09:00"),
     *         @OA\Property(property="end_time", type="string", example="11:00"),
     *         @OA\Property(property="status", type="string", example="BOOKED")
     *       ),
     *       @OA\Property(
     *         property="payment_url",
     *         type="string",
     *         example="https://checkout.xendit.co/..."
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response=422,
     *     description="Validasi gagal",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The given data was invalid")
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response=500,
     *     description="Kesalahan server / Xendit error"
     *   )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'booked_by' => 'required|string',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Simpan booking
        $booking = Booking::create([
            'meeting_room_id' => $data['meeting_room_id'],
            'booked_by' => $data['booked_by'],
            'booking_date' => $data['booking_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => 'BOOKED'
        ]);

        // Konfigurasi Xendit
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $api = new InvoiceApi();

        $invoiceRequest = new CreateInvoiceRequest([
            'external_id' => 'booking-' . $booking->id,
            'amount' => 120000,
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

        return response()->json([
            'message' => 'Booking berhasil',
            'booking' => $booking,
            'payment_url' => $invoice['invoice_url']
        ], 201);
    }
}
