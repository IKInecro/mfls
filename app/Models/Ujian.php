<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    protected $fillable = [
        'nama',
        'durasi',
        'is_active',
        'jadwal_mulai',
        'jadwal_selesai',
        'override_status',
    ];

    protected $casts = [
        'jadwal_mulai' => 'datetime',
        'jadwal_selesai' => 'datetime',
    ];

    public function soals()
    {
        return $this->hasMany(Soal::class, 'ujian_id');
    }

    public function jawabanUjians()
    {
        return $this->hasMany(JawabanUjian::class, 'ujian_id');
    }

    public function sesiUjians()
    {
        return $this->hasMany(SesiUjian::class, 'ujian_id');
    }

    /**
     * Aturan akses pengerjaan (dipakai halaman web & API):
     * - override tutup → selalu tertutup; override buka → selalu terbuka
     * - auto → is_active + (tanpa jadwal → terbuka; ada jadwal → sekarang di rentang)
     */
    public function isOpen(): bool
    {
        if ($this->override_status === 'tutup') {
            return false;
        }
        if ($this->override_status === 'buka') {
            return (bool) $this->is_active;
        }
        if (!$this->is_active) {
            return false;
        }
        if (!$this->jadwal_mulai && !$this->jadwal_selesai) {
            return true;
        }

        $now = now();

        return (!$this->jadwal_mulai || $now->gte($this->jadwal_mulai))
            && (!$this->jadwal_selesai || $now->lte($this->jadwal_selesai));
    }

    /** Sisa waktu berjalan (detik) untuk peserta. Belum mulai = durasi penuh. */
    public function sisaDetik(?SesiUjian $sesi): int
    {
        $dispensasi = $sesi ? DispensasiUjian::where('ujian_id', $this->id)
            ->where('peserta_id', $sesi->peserta_id)
            ->sum('tambahan_menit') : 0;

        $detik = (int) ($this->durasi ?? 60) * 60 + $dispensasi * 60;

        if ($sesi && $sesi->mulai_pada) {
            $lewat = now()->diffInSeconds($sesi->mulai_pada, true);
            $detik = max(0, $detik - $lewat);
        }

        // Batas keras jadwal selesai (+ menit dispensasi peserta) — peserta gak
        // bisa nyedot waktu tanpa batas walau admin override-buka.
        if ($this->jadwal_selesai) {
            $batas = $this->jadwal_selesai->copy()->addMinutes((int) $dispensasi);
            if (now()->gt($batas)) {
                return 0;
            }
            $detik = min($detik, now()->diffInSeconds($batas, true));
        }

        return $detik;
    }

    /** Status sesi ujian untuk label UI: tertutup | berjalan | selesai. */
    public function statusSesi(): string
    {
        if (!$this->isOpen()) {
            return 'tertutup';
        }
        if ($this->jadwal_selesai && now()->gt($this->jadwal_selesai)) {
            return 'selesai';
        }

        return 'berjalan';
    }
}
