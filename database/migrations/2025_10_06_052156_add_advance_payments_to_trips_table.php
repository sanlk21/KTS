<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trips', function (Blueprint $table) {
            // Advance payment fields
            $table->decimal('advance_amount', 10, 2)->default(0)->after('paid_amount');
            $table->decimal('actual_paid', 10, 2)->default(0)->after('advance_amount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('actual_paid');
            $table->text('payment_notes')->nullable()->after('balance_due');
        });

        // Create a separate table for driver advance history
        Schema::create('driver_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['advance', 'deduction', 'payment'])->default('advance');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
            
            $table->index(['driver_id', 'transaction_date']);
        });
    }

    public function down()
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['advance_amount', 'actual_paid', 'balance_due', 'payment_notes']);
        });
        
        Schema::dropIfExists('driver_advances');
    }
};