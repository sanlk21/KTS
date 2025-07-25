<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('tipper_number');
            $table->unsignedBigInteger('driver_id');
            $table->string('driver_name'); // Add this field
            $table->unsignedBigInteger('plant_id');
            $table->string('plant_name'); // Add this field
            $table->date('delivery_date');
            $table->time('delivery_time')->nullable(); // Add this field
            $table->decimal('trip_amount', 8, 2)->default(0.00);
            $table->decimal('paid_amount', 8, 2)->default(0.00);

            $table->foreign('tipper_number')->references('tipper_number')->on('tippers')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
