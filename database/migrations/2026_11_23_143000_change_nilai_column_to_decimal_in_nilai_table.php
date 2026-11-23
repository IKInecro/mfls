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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `nilai` MODIFY COLUMN `nilai` DECIMAL(5, 2) NOT NULL");
        } else {
            // SQLite support
            Schema::table('nilai', function (Blueprint $table) {
                $table->decimal('nilai', 5, 2)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `nilai` MODIFY COLUMN `nilai` INT NOT NULL");
        } else {
            Schema::table('nilai', function (Blueprint $table) {
                $table->integer('nilai')->change();
            });
        }
    }
};
