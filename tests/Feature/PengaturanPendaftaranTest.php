<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\PengaturanPendaftaran;
use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanPendaftaranTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Akun
    {
        return Akun::create([
            'nama' => 'Admin Tes',
            'email' => 'admin-tes@example.com',
            'password' => 'password',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    private function pendaftar(): Akun
    {
        $akun = Akun::create([
            'nama' => 'Pendaftar Tes',
            'email' => 'pendaftar-tes@example.com',
            'password' => 'password',
            'role' => 'pendaftar',
            'email_verified_at' => now(),
        ]);

        Peserta::create([
            'akun_id' => $akun->id,
            'nisn' => '0012345678',
            'nama' => 'Pendaftar Tes',
            'no_whatsapp' => '081234567890',
            'tgl_lahir' => '2005-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'tahun_lulus' => 2026,
            'provinsi' => 'Jawa Timur',
            'kabupaten' => 'Surabaya',
            'nama_sekolah' => 'SMAN 1 Surabaya',
            'telp_sekolah' => '0311111111',
        ]);

        return $akun;
    }

    // --- Gerbang default: tertutup (sama seperti perilaku sebelum fitur) ---

    public function test_default_tertutup_register_dan_homepage(): void
    {
        $this->get('/register')->assertRedirect('/');
        $this->get('/')->assertSee('Pendaftaran Ditutup');
        $this->assertGuest();
    }

    public function test_default_tertutup_pendaftar_tidak_bisa_update(): void
    {
        $this->actingAs($this->pendaftar())
            ->post('/pendaftar/biodata', ['nama' => 'Nama Baru'])
            ->assertSessionHas('error');

        $this->assertSame('Pendaftar Tes', \App\Models\Peserta::find(1)->nama);
    }

    // --- Gerbang 1: jendela waktu ---

    public function test_register_terbuka_saatsedang_jendela(): void
    {
        PengaturanPendaftaran::instance()->update([
            'pendaftaran_mulai' => now()->subHour(),
            'pendaftaran_selesai' => now()->addHour(),
        ]);

        $this->get('/register')->assertOk();
        $this->get('/')->assertSee('Pendaftaran Dibuka');
    }

    public function test_register_tertutup_sebelum_jendela(): void
    {
        PengaturanPendaftaran::instance()->update([
            'pendaftaran_mulai' => now()->addDay(),
            'pendaftaran_selesai' => now()->addDays(7),
        ]);

        $this->get('/register')->assertRedirect('/');
    }

    public function test_register_tertutup_setelah_jendela(): void
    {
        PengaturanPendaftaran::instance()->update([
            'pendaftaran_mulai' => now()->subDays(7),
            'pendaftaran_selesai' => now()->subHour(),
        ]);

        $this->get('/register')->assertRedirect('/');
    }

    // --- Override manual menang atas jadwal ---

    public function test_override_buka_meski_tidak_ada_jadwal(): void
    {
        PengaturanPendaftaran::instance()->update(['mode' => 'buka']);

        $this->get('/register')->assertOk();
    }

    public function test_override_tutup_meski_jendela_terbuka(): void
    {
        PengaturanPendaftaran::instance()->update([
            'pendaftaran_mulai' => now()->subHour(),
            'pendaftaran_selesai' => now()->addHour(),
            'mode' => 'tutup',
        ]);

        $this->get('/register')->assertRedirect('/');
    }

    // --- Gerbang 2: aksi pendaftar ---

    public function test_pendaftar_bisa_update_biodata_saatsedang_buka(): void
    {
        PengaturanPendaftaran::instance()->update(['mode' => 'buka']);
        $akun = $this->pendaftar();

        $this->actingAs($akun)->post('/pendaftar/biodata', [
            'nama' => 'Pendaftar Tes Baru',
            'nisn' => '0012345678',
            'no_whatsapp' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'tgl_lahir' => '2005-01-01',
            'provinsi' => 'Jawa Timur',
            'kabupaten' => 'Surabaya',
            'nama_sekolah' => 'SMAN 1 Surabaya',
            'tahun_lulus' => 2026,
        ])->assertSessionHas('success');

        $this->assertSame('Pendaftar Tes Baru', $akun->fresh()->nama);
    }

    // --- Halaman admin ---

    public function test_admin_bisa_buka_halaman_pengaturan(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/pengaturan/pendaftaran')
            ->assertOk();
    }

    public function test_tamu_diredirect_ke_login(): void
    {
        $this->get('/admin/pengaturan/pendaftaran')->assertRedirect('/login');
    }

    public function test_admin_bisa_simpan_pengaturan(): void
    {
        $this->actingAs($this->admin())->post('/admin/pengaturan/pendaftaran', [
            'pendaftaran_mulai' => '2026-12-01T08:00',
            'pendaftaran_selesai' => '2026-12-31T23:59',
            'mode' => 'auto',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('pengaturan_pendaftaran', [
            'pendaftaran_mulai' => '2026-12-01 08:00:00',
            'pendaftaran_selesai' => '2026-12-31 23:59:00',
            'mode' => 'auto',
        ]);
    }

    public function test_simpan_wajib_isi_semua_field(): void
    {
        $this->actingAs($this->admin())->post('/admin/pengaturan/pendaftaran', [
            'mode' => 'buka_paksa', // mode tidak valid + tanggal kosong
        ])->assertSessionHasErrors(['pendaftaran_mulai', 'pendaftaran_selesai', 'mode']);
    }

    public function test_admin_bisa_akses_halaman_timeline(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/pengaturan/timeline')
            ->assertOk()
            ->assertSee('Linimasa Seleksi Batch 3')
            ->assertSee('Pratinjau Langsung');
    }

    public function test_admin_bisa_simpan_dan_update_timeline(): void
    {
        $admin = $this->admin();

        $steps = [
            [
                'step' => '01',
                'title' => 'Tahap Uji Coba Kustom',
                'date' => '01 - 10 Juni 2026',
                'desc' => 'Deskripsi tahapan uji coba kustom admin',
                'status' => 'Sedang Berlangsung',
            ],
            [
                'step' => '02',
                'title' => 'Pengumuman Final Kustom',
                'date' => '25 Juni 2026',
                'desc' => 'Deskripsi pengumuman final',
                'status' => 'Mendatang',
            ]
        ];

        $response = $this->actingAs($admin)->post('/admin/pengaturan/timeline', [
            'steps' => $steps,
            'active_index' => 0,
        ]);

        $response->assertSessionHas('success');

        $pengaturan = PengaturanPendaftaran::instance();
        $this->assertCount(2, $pengaturan->timeline);
        $this->assertEquals('Tahap Uji Coba Kustom', $pengaturan->timeline[0]['title']);
        $this->assertTrue($pengaturan->timeline[0]['active']);
        $this->assertFalse($pengaturan->timeline[1]['active']);

        // Check landing page renders this updated timeline
        $this->get('/')
            ->assertOk()
            ->assertSee('Tahap Uji Coba Kustom')
            ->assertSee('Pengumuman Final Kustom');
    }

    public function test_admin_bisa_reset_timeline_ke_default(): void
    {
        $admin = $this->admin();

        // Save custom
        $this->actingAs($admin)->post('/admin/pengaturan/timeline', [
            'steps' => [
                [
                    'step' => '99',
                    'title' => 'Tahap Khusus',
                    'date' => '31 Desember 2026',
                    'desc' => 'Testing',
                    'status' => 'Mendatang',
                ]
            ],
        ]);

        // Reset
        $this->actingAs($admin)->post('/admin/pengaturan/timeline/reset')
            ->assertSessionHas('success');

        $pengaturan = PengaturanPendaftaran::instance();
        $this->assertCount(7, $pengaturan->timeline);
        $this->assertEquals('Pembukaan Pendaftaran Akun', $pengaturan->timeline[0]['title']);
    }

    public function test_homepage_tidak_ada_tawk_to_dan_memiliki_widget_bantuan_mandiri(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        // Pastikan script Tawk.to ("we are here") sudah bersih
        $response->assertDontSee('embed.tawk.to');

        // Pastikan widget helpdesk mandiri ada
        $response->assertSee('mfls-help-widget');
        $response->assertSee('Helpdesk MFLS Batch 3');
        $response->assertSee('Butuh Bantuan?');
    }
}
