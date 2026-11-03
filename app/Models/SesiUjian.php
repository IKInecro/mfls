<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiUjian extends Model
{
    use HasFactory;

    protected $table = 'sesi_ujian';

    protected $fillable = [
        'ujian_id',
        'peserta_id',
        'mulai_pada',
        'jawaban',
        'terkunci',
        'alasan_kunci',
        'pelanggaran_count',
        'terkunci_pada',
        'dibuka_pada',
        'dibuka_oleh',
    ];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'terkunci' => 'boolean',
        'terkunci_pada' => 'datetime',
        'dibuka_pada' => 'datetime',
        'pelanggaran_count' => 'integer',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    /** Draft jawaban JSON → array. */
    public function draft(): array
    {
        return json_decode((string) $this->jawaban, true) ?: [];
    }
}
