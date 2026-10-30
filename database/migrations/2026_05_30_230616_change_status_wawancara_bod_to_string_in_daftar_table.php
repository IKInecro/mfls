<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw statement because changing ENUM requires doctrine/dbal which might not be fully supported
        // Syntax MODIFY COLUMN hanya berlaku di MySQL/MariaDB
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE daftar MODIFY COLUMN status_wawancara_bod VARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE daftar MODIFY COLUMN status_wawancara_bod ENUM('Layak', 'Tidak Layak') NULL");
        }
    }
};
