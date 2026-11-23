<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Daftar;
use App\Models\Matpel;
use App\Models\Nilai;
use App\Models\PengaturanPendaftaran;
use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NilaiRaporTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        PengaturanPendaftaran::instance()->update(['mode' => 'buka']);
    }

    private function createPendaftar($email = 'peserta_nilai@test.com', $nama = 'Budi Nilai')
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
            'rata_rata_nilai' => 0,
            'status' => 'menunggu',
        ]);

        // Pastikan core matpel ada
        $matpelIndo = Matpel::firstOrCreate(['nama' => 'Bahasa Indonesia']);
        $matpelMtk = Matpel::firstOrCreate(['nama' => 'Matematika Wajib']);
        $matpelIng = Matpel::firstOrCreate(['nama' => 'Bahasa Inggris']);

        return [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]];
    }

    public function test_pendaftar_can_save_nilai_with_decimals_and_commas()
    {
        [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]] = $this->createPendaftar();

        $response = $this->actingAs($akun)->post(route('pendaftar.nilai.store'), [
            'nilai' => [
                $matpelIndo->id => [1 => '85.50', 2 => '87,25'],
                $matpelMtk->id => [1 => '90', 2 => '92,50'],
                $matpelIng->id => [1 => '88', 2 => '89'],
            ],
            'custom_matpel' => [
                1 => [
                    1 => ['nama' => 'Fisika', 'nilai' => '84,75'],
                    2 => ['nama' => 'Kimia', 'nilai' => '86.50'],
                ]
            ]
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verifikasi nilai tersimpan dengan format desimal yang benar
        $nilaiIndoS1 = Nilai::where('peserta_id', $peserta->id)->where('matpel_id', $matpelIndo->id)->where('semester', 1)->first();
        $this->assertNotNull($nilaiIndoS1);
        $this->assertEquals(85.50, (float) $nilaiIndoS1->nilai);

        $nilaiIndoS2 = Nilai::where('peserta_id', $peserta->id)->where('matpel_id', $matpelIndo->id)->where('semester', 2)->first();
        $this->assertNotNull($nilaiIndoS2);
        $this->assertEquals(87.25, (float) $nilaiIndoS2->nilai);

        // Verifikasi mapel pendukung
        $fisika = Matpel::where('nama', 'Fisika')->first();
        $this->assertNotNull($fisika);
        $nilaiFisika = Nilai::where('peserta_id', $peserta->id)->where('matpel_id', $fisika->id)->where('semester', 1)->first();
        $this->assertNotNull($nilaiFisika);
        $this->assertEquals(84.75, (float) $nilaiFisika->nilai);

        // Verifikasi rata-rata terupdate pada model Daftar
        $daftar->refresh();
        $this->assertGreaterThan(0, $daftar->rata_rata_nilai);
    }

    public function test_pendaftar_can_clear_previously_saved_score()
    {
        [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]] = $this->createPendaftar();

        Nilai::create([
            'peserta_id' => $peserta->id,
            'matpel_id' => $matpelIndo->id,
            'semester' => 1,
            'nilai' => 88,
        ]);

        $response = $this->actingAs($akun)->post(route('pendaftar.nilai.store'), [
            'nilai' => [
                $matpelIndo->id => [1 => ''], // Dikosongkan
            ],
            'custom_matpel' => [
                1 => [
                    1 => ['nama' => 'Fisika', 'nilai' => '85'],
                    2 => ['nama' => 'Kimia', 'nilai' => '85'],
                ]
            ]
        ]);

        $response->assertRedirect();

        $nilaiIndoS1 = Nilai::where('peserta_id', $peserta->id)->where('matpel_id', $matpelIndo->id)->where('semester', 1)->first();
        $this->assertNull($nilaiIndoS1);
    }

    public function test_reject_when_custom_subject_has_name_but_empty_score()
    {
        [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]] = $this->createPendaftar();

        $response = $this->actingAs($akun)->post(route('pendaftar.nilai.store'), [
            'custom_matpel' => [
                1 => [
                    1 => ['nama' => 'Fisika', 'nilai' => ''],
                    2 => ['nama' => 'Kimia', 'nilai' => '85'],
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reject_when_score_is_out_of_range()
    {
        [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]] = $this->createPendaftar();

        $response = $this->actingAs($akun)->post(route('pendaftar.nilai.store'), [
            'nilai' => [
                $matpelIndo->id => [1 => '150'], // Melebihi 100
            ],
            'custom_matpel' => [
                1 => [
                    1 => ['nama' => 'Fisika', 'nilai' => '85'],
                    2 => ['nama' => 'Kimia', 'nilai' => '85'],
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reject_when_fewer_than_two_custom_subjects_provided()
    {
        [$akun, $peserta, $daftar, [$matpelIndo, $matpelMtk, $matpelIng]] = $this->createPendaftar();

        $response = $this->actingAs($akun)->post(route('pendaftar.nilai.store'), [
            'custom_matpel' => [
                1 => [
                    1 => ['nama' => 'Fisika', 'nilai' => '85'],
                    // Hanya 1 mapel pendukung
                ]
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
