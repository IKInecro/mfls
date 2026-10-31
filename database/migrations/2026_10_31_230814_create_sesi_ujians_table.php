<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->onDelete('cascade');
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->dateTime('mulai_pada');
            $table->text('jawaban')->nullable(); // draft autosave JSON {soal_id: "a"}
            $table->timestamps();
            $table->unique(['ujian_id', 'peserta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_ujian');
    }
};
