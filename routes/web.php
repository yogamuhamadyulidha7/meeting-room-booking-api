<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes (Blade – TANPA Inertia)
|--------------------------------------------------------------------------
*/

/*
| Home → langsung ke Dashboard
*/
Route::get('/', function () {
    return redirect('/dashboard');
});

/*
| Dashboard (Blade + Database)
*/
Route::get('/dashboard', function () {

    $bookings = DB::table('bookings')->get();

    return view('dashboard', [
        'bookings' => $bookings
    ]);
});

/*
|--------------------------------------------------------------------------
| (Opsional) Route tambahan nanti di sini
|--------------------------------------------------------------------------
*/
