<?php
// In your 2025_05_29_035914_create_drivers_table.php file

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tipper_number')->nullable();
            $table->string('nic')->unique();
            $table->string('phone_number')->nullable(); // Match your app's expectation
            $table->text('address')->nullable();
            $table->string('photo')->nullable(); // For driver photos
            $table->string('license_number')->unique()->nullable();
            $table->date('license_expiry')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Add foreign key constraint if needed
            $table->foreign('tipper_number')->references('tipper_number')->on('tippers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
