<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            if (!Schema::hasColumn('berkas', 'bukti_follow_ig_mncunation')) {
                $table->string('bukti_follow_ig_mncunation')->nullable();
            }
            if (!Schema::hasColumn('berkas', 'bukti_follow_ig_thearetegen')) {
                $table->string('bukti_follow_ig_thearetegen')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('berkas', function (Blueprint $table) {
            $table->dropColumn([
                'bukti_follow_ig_mncunation',
                'bukti_follow_ig_thearetegen',
            ]);
        });
    }
};
