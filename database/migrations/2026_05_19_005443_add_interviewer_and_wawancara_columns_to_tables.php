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
        // 1. Alter role ENUM in akun table to include 'dosen' (hanya MySQL/MariaDB)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE akun MODIFY COLUMN role ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia', 'palugada', 'dosen') NOT NULL");
        }

        // 2. Add interviewer_id and ruangan to peserta table
        Schema::table('peserta', function (Blueprint $table) {
            $table->unsignedBigInteger('interviewer_id')->nullable()->after('akun_id');
            $table->foreign('interviewer_id')->references('id')->on('akun')->onDelete('set null');
            $table->string('ruangan')->nullable()->after('interviewer_id');
        });

        // 3. Add wawancara assessment columns to penilaian_akademiks table
        Schema::table('penilaian_akademiks', function (Blueprint $table) {
            $table->float('wawancara_motivasi')->nullable();
            $table->float('wawancara_prestasi')->nullable();
            $table->float('wawancara_karakter')->nullable();
            $table->float('wawancara_kontribusi')->nullable();
            $table->float('wawancara_komunikasi')->nullable();

            $table->text('wawancara_motivasi_catatan')->nullable();
            $table->text('wawancara_prestasi_catatan')->nullable();
            $table->text('wawancara_karakter_catatan')->nullable();
            $table->text('wawancara_kontribusi_catatan')->nullable();
            $table->text('wawancara_komunikasi_catatan')->nullable();

            $table->string('rekomendasi_akhir')->nullable();
            $table->string('rekomendasi_beasiswa')->nullable();
            $table->text('catatan_rekomendasi_beasiswa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian_akademiks', function (Blueprint $table) {
            $table->dropColumn([
                'wawancara_motivasi',
                'wawancara_prestasi',
                'wawancara_karakter',
                'wawancara_kontribusi',
                'wawancara_komunikasi',
                'wawancara_motivasi_catatan',
                'wawancara_prestasi_catatan',
                'wawancara_karakter_catatan',
                'wawancara_kontribusi_catatan',
                'wawancara_komunikasi_catatan',
                'rekomendasi_akhir',
                'rekomendasi_beasiswa',
                'catatan_rekomendasi_beasiswa'
            ]);
        });

        Schema::table('peserta', function (Blueprint $table) {
            $table->dropForeign(['interviewer_id']);
            $table->dropColumn(['interviewer_id', 'ruangan']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE akun MODIFY COLUMN role ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia', 'palugada') NOT NULL");
        }
    }
};
