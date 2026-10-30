<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPendaftaran extends Model
{
    protected $table = 'pengaturan_pendaftaran';

    protected $fillable = [
        'pendaftaran_mulai',
        'pendaftaran_selesai',
        'mode',
    ];

    protected function casts(): array
    {
        return [
            'pendaftaran_mulai' => 'datetime',
            'pendaftaran_selesai' => 'datetime',
        ];
    }

    /**
     * Baris pengaturan tunggal. Belum ada = default tertutup otomatis (mode auto, tanpa jadwal).
     */
    public static function instance(): static
    {
        return static::query()->firstOrCreate([], ['mode' => 'auto']);
    }

    /**
     * Satu-satunya sumber kebenaran status pendaftaran.
     * Dipanggil dari /register, aksi pendaftar, dan homepage.
     */
    public static function aktif(): bool
    {
        $s = static::instance();

        if ($s->mode === 'buka') {
            return true;
        }
        if ($s->mode === 'tutup') {
            return false;
        }

        return $s->pendaftaran_mulai
            && $s->pendaftaran_selesai
            && now()->gte($s->pendaftaran_mulai)
            && now()->lte($s->pendaftaran_selesai);
    }
}
