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

        // Verify "Kirim Sertifikat Massal" is removed
        $response->assertDontSee('Kirim Sertifikat Massal');

        // Verify View Mode Toggle
        $responseDetailed = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['view' => 'detailed']));
        $responseDetailed->assertStatus(200);
        $responseDetailed->assertSee('Tampilan Rinci (S1-S5)');
        $responseDetailed->assertSee('display: table-cell;', false);
    }

    public function test_seleksi_administrasi_paginates_pendaftar_to_prevent_browser_lag()
    {
        $admin = Akun::create([
            'nama' => 'Admin Tester',
            'email' => 'admin_page@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Buat 30 pendaftar
        for ($i = 1; $i <= 30; $i++) {
            $user = Akun::create([
                'nama' => 'Peserta Ke ' . $i,
                'email' => 'peserta' . $i . '@test.com',
                'password' => Hash::make('password123'),
                'role' => 'pendaftar',
                'is_active' => true,
            ]);

            $peserta = Peserta::create([
                'akun_id' => $user->id,
                'nama' => 'Peserta Ke ' . $i,
                'nisn' => 'NISN' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'pilihan_prodi' => 'Manajemen',
                'jenis_kelamin' => 'Laki-laki',
                'nama_sekolah' => 'SMA ' . $i,
            ]);

            Daftar::create([
                'peserta_id' => $peserta->id,
                'no_wa' => '0812345678' . $i,
                'jenis_kelamin' => 'Laki-laki',
                'asal_sekolah' => 'SMA ' . $i,
                'provinsi' => 'DKI Jakarta',
                'kabupaten' => 'Jakarta Selatan',
                'status' => 'menunggu',
                'rata_rata_nilai' => 80 + ($i % 10),
                'tahun_lulus' => 2027,
                'ttl' => 'Jakarta, 01/01/2006',
            ]);
        }

        // Default 25 per page: page 1 harus memuat 25 items
        $response = $this->actingAs($admin)->get(route('admin.pendaftar.index'));
        $response->assertStatus(200);
        $pendaftars = $response->viewData('pendaftars');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $pendaftars);
        $this->assertEquals(25, $pendaftars->perPage());
        $this->assertEquals(30, $pendaftars->total());
        $this->assertEquals(25, $pendaftars->count());
        $this->assertEquals(2, $pendaftars->lastPage());

        $response->assertSee('25 per hal');
        $response->assertSee('50 per hal');
        $response->assertSee('100 per hal');

        // Test custom per_page = 50
        $response50 = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['per_page' => 50]));
        $response50->assertStatus(200);
        $pendaftars50 = $response50->viewData('pendaftars');
        $this->assertEquals(50, $pendaftars50->perPage());
        $this->assertEquals(30, $pendaftars50->count());

        // Test page 2 pada default 25 per page (sisa 5 pendaftar)
        $responsePage2 = $this->actingAs($admin)->get(route('admin.pendaftar.index', ['page' => 2]));
        $responsePage2->assertStatus(200);
        $pendaftarsPage2 = $responsePage2->viewData('pendaftars');
        $this->assertEquals(5, $pendaftarsPage2->count());
    }
}
