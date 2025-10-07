<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('trips', 'advance_amount')) {
                $table->decimal('advance_amount', 10, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('trips', 'actual_paid')) {
                $table->decimal('actual_paid', 10, 2)->default(0)->after('advance_amount');
            }
            if (!Schema::hasColumn('trips', 'balance_due')) {
                $table->decimal('balance_due', 10, 2)->default(0)->after('actual_paid');
            }
            if (!Schema::hasColumn('trips', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('balance_due');
            }
            if (!Schema::hasColumn('trips', 'trip_number')) {
                $table->integer('trip_number')->default(1)->after('payment_notes');
            }
            if (!Schema::hasColumn('trips', 'batch_id')) {
                $table->string('batch_id')->nullable()->after('trip_number');
            }
            if (!Schema::hasColumn('trips', 'total_trips_in_batch')) {
                $table->integer('total_trips_in_batch')->default(1)->after('batch_id');
            }
            if (!Schema::hasColumn('trips', 'total_batch_amount')) {
                $table->decimal('total_batch_amount', 10, 2)->default(0)->after('total_trips_in_batch');
            }
            if (!Schema::hasColumn('trips', 'total_batch_salary')) {
                $table->decimal('total_batch_salary', 10, 2)->default(0)->after('total_batch_amount');
            }
            if (!Schema::hasColumn('trips', 'net_income_per_trip')) {
                $table->decimal('net_income_per_trip', 10, 2)->default(0)->after('total_batch_salary');
            }
            if (!Schema::hasColumn('trips', 'total_net_income')) {
                $table->decimal('total_net_income', 10, 2)->default(0)->after('net_income_per_trip');
            }
            
            // Add index for batch_id for better performance
            if (!Schema::hasColumn('trips', 'batch_id')) {
                $table->index('batch_id');
            }
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'advance_amount',
                'actual_paid',
                'balance_due',
                'payment_notes',
                'trip_number',
                'batch_id',
                'total_trips_in_batch',
                'total_batch_amount',
                'total_batch_salary',
                'net_income_per_trip',
                'total_net_income'
            ]);
        });
    }
};