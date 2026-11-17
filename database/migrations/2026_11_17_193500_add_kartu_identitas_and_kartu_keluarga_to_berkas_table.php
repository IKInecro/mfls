<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            if (!Schema::hasColumn('berkas', 'kartu_identitas')) {
                $table->string('kartu_identitas')->nullable()->after('foto');
            }
            if (!Schema::hasColumn('berkas', 'kartu_keluarga')) {
                $table->string('kartu_keluarga')->nullable()->after('kartu_identitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn(['kartu_identitas', 'kartu_keluarga']);
        });
    }
};
