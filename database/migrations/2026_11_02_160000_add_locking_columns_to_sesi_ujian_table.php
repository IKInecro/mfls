<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sesi_ujian') && !Schema::hasColumn('sesi_ujian', 'terkunci')) {
            Schema::table('sesi_ujian', function (Blueprint $table) {
                $table->boolean('terkunci')->default(false)->after('jawaban');
                $table->string('alasan_kunci')->nullable()->after('terkunci');
                $table->unsignedTinyInteger('pelanggaran_count')->default(0)->after('alasan_kunci');
                $table->dateTime('terkunci_pada')->nullable()->after('pelanggaran_count');
                $table->dateTime('dibuka_pada')->nullable()->after('terkunci_pada');
                $table->string('dibuka_oleh')->nullable()->after('dibuka_pada');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sesi_ujian') && Schema::hasColumn('sesi_ujian', 'terkunci')) {
            Schema::table('sesi_ujian', function (Blueprint $table) {
                $table->dropColumn([
                    'terkunci',
                    'alasan_kunci',
                    'pelanggaran_count',
                    'terkunci_pada',
                    'dibuka_pada',
                    'dibuka_oleh',
                ]);
            });
        }
    }
};
