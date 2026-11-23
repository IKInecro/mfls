<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Berkas;
use App\Models\Daftar;
use App\Models\Peserta;
use App\Models\PengaturanPendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VideoMotivasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        PengaturanPendaftaran::instance()->update(['mode' => 'buka']);
    }

    private function createPendaftar($email = 'peserta@test.com', $nama = 'Peserta Uji')
    {
        $akun = Akun::create([
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'pendaftar',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $peserta = Peserta::create([
            'akun_id' => $akun->id,
            'nama' => $nama,
            'email' => $email,
            'pilihan_prodi' => 'Sistem Informasi | Ilmu Komputer',
            'tahun_lulus' => 2026,
            'no_hp' => '081234567890',
            'no_whatsapp' => '081234567890',
        ]);

        $daftar = Daftar::create([
            'peserta_id' => $peserta->id,
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'tahun_lulus' => 2026,
            'ttl' => 'Jakarta, 01-01-2005',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'asal_sekolah' => 'SMAN 1 Jakarta',
            'rata_rata_nilai' => 88.5,
            'status' => 'menunggu',
        ]);

        $berkas = Berkas::create([
            'peserta_id' => $peserta->id,
        ]);

        return [$akun, $peserta, $berkas];
    }

    private function createAdmin($role = 'admin')
    {
        return Akun::create([
            'nama' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_pendaftar_can_save_video_motivasi_links()
    {
        [$akun, $peserta, $berkas] = $this->createPendaftar();

        $response = $this->actingAs($akun)->post(route('pendaftar.berkas.store'), [
            'upload_field' => 'motivasi_video',
            'motivasi_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'motivasi_video_tiktok' => 'tiktok.com/@mncu/video/123456789',
        ]);

        $response->assertRedirect();
        
        $berkas->refresh();
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $berkas->motivasi_video);
        // Note: auto prepends https:// if missing
        $this->assertEquals('https://tiktok.com/@mncu/video/123456789', $berkas->motivasi_video_tiktok);
    }

    public function test_pendaftar_can_delete_video_motivasi_field()
    {
        [$akun, $peserta, $berkas] = $this->createPendaftar();
        $berkas->update([
            'motivasi_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'motivasi_video_tiktok' => 'https://www.tiktok.com/@mncu/video/123',
        ]);

        $response = $this->actingAs($akun)->post(route('pendaftar.berkas.delete_file'), [
            'field' => 'motivasi_video',
        ]);

        $response->assertRedirect();

        $berkas->refresh();
        $this->assertNull($berkas->motivasi_video);
        $this->assertEquals('https://www.tiktok.com/@mncu/video/123', $berkas->motivasi_video_tiktok);
    }

    public function test_admin_can_view_video_motivasi_index_page()
    {
        $admin = $this->createAdmin();
        [$akun, $peserta, $berkas] = $this->createPendaftar('budi@test.com', 'Budi Santoso');
        $berkas->update([
            'motivasi_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'motivasi_video_tiktok' => 'https://www.tiktok.com/@budi/video/999',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.video_motivasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Submission Video Motivasi');
        $response->assertSee('Budi Santoso');
        $response->assertSee('YouTube');
        $response->assertSee('TikTok');
    }

    public function test_admin_can_filter_video_motivasi_by_status()
    {
        $admin = $this->createAdmin();
        
        // Peserta 1: Sudah submit
        [$akun1, $peserta1, $berkas1] = $this->createPendaftar('user1@test.com', 'Calon Ada Video');
        $berkas1->update([
            'motivasi_video' => 'https://www.youtube.com/watch?v=123',
        ]);

        // Peserta 2: Belum submit
        [$akun2, $peserta2, $berkas2] = $this->createPendaftar('user2@test.com', 'Calon Tanpa Video');

        // Test filter sudah
        $responseSudah = $this->actingAs($admin)->get(route('admin.video_motivasi.index', ['status' => 'sudah']));
        $responseSudah->assertStatus(200);
        $responseSudah->assertSee('Calon Ada Video');
        $responseSudah->assertDontSee('Calon Tanpa Video');

        // Test filter belum
        $responseBelum = $this->actingAs($admin)->get(route('admin.video_motivasi.index', ['status' => 'belum']));
        $responseBelum->assertStatus(200);
        $responseBelum->assertSee('Calon Tanpa Video');
        $responseBelum->assertDontSee('Calon Ada Video');
    }

    public function test_non_admin_cannot_access_video_motivasi_admin_page()
    {
        [$pendaftar] = $this->createPendaftar();

        $response = $this->actingAs($pendaftar)->get(route('admin.video_motivasi.index'));
        $response->assertStatus(403);
    }
}
