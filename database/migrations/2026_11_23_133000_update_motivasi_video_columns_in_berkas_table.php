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
        Schema::table('berkas', function (Blueprint $table) {
            if (!Schema::hasColumn('berkas', 'motivasi_video_tiktok')) {
                $table->string('motivasi_video_tiktok')->nullable()->after('motivasi_video');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `berkas` MODIFY COLUMN `motivasi_video` TEXT NULL");
            DB::statement("ALTER TABLE `berkas` MODIFY COLUMN `motivasi_video_tiktok` TEXT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `berkas` MODIFY COLUMN `motivasi_video` VARCHAR(255) NULL");
            DB::statement("ALTER TABLE `berkas` MODIFY COLUMN `motivasi_video_tiktok` VARCHAR(255) NULL");
        }
    }
};
