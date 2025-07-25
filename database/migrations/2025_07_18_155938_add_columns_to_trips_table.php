<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('driver_name')->after('driver_id');
            $table->string('plant_name')->after('plant_id');
            $table->time('delivery_time')->after('delivery_date');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'plant_name', 'delivery_time']);
        });
    }
};
