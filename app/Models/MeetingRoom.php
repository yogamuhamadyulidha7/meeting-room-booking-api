<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    protected $fillable = ['room_name', 'capacity', 'location', 'is_available'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

