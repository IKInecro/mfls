<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Berkas;
use App\Models\Daftar;
use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeleksiAdministrasiLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_seleksi_administrasi_page()
    {
        $response = $this->get(route('admin.pendaftar.index'));
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_seleksi_administrasi_with_filters_and_counts()
    {
        $admin = Akun::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user1 = Akun::create([
            'nama' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => Hash::make('password123'),
            'role' => 'pendaftar',
            'is_active' => true,
        ]);

        $peserta1 = Peserta::create([
            'akun_id' => $user1->id,
            'nama' => 'Siti Aminah',
            'nisn' => '9988776655',
            'pilihan_prodi' => 'Desain Komunikasi Visual (DKV)',
            'jenis_kelamin' => 'Perempuan',
            'nama_sekolah' => 'SMKN 2 Bandung',
        ]);

        $daftar1 = Daftar::create([
            'peserta_id' => $peserta1->id,
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'Perempuan',
            'asal_sekolah' => 'SMKN 2 Bandung',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Kota Bandung',
            'status' => 'lulus',
            'rata_rata_nilai' => 92.5,
            'tahun_lulus' => 2026,
            'ttl' => 'Bandung, 10/05/2007',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pendaftar.index'));
        $response->assertStatus(200);
        $response->assertViewHas('counts');
        $response->assertViewHas('pendaftars');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Total Pendaftar');
        $response->assertSee('Tampilan Fleksibel');

        // Test Filter Status = lulus
        $responseLulus = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['status' => 'lulus']));
        $responseLulus->assertStatus(200);
        $responseLulus->assertSee('Siti Aminah');

        // Test Filter Status = tidak_lulus (should not show Siti Aminah)
        $responseTolak = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['status' => 'tidak_lulus']));
        $responseTolak->assertStatus(200);
        $responseTolak->assertDontSee('Siti Aminah');

        // Test Filter Prodi
        $responseDkv = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['prodi' => 'Desain Komunikasi Visual']));
        $responseDkv->assertStatus(200);
        $responseDkv->assertSee('Siti Aminah');

        $responseMgt = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['prodi' => 'Manajemen']));
        $responseMgt->assertStatus(200);
        $responseMgt->assertDontSee('Siti Aminah');
    }
}
