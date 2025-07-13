<?php

   use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   return new class extends Migration
   {
       public function up(): void
       {
           Schema::create('tippers', function (Blueprint $table) {
               $table->string('tipper_number')->primary();
               $table->enum('size', [2,3, 4]);
               $table->date('license_expiry')->nullable();
               $table->timestamps();
           });
       }

       public function down(): void
       {
           Schema::dropIfExists('tippers');
       }
   };
