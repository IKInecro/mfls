<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class Akun extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'akun';

    protected $fillable = [
        'nama',
        'email',
        'email_verified_at',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function peserta()
    {
        return $this->hasOne(Peserta::class, 'akun_id');
    }

    public function penilaianMentor()
    {
        return $this->hasMany(PenilaianMentor::class, 'mentor_id');
    }

    public function keputusanAkademik()
    {
        return $this->hasMany(Keputusan::class, 'akademik_id');
    }

    public function riwayatAktivitas()
    {
        return $this->hasMany(RiwayatAktivitas::class, 'pelaku_id');
    }
}
