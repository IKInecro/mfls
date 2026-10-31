<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            $table->dateTime('jadwal_mulai')->nullable()->after('is_active');
            $table->dateTime('jadwal_selesai')->nullable()->after('jadwal_mulai');
            // auto = ikut jadwal; buka = paksa terbuka; tutup = paksa tertutup
            $table->string('override_status', 10)->default('auto')->after('jadwal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('ujian', function (Blueprint $table) {
            $table->dropColumn(['jadwal_mulai', 'jadwal_selesai', 'override_status']);
        });
    }
};
