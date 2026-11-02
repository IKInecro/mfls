<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_modal_konfirmasi_logout_hadir_pada_halaman_admin(): void
    {
        $admin = \App\Models\Akun::create([
            'nama' => 'Admin Test',
            'email' => 'admin-modal@example.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertOk()
            ->assertSee('confirmLogout')
            ->assertSee('Konfirmasi Keluar Akun')
            ->assertSee('Keluar Sistem');
    }

    public function test_modal_konfirmasi_logout_hadir_pada_halaman_pendaftar(): void
    {
        $pendaftar = \App\Models\Akun::create([
            'nama' => 'Pendaftar Test',
            'email' => 'pendaftar-modal@example.com',
            'password' => 'password',
            'role' => 'pendaftar',
            'email_verified_at' => now(),
        ]);

        \App\Models\Peserta::create([
            'akun_id' => $pendaftar->id,
            'nisn' => '1234567890',
            'nama' => 'Pendaftar Test',
            'no_whatsapp' => '081234567890',
            'tgl_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'tahun_lulus' => 2026,
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'nama_sekolah' => 'SMAN 1 Jakarta',
            'telp_sekolah' => '0211111111',
        ]);

        $response = $this->actingAs($pendaftar)->get('/pendaftar/dashboard');
        $response->assertOk()
            ->assertSee('confirmLogout')
            ->assertSee('Konfirmasi Keluar Akun');
    }
}
