<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'akun_id',
        'nisn',
        'nama',
        'no_whatsapp',
        'tgl_lahir',
        'jenis_kelamin',
        'tahun_lulus',
        'provinsi',
        'kabupaten',
        'nama_sekolah',
        'telp_sekolah',
        'no_guru_bk',
        'link_ig',
        'link_tiktok',
        'link_tiktok',
        'link_twibbon',
        'pilihan_prodi',
        'status_seleksi_ujian',
        'is_email_dikirim',
        'interviewer_id',
        'ruangan',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id');
    }

    public function daftar()
    {
        return $this->hasOne(Daftar::class, 'peserta_id');
    }

    public function berkas()
    {
        return $this->hasOne(Berkas::class, 'peserta_id');
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class, 'peserta_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'peserta_id');
    }

    public function jawabanUjians()
    {
        return $this->hasMany(JawabanUjian::class, 'peserta_id');
    }

    public function nilaiUjians()
    {
        return $this->hasMany(NilaiUjian::class, 'peserta_id');
    }

    public function penilaianMentors()
    {
        return $this->hasMany(PenilaianMentor::class, 'peserta_id');
    }

    public function penilaianAkademiks()
    {
        return $this->hasMany(PenilaianAkademik::class, 'peserta_id');
    }

    public function keputusan()
    {
        return $this->hasOne(Keputusan::class, 'peserta_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(Akun::class, 'interviewer_id');
    }

    /**
     * Calculate registration progress percentage (0-100)
     */
    public function getProgressAttribute(): int
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        // 1. Biodata Points
        $biodataFields = ['nama', 'nisn', 'no_whatsapp', 'tgl_lahir', 'jenis_kelamin', 'provinsi', 'kabupaten', 'nama_sekolah', 'tahun_lulus'];
        $totalPoints += count($biodataFields);
        foreach ($biodataFields as $field) {
            if (!empty($this->$field)) $earnedPoints++;
        }

        // 2. Determine Max Semester (Alumni: 6, 2027: 5)
        $tahunLulus = (int) ($this->tahun_lulus ?? 2027);
        $maxSemester = ($tahunLulus < 2027) ? 6 : 5;

        // 3. Berkas Points (Comprehensive & Dynamic)
        $berkas = $this->berkas;
        $berkasFields = [
            'foto', 'personal_statement', 'study_plan', 'motivasi_video',
            'bukti_follow_ig_beasiswamncu', 'bukti_follow_ig_mncu', 
            'bukti_follow_tiktok_beasiswamncu', 'bukti_follow_tiktok_mncu'
        ];
        
        // Add Rapor scan mandatory fields
        for ($i = 1; $i <= $maxSemester; $i++) {
            $berkasFields[] = 'rapor' . $i;
        }

        // Ijazah/SKL/Kartu Pelajar is Mandatory for everyone
        $berkasFields[] = 'ijazah';

        // Conditional: New Graduates 2027 must upload School Recommendation
        if ($tahunLulus >= 2027) {
            $berkasFields[] = 'surat_rekomendasi_sekolah';
        }

        // Conditional: DKV must upload Color Blindness Certificate & Portfolio
        if (str_contains($this->pilihan_prodi ?? '', 'Desain Komunikasi Visual')) {
            $berkasFields[] = 'surat_buta_warna';
            $berkasFields[] = 'portfolio';
        }

        $totalPoints += count($berkasFields);
        if ($berkas) {
            foreach ($berkasFields as $field) {
                if (!empty($berkas->$field)) $earnedPoints++;
            }
        }

        // 4. Grades (Nilai) Input Points
        // We add 1 point per mandatory semester if grades are filled
        $totalPoints += $maxSemester;
        $nilaisGrouped = $this->nilais->groupBy('semester');
        for ($i = 1; $i <= $maxSemester; $i++) {
            if (isset($nilaisGrouped[$i]) && $nilaisGrouped[$i]->count() >= 3) {
                $earnedPoints++;
            }
        }

        // 5. Sosmed/Twibbon Points
        $sosmedFields = ['link_twibbon']; 
        $totalPoints += count($sosmedFields);
        foreach ($sosmedFields as $field) {
            if (!empty($this->$field)) $earnedPoints++;
        }

        return ($totalPoints > 0) ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
    }
}
