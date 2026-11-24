<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Berkas;
use App\Models\Daftar;
use App\Models\Peserta;
use App\Models\Sertifikat;
use App\Models\PengaturanPendaftaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BerkasPendaftarMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        PengaturanPendaftaran::instance()->update(['mode' => 'buka']);
    }

    private function createPendaftar($email = 'peserta@test.com', $nama = 'Budi Santoso', $prodi = 'Sistem Informasi', $isLengkap = false, $nisn = null)
    {
        static $counter = 1000;
        $counter++;
        $nisn = $nisn ?? ('00' . $counter);

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
            'nisn' => $nisn,
            'pilihan_prodi' => $prodi,
            'tahun_lulus' => 2027,
            'no_hp' => '081234567890',
            'no_whatsapp' => '081234567890',
            'nama_sekolah' => 'SMAN 1 Jakarta',
        ]);

        $daftar = Daftar::create([
            'peserta_id' => $peserta->id,
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'tahun_lulus' => 2027,
            'ttl' => 'Jakarta, 01-01-2006',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'asal_sekolah' => 'SMAN 1 Jakarta',
            'rata_rata_nilai' => 88.5,
            'status' => 'menunggu',
        ]);

        $berkasData = [
            'peserta_id' => $peserta->id,
        ];

        if ($isLengkap) {
            $berkasData['foto'] = 'berkas/foto.jpg';
            $berkasData['rapor1'] = 'berkas/rapor1.pdf';
            $berkasData['rapor2'] = 'berkas/rapor2.pdf';
            $berkasData['rapor3'] = 'berkas/rapor3.pdf';
            $berkasData['rapor4'] = 'berkas/rapor4.pdf';
            $berkasData['rapor5'] = 'berkas/rapor5.pdf';
            $berkasData['ijazah'] = 'berkas/ijazah.pdf';
            $berkasData['personal_statement'] = 'berkas/ps.pdf';
            $berkasData['study_plan'] = 'berkas/sp.pdf';
            $berkasData['surat_rekomendasi_sekolah'] = 'berkas/rekom.pdf';
        }

        $berkas = Berkas::create($berkasData);

        return [$akun, $peserta, $berkas, $daftar];
    }

    private function createAdmin($role = 'admin')
    {
        return Akun::create([
            'nama' => 'Admin User',
            'email' => 'admin_' . $role . '@test.com',
            'password' => Hash::make('password123'),
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    public function test_guest_cannot_access_berkas_monitoring()
    {
        $response = $this->get(route('admin.berkas.index'));
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_berkas_monitoring()
    {
        $mentor = $this->createAdmin('mentor');
        $response = $this->actingAs($mentor)->get(route('admin.berkas.index'));
        $response->assertStatus(403);

        $dosen = $this->createAdmin('dosen');
        $response = $this->actingAs($dosen)->get(route('admin.berkas.index'));
        $response->assertStatus(403);
    }

    public function test_admin_and_authorized_roles_can_access_berkas_monitoring()
    {
        $this->createPendaftar('lengkap@test.com', 'Siti Lengkap', 'Sains Komunikasi', true);
        $this->createPendaftar('belum@test.com', 'Andi Belum', 'Manajemen', false);

        foreach (['god', 'admin', 'panitia', 'akademik', 'palugada'] as $role) {
            $user = $this->createAdmin($role);
            $response = $this->actingAs($user)->get(route('admin.berkas.index'));
            $response->assertStatus(200);
            $response->assertSee('Berkas & Dokumen Pendaftar', false);
            $response->assertSee('Siti Lengkap');
            $response->assertSee('Andi Belum');
            $response->assertSee('Total Pendaftar');
            $response->assertSee('Berkas Lengkap');
            $response->assertSee('Belum Lengkap');
        }
    }

    public function test_admin_can_filter_berkas_by_search_keyword()
    {
        $this->createPendaftar('budi@test.com', 'Budi Hartono', 'Sistem Informasi', true);
        $this->createPendaftar('clara@test.com', 'Clara Shinta', 'Akuntansi', false);

        $admin = $this->createAdmin('admin');

        $response = $this->actingAs($admin)->get(route('admin.berkas.index', ['search' => 'Budi']));
        $response->assertStatus(200);
        $response->assertSee('Budi Hartono');
        $response->assertDontSee('Clara Shinta');
    }

    public function test_admin_can_filter_berkas_by_prodi()
    {
        $this->createPendaftar('dkv@test.com', 'Doni DKV', 'Desain Komunikasi Visual (DKV)', false);
        $this->createPendaftar('manajemen@test.com', 'Maya Manajemen', 'Manajemen', true);

        $admin = $this->createAdmin('admin');

        $response = $this->actingAs($admin)->get(route('admin.berkas.index', ['prodi' => 'Desain Komunikasi Visual (DKV)']));
        $response->assertStatus(200);
        $response->assertSee('Doni DKV');
        $response->assertDontSee('Maya Manajemen');
    }

    public function test_admin_can_filter_berkas_by_completeness_status()
    {
        $this->createPendaftar('lengkap@test.com', 'Peserta Lengkap', 'Ilmu Komputer', true);
        $this->createPendaftar('belum@test.com', 'Peserta Kosong', 'Ilmu Komputer', false);

        $admin = $this->createAdmin('admin');

        // Test filter lengkap
        $responseLengkap = $this->actingAs($admin)->get(route('admin.berkas.index', ['status' => 'lengkap']));
        $responseLengkap->assertStatus(200);
        $responseLengkap->assertSee('Peserta Lengkap');
        $responseLengkap->assertDontSee('Peserta Kosong');

        // Test filter belum lengkap
        $responseBelum = $this->actingAs($admin)->get(route('admin.berkas.index', ['status' => 'belum_lengkap']));
        $responseBelum->assertStatus(200);
        $responseBelum->assertSee('Peserta Kosong');
        $responseBelum->assertDontSee('Peserta Lengkap');
    }

    public function test_admin_can_filter_berkas_by_sertifikat()
    {
        [$akun1, $peserta1] = $this->createPendaftar('sertif@test.com', 'Juara Satu', 'Manajemen', false);
        Sertifikat::create([
            'peserta_id' => $peserta1->id,
            'nama' => 'Juara 1 LKS Nasional',
            'file' => 'sertifikat/lks.pdf',
            'tahun' => 2024,
        ]);

        $this->createPendaftar('nosertif@test.com', 'Bambang Nihil Sertif', 'Manajemen', false);

        $admin = $this->createAdmin('admin');

        $response = $this->actingAs($admin)->get(route('admin.berkas.index', ['status' => 'ada_sertifikat']));
        $response->assertStatus(200);
        $response->assertSee('Juara Satu');
        $response->assertDontSee('Bambang Nihil Sertif');
    }

    public function test_modal_previewer_and_sidebar_link_present()
    {
        $this->createPendaftar('peserta@test.com', 'Test User', 'Sistem Informasi', true);
        $admin = $this->createAdmin('admin');

        $response = $this->actingAs($admin)->get(route('admin.berkas.index'));
        $response->assertStatus(200);

        // Assert preview modal elements are rendered
        $response->assertSee('id="berkas-preview-modal"', false);
        $response->assertSee('id="viewer-pdf-iframe"', false);
        $response->assertSee('id="viewer-image-element"', false);
        $response->assertSee('id="modal-doc-tabs-bar"', false);

        // Assert sidebar has the link
        $response->assertSee(route('admin.berkas.index'));
        $response->assertSee('Berkas Pendaftar');
    }
}
