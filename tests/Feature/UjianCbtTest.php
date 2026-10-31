<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\JawabanUjian;
use App\Models\NilaiUjian;
use App\Models\Peserta;
use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UjianCbtTest extends TestCase
{
    use RefreshDatabase;

    private function akun(string $role): Akun
    {
        $akun = Akun::create([
            'nama' => ucfirst($role).' Tes',
            'email' => $role.'-'.uniqid().'@example.com',
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ]);

        if ($role === 'pendaftar') {
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
        }

        return $akun;
    }

    private function ujian(array $attrs = []): Ujian
    {
        return Ujian::create(array_merge([
            'nama' => 'Test CBT '.uniqid(),
            'durasi' => 60,
            'is_active' => true,
            'override_status' => 'auto',
        ], $attrs));
    }

    private function soal(Ujian $ujian, string $kunci = 'a'): Soal
    {
        return Soal::create([
            'ujian_id' => $ujian->id,
            'pertanyaan' => 'Soal tes?',
            'opsi_a' => 'Jawaban A',
            'opsi_b' => 'Jawaban B',
            'opsi_c' => 'Jawaban C',
            'opsi_d' => 'Jawaban D',
            'opsi_e' => 'Jawaban E',
            'kunci_jawaban' => $kunci,
            'bobot' => 1,
            'kategori' => 'tba',
        ]);
    }

    private function mulaiSesi(Ujian $ujian, Peserta $peserta, ?Carbon $mulai = null): SesiUjian
    {
        return SesiUjian::create([
            'ujian_id' => $ujian->id,
            'peserta_id' => $peserta->id,
            'mulai_pada' => $mulai ?? now(),
        ]);
    }

    // ---------- Panel Ujian (admin) ----------

    public function test_admin_dan_akademik_bisa_buka_panel(): void
    {
        $this->actingAs($this->akun('admin'))->get('/admin/panel-ujian')->assertOk();
        $this->actingAs($this->akun('akademik'))->get('/admin/panel-ujian')->assertOk();
    }

    public function test_panitia_dan_pendaftar_ditolak_dari_panel(): void
    {
        $this->actingAs($this->akun('panitia'))->get('/admin/panel-ujian')->assertForbidden();
        $this->actingAs($this->akun('pendaftar'))->get('/admin/panel-ujian')->assertForbidden();
    }

    public function test_buat_test_baru_dan_validasi_nama_dobel(): void
    {
        $admin = $this->akun('admin');

        $this->actingAs($admin)->post('/admin/panel-ujian', [
            'nama' => 'Test Wawasan Kebangsaan',
            'durasi' => 45,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('ujian', ['nama' => 'Test Wawasan Kebangsaan', 'durasi' => 45]);

        $this->actingAs($admin)->post('/admin/panel-ujian', [
            'nama' => 'Test Wawasan Kebangsaan',
            'durasi' => 45,
        ])->assertSessionHasErrors('nama');
    }

    public function test_jadwal_mengatur_status_buka_tutup(): void
    {
        $ujian = $this->ujian([
            'jadwal_mulai' => '2026-11-01 09:00:00',
            'jadwal_selesai' => '2026-11-01 11:00:00',
        ]);

        Carbon::setTestNow('2026-11-01 08:59:00');
        $this->assertFalse($ujian->fresh()->isOpen());

        Carbon::setTestNow('2026-11-01 10:00:00');
        $this->assertTrue($ujian->fresh()->isOpen());

        Carbon::setTestNow('2026-11-01 11:01:00');
        $this->assertFalse($ujian->fresh()->isOpen());

        Carbon::setTestNow();
    }

    public function test_override_buka_tutup_dan_kembali_ke_otomatis(): void
    {
        $ujian = $this->ujian([
            'jadwal_mulai' => '2026-11-01 09:00:00',
            'jadwal_selesai' => '2026-11-01 11:00:00',
        ]);
        $admin = $this->akun('admin');

        Carbon::setTestNow('2026-10-31 12:00:00'); // di luar jadwal
        $this->assertFalse($ujian->fresh()->isOpen());

        $this->actingAs($admin)->put("/admin/panel-ujian/{$ujian->id}/override", ['override_status' => 'buka'])
            ->assertSessionHas('success');
        $this->assertTrue($ujian->fresh()->isOpen());

        $this->actingAs($admin)->put("/admin/panel-ujian/{$ujian->id}/override", ['override_status' => 'tutup']);
        $this->assertFalse($ujian->fresh()->isOpen());

        $this->actingAs($admin)->put("/admin/panel-ujian/{$ujian->id}/override", ['override_status' => 'auto']);
        $this->assertFalse($ujian->fresh()->isOpen()); // balik ke otomatis → di luar jadwal

        Carbon::setTestNow();
    }

    public function test_hapus_test_menghapus_soal_bersamanya(): void
    {
        $ujian = $this->ujian();
        $this->soal($ujian);

        $this->actingAs($this->akun('admin'))
            ->delete("/admin/panel-ujian/{$ujian->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('ujian', ['id' => $ujian->id]);
        $this->assertDatabaseMissing('soal', ['ujian_id' => $ujian->id]);
    }

    public function test_atur_jadwal_dari_panel(): void
    {
        $ujian = $this->ujian();
        $admin = $this->akun('admin');

        $this->actingAs($admin)->put("/admin/panel-ujian/{$ujian->id}/jadwal", [
            'jadwal_mulai' => '2026-12-01 08:00',
            'jadwal_selesai' => '2026-12-01 10:00',
        ])->assertSessionHas('success');

        $this->assertSame('2026-12-01 08:00:00', $ujian->fresh()->jadwal_mulai->format('Y-m-d H:i:s'));
    }

    // ---------- Halaman peserta ----------

    public function test_kartu_ujian_mencerminkan_status(): void
    {
        $peserta = $this->akun('pendaftar');
        $tertutup = $this->ujian(['override_status' => 'tutup', 'nama' => 'Tertutup Tes']);
        $buka = $this->ujian(['nama' => 'Buka Tes']);

        $html = $this->actingAs($peserta)->get('/pendaftar/ujian')->assertOk()->getContent();

        $this->assertStringContainsString('Menunggu panitia', $html);
        $this->assertStringContainsString('Mulai Ujian', $html);
        $this->assertStringContainsString('Tertutup Tes', $html);
    }

    public function test_kerjakan_membuat_sesi_tanpa_membocorkan_kunci(): void
    {
        $peserta = $this->akun('pendaftar');
        $ujian = $this->ujian();
        $this->soal($ujian, 'a');

        $html = $this->actingAs($peserta)->get("/pendaftar/ujian/{$ujian->id}")->assertOk()->getContent();

        $this->assertStringContainsString('Soal tes?', $html);
        $this->assertStringContainsString('noselect', $html); // lapisan anti-copy aktif
        $this->assertStringNotContainsString('kunci_jawaban', $html);

        $this->assertDatabaseHas('sesi_ujian', [
            'ujian_id' => $ujian->id,
            'peserta_id' => $peserta->peserta->id,
        ]);
    }

    public function test_kerjakan_ditolak_saat_ujian_tertutup(): void
    {
        $peserta = $this->akun('pendaftar');
        $ujian = $this->ujian(['override_status' => 'tutup']);

        $this->actingAs($peserta)->get("/pendaftar/ujian/{$ujian->id}")
            ->assertRedirect(route('ujian.index'));

        $this->assertSame(0, SesiUjian::count());
    }

    public function test_draft_autosave_dan_ditolak_tanpa_sesi(): void
    {
        $peserta = $this->akun('pendaftar');
        $pesertaId = $peserta->peserta->id;
        $ujian = $this->ujian();
        $this->soal($ujian);

        // belum mulai → 403
        $this->actingAs($peserta)
            ->postJson("/pendaftar/ujian/{$ujian->id}/draft", ['jawaban' => ['1' => 'a']])
            ->assertForbidden();

        $this->mulaiSesi($ujian, $peserta->peserta);

        $this->actingAs($peserta)
            ->postJson("/pendaftar/ujian/{$ujian->id}/draft", ['jawaban' => ['1' => 'a', '2' => 'b']])
            ->assertOk();

        $this->assertSame('{"1":"a","2":"b"}', SesiUjian::first()->jawaban);
    }

    public function test_draft_ditolak_setelah_waktu_habis(): void
    {
        $peserta = $this->akun('pendaftar');
        $ujian = $this->ujian(['durasi' => 60]);
        $this->mulaiSesi($ujian, $peserta->peserta, now()->subMinutes(90));

        $this->actingAs($peserta)
            ->postJson("/pendaftar/ujian/{$ujian->id}/draft", ['jawaban' => ['1' => 'a']])
            ->assertStatus(422);
    }

    public function test_submit_final_menghitung_nilai_dan_menutup_sesi(): void
    {
        $peserta = $this->akun('pendaftar');
        $pesertaId = $peserta->peserta->id;
        $ujian = $this->ujian();
        $this->soal($ujian, 'a');
        $this->mulaiSesi($ujian, $peserta->peserta);

        $this->actingAs($peserta)
            ->postJson("/pendaftar/ujian/{$ujian->id}/submit", ['answers' => ['1' => 'a']])
            ->assertOk()
            ->assertJsonPath('results.score', 100);

        $this->assertSame(1, JawabanUjian::where('ujian_id', $ujian->id)->where('peserta_id', $pesertaId)->count());
        $this->assertSame(100.0, (float) NilaiUjian::where('ujian_id', $ujian->id)->value('skor_rata'));
        $this->assertSame(0, SesiUjian::count()); // sesi dibersihkan

        // status kartu → selesai + nilai; halaman kerjakan dibalikkan
        $html = $this->actingAs($peserta)->get('/pendaftar/ujian')->getContent();
        $this->assertStringContainsString('Sudah dikerjakan', $html);

        $this->actingAs($peserta)->get("/pendaftar/ujian/{$ujian->id}")
            ->assertRedirect(route('ujian.index'));
    }

    public function test_submit_ditolak_saat_ujian_tertutup_tanpa_sesi(): void
    {
        $peserta = $this->akun('pendaftar');
        $ujian = $this->ujian(['override_status' => 'tutup']);
        $this->soal($ujian);

        $this->actingAs($peserta)
            ->postJson("/pendaftar/ujian/{$ujian->id}/submit", ['answers' => ['1' => 'a']])
            ->assertForbidden();

        $this->assertSame(0, JawabanUjian::count());
    }

    // ---------- Gate API (app external) ----------

    public function test_api_ujian_tertutup_tidak_muncul_dan_soal_ditolak(): void
    {
        $peserta = $this->akun('pendaftar');
        $token = $peserta->createToken('tes')->plainTextToken;

        $buka = $this->ujian(['nama' => 'API Buka Tes']);
        $tutup = $this->ujian(['nama' => 'API Tutup Tes', 'override_status' => 'tutup']);
        $this->soal($tutup);

        $daftar = $this->withToken($token)->getJson('/api/ujians')->assertOk()->json('data');
        $nama = array_column($daftar, 'nama');
        $this->assertContains('API Buka Tes', $nama);
        $this->assertNotContains('API Tutup Tes', $nama);

        $this->withToken($token)->getJson("/api/soal?ujian_id={$tutup->id}")->assertForbidden();

        // peserta yang sesinya sudah jalan tetap boleh ambil soal (resume)
        $this->mulaiSesi($tutup, $peserta->peserta);
        $this->withToken($token)->getJson("/api/soal?ujian_id={$tutup->id}")->assertOk();
    }

    public function test_sisa_detik_dibatasi_jadwal_selesai_dan_dispensasi(): void
    {
        $ujian = $this->ujian([
            'durasi' => 60,
            'jadwal_mulai' => '2026-11-01 09:00:00',
            'jadwal_selesai' => '2026-11-01 09:30:00',
        ]);
        $peserta = $this->akun('pendaftar')->peserta;
        $sesi = $this->mulaiSesi($ujian, $peserta, Carbon::parse('2026-11-01 09:00:00'));

        Carbon::setTestNow('2026-11-01 09:10:00');
        // durasi 60m tapi jadwal selesai 09:30 → sisa maksimal 20 menit
        $this->assertSame(20 * 60, $ujian->fresh()->sisaDetik($sesi->fresh()));

        Carbon::setTestNow('2026-11-01 09:31:00');
        $this->assertSame(0, $ujian->fresh()->sisaDetik($sesi->fresh()));

        Carbon::setTestNow();
    }
}
