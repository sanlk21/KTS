<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDriverAdvancesTypeEnum extends Migration
{
    public function up()
    {
        // For MySQL
        DB::statement("ALTER TABLE driver_advances MODIFY COLUMN type ENUM('advance', 'deduction', 'payment', 'salary') NOT NULL");

        // For other databases, you might need to drop and recreate the column
        // Schema::table('driver_advances', function (Blueprint $table) {
        //     $table->enum('type', ['advance', 'deduction', 'payment', 'salary'])->default('advance')->change();
        // });
    }

    public function down()
    {
        DB::statement("ALTER TABLE driver_advances MODIFY COLUMN type ENUM('advance', 'deduction', 'payment') NOT NULL");
    }
}