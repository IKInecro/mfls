<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Berkas;
use App\Models\Daftar;
use App\Models\Matpel;
use App\Models\Nilai;
use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_export_seleksi_administrasi()
    {
        $response = $this->get(route('admin.export'));
        $response->assertRedirect('/login');
    }

    public function test_admin_can_export_seleksi_administrasi_with_content_length_and_full_columns()
    {
        $admin = Akun::create([
            'nama' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user = Akun::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'pendaftar',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $peserta = Peserta::create([
            'akun_id' => $user->id,
            'nama' => 'Budi Santoso',
            'nisn' => '1234567890',
            'pilihan_prodi' => 'Desain Komunikasi Visual (DKV)',
            'pilihan_prodi2' => 'Manajemen',
            'jenis_kelamin' => 'Laki-laki',
            'no_whatsapp' => '08123456789',
            'nama_sekolah' => 'SMAN 1 Jakarta',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'link_ig' => 'https://instagram.com/budi',
            'link_tiktok' => 'https://tiktok.com/@budi',
            'link_twibbon' => 'https://twibbon.com/budi',
        ]);

        $daftar = Daftar::create([
            'peserta_id' => $peserta->id,
            'no_wa' => '08123456789',
            'jenis_kelamin' => 'Laki-laki',
            'tahun_lulus' => 2026,
            'ttl' => 'Jakarta, 01/01/2007',
            'asal_sekolah' => 'SMAN 1 Jakarta',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'status' => 'lulus',
            'kode_referral' => 'REF123',
            'sumber_informasi' => 'Instagram',
            'rata_rata_nilai' => 88.5,
            'avg_semester_1' => 87.0,
            'avg_semester_2' => 88.0,
            'avg_semester_3' => 89.0,
            'avg_semester_4' => 89.5,
            'avg_semester_5' => 89.0,
        ]);

        $berkas = Berkas::create([
            'peserta_id' => $peserta->id,
            'motivasi_video' => 'https://youtube.com/watch?v=abcd123',
            'motivasi_video_tiktok' => 'https://tiktok.com/@budi/video/123',
            'surat_buta_warna' => 'berkas/buta_warna_budi.pdf',
            'portfolio' => 'https://drive.google.com/budi_portfolio',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.export'));

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('Content-Length'));
        $this->assertGreaterThan(0, (int) $response->headers->get('Content-Length'));
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Database_Seleksi_Administrasi_', $response->headers->get('Content-Disposition'));

        $content = file_get_contents($response->getFile()->getPathname());
        // Check for UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        // Check required columns in header
        $this->assertStringContainsString('Status Seleksi Administrasi', $content);
        $this->assertStringContainsString('Progres Berkas', $content);
        $this->assertStringContainsString('Tanggal Pendaftaran', $content);
        $this->assertStringContainsString('LINK VIDEO MOTIVASI', $content);
        $this->assertStringContainsString('LINK VIDEO MOTIVASI TIKTOK', $content);
        $this->assertStringContainsString('SURAT BUTA WARNA (DKV)', $content);
        $this->assertStringContainsString('PORTOFOLIO (DKV)', $content);

        // Check data values in rows
        $this->assertStringContainsString('Budi Santoso', $content);
        $this->assertStringContainsString('budi@example.com', $content);
        $this->assertStringContainsString('LULUS', $content);
        $this->assertStringContainsString('JABODETABEK', $content);
        $this->assertStringContainsString('https://youtube.com/watch?v=abcd123', $content);
        $this->assertStringContainsString('https://tiktok.com/@budi/video/123', $content);
        $this->assertStringContainsString('https://drive.google.com/budi_portfolio', $content);
    }

    public function test_export_with_custom_comma_delimiter()
    {
        $admin = Akun::create([
            'nama' => 'Admin Test',
            'email' => 'admin2@test.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.export', ['delimiter' => ',']));

        $response->assertStatus(200);
        $content = file_get_contents($response->getFile()->getPathname());
        $this->assertStringContainsString('"Status Seleksi Administrasi","Progres Berkas"', $content);
    }
}
