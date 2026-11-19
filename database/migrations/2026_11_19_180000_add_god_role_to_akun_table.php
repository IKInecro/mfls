<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Akun;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah 'god' ke enum role di tabel akun (khusus MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `akun` MODIFY COLUMN `role` ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia', 'palugada', 'dosen', 'god') NOT NULL");
        }

        // 2. Tambah kolom is_active jika belum ada
        Schema::table('akun', function (Blueprint $table) {
            if (!Schema::hasColumn('akun', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        // 3. Buat akun default GOD Admin jika belum ada
        if (!Akun::where('email', 'godmncu@mncu.ac.id')->exists()) {
            Akun::create([
                'nama' => 'Developer Core',
                'email' => 'godmncu@mncu.ac.id',
                'password' => Hash::make('12345678'),
                'role' => 'god',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        } else {
            Akun::where('email', 'godmncu@mncu.ac.id')->update([
                'role' => 'god',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            if (Schema::hasColumn('akun', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `akun` MODIFY COLUMN `role` ENUM('admin', 'akademik', 'mentor', 'pendaftar', 'panitia', 'palugada', 'dosen') NOT NULL");
        }
    }
};
