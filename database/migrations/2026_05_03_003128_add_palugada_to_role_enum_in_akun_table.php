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
        // Syntax MODIFY COLUMN hanya berlaku di MySQL/MariaDB; di SQLite enum tidak butuh diubah
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE akun MODIFY COLUMN role ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia', 'palugada') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE akun MODIFY COLUMN role ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia') NOT NULL");
        }
    }
};
