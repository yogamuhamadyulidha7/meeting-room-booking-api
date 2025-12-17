<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Relasi ke meeting_rooms (tanpa foreign key constraint)
            $table->unsignedBigInteger('meeting_room_id');

            $table->string('booked_by');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');

            // Status default untuk booking
            $table->string('status')->default('BOOKED');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
