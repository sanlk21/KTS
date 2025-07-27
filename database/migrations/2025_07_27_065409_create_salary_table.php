<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->decimal('daily_salary', 10, 2)->default(0); // Expected daily salary
            $table->decimal('weekly_salary', 10, 2)->default(0); // Expected weekly salary
            $table->decimal('monthly_salary', 10, 2)->default(0); // Expected monthly salary
            $table->enum('salary_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->decimal('advance_amount', 10, 2)->default(0); // Any advance given
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('driver_salary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->date('record_date');
            $table->decimal('earned_amount', 10, 2)->default(0); // Amount earned from trips
            $table->decimal('expected_amount', 10, 2)->default(0); // Expected salary for the day
            $table->decimal('paid_amount', 10, 2)->default(0); // Amount actually paid to driver
            $table->decimal('balance_amount', 10, 2)->default(0); // Running balance (+ve means we owe, -ve means driver owes)
            $table->decimal('previous_balance', 10, 2)->default(0); // Balance from previous day
            $table->integer('total_trips')->default(0); // Number of trips completed
            $table->text('notes')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overpaid'])->default('pending');
            $table->timestamps();

            $table->unique(['driver_id', 'record_date']);
        });

        Schema::create('driver_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('salary_record_id')->nullable()->constrained('driver_salary_records')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_type', ['salary', 'advance', 'bonus', 'deduction', 'adjustment'])->default('salary');
            $table->string('payment_method')->default('cash'); // cash, bank, etc.
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_payments');
        Schema::dropIfExists('driver_salary_records');
        Schema::dropIfExists('driver_salaries');
    }
};
