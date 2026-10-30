<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->datetime('pendaftaran_mulai')->nullable();
            $table->datetime('pendaftaran_selesai')->nullable();
            // auto = ikut jadwal, buka = buka paksa, tutup = tutup paksa
            $table->string('mode', 10)->default('auto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_pendaftaran');
    }
};
