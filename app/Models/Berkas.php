<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    protected $table = 'berkas';

    protected $fillable = [
        'peserta_id',
        'foto',
        'rapor1',
        'rapor2',
        'rapor3',
        'rapor4',
        'rapor5',
        'rapor6',
        'ijazah',
        'surat_rekom',
        'personal_statement',
        'study_plan',
        'surat_rekomendasi_sekolah',
        'motivasi_video',
        'motivasi_video_tiktok',
        'surat_buta_warna',
        'bukti_follow_ig_beasiswamncu',
        'bukti_follow_ig_mncu',
        'bukti_follow_ig_mncunation',
        'bukti_follow_ig_thearetegen',
        'bukti_follow_tiktok_beasiswamncu',
        'bukti_follow_tiktok_mncu',
        'portfolio',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }
}
