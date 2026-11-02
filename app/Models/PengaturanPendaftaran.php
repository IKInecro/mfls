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
        'timeline',
    ];

    protected function casts(): array
    {
        return [
            'pendaftaran_mulai' => 'datetime',
            'pendaftaran_selesai' => 'datetime',
            'timeline' => 'array',
        ];
    }

    /**
     * Default rangkaian timeline tahapan seleksi MFLS Batch 3 2026.
     */
    public static function defaultTimeline(): array
    {
        return [
            [
                'step' => '01',
                'title' => 'Pembukaan Pendaftaran Akun',
                'date' => '16 Maret 2026',
                'desc' => 'Registrasi akun calon pendaftar, pengisian data biodata diri, serta upload dokumen awal.',
                'status' => 'Selesai',
                'active' => false,
            ],
            [
                'step' => '02',
                'title' => 'Batas Akhir Submit Berkas',
                'date' => '04 Mei 2026',
                'desc' => 'Penutupan submit berkas rapor, personal statement, study plan, dan surat rekomendasi sekolah.',
                'status' => 'Sedang Berlangsung',
                'active' => true,
            ],
            [
                'step' => '03',
                'title' => 'Pengumpulan Mini Project',
                'date' => '08 Mei 2026',
                'desc' => 'Pengerjaan mini project atau studi kasus kepemimpinan sesuai panduan panitia seleksi.',
                'status' => 'Mendatang',
                'active' => false,
            ],
            [
                'step' => '04',
                'title' => 'Tes CBT (TPA, TBI & Pemetaan)',
                'date' => '16 Mei 2026',
                'desc' => 'Ujian seleksi berbasis komputer (CBT online) mencakup Tes Potensi Akademik dan Bahasa Inggris.',
                'status' => 'Mendatang',
                'active' => false,
            ],
            [
                'step' => '05',
                'title' => 'Wawancara BoD & Praktisi',
                'date' => '20 Mei 2026',
                'desc' => 'Sesi wawancara mendalam bersama pimpinan MNC University dan praktisi profesional MNC Group.',
                'status' => 'Mendatang',
                'active' => false,
            ],
            [
                'step' => '06',
                'title' => 'Sit-In Class & Company Visit',
                'date' => '21 - 22 Mei 2026',
                'desc' => 'Pengalaman belajar langsung dan studi lapangan di unit bisnis dan studio MNC Group.',
                'status' => 'Mendatang',
                'active' => false,
            ],
            [
                'step' => '07',
                'title' => 'Pengumuman Resmi Awardee',
                'date' => '03 Juni 2026',
                'desc' => 'Penerbitan surat keputusan resmi peraih beasiswa penuh 100% dan parsial MNC University.',
                'status' => 'Mendatang',
                'active' => false,
            ],
        ];
    }

    /**
     * Mengambil array timeline dari DB atau fallback ke default.
     */
    public function getTimelineOrDefault(): array
    {
        if (!empty($this->timeline) && is_array($this->timeline)) {
            return $this->timeline;
        }

        return static::defaultTimeline();
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
