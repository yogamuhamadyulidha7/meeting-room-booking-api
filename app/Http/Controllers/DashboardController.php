<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::orderBy('booking_date', 'asc')->get();

        return view('dashboard', [
            'bookings' => $bookings
        ]);
    }
}
