<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'meeting_room_id',
        'booked_by',
        'booking_date',
        'start_time',
        'end_time',
        'status'
    ];

    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }
}

