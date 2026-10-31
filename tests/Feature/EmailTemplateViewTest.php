<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateViewTest extends TestCase
{
    use RefreshDatabase;

    private function fakeMessage(): object
    {
        return new class
        {
            public function embed($path)
            {
                return 'cid:logo';
            }
        };
    }

    private function render(string $view, array $data, ?string $templateBody): string
    {
        return view('emails.'.$view, array_merge($data, [
            'message' => $this->fakeMessage(),
            'templateBody' => $templateBody,
        ]))->render();
    }

    private function baseData(string $key): array
    {
        $user = (object) ['nama' => 'Budi Santoso', 'email' => 'budi@example.com'];

        return match ($key) {
            'otp' => ['nama' => 'Budi', 'otp' => '123456'],
            'reset_password' => ['user' => $user, 'token' => 'abc123'],
            'email_change_otp' => ['user' => $user, 'otp' => '123456'],
            'admin_changed_email' => ['nama' => 'Budi', 'oldEmail' => 'a@x.com', 'newEmail' => 'b@x.com'],
            'lolos_seleksi_ujian' => ['nama' => 'Budi', 'asalSekolah' => 'SMAN 1'],
            'certificate' => ['nama' => 'Budi'],
            'ujian_link' => ['nama' => 'Budi', 'linkUjian' => 'http://localhost/ujian/abc'],
            default => [],
        };
    }

    /** Teks default (isi asli) yang harus hilang saat override dipasang. */
    private function marker(string $key): string
    {
        return match ($key) {
            'otp' => 'Verifikasi Akun MNCU Future Leader Scholarship',
            'reset_password' => 'Atur Ulang Kata Sandi',
            'email_change_otp' => 'Verifikasi Ganti Email',
            'admin_changed_email' => 'Pembaruan Alamat Email',
            'lolos_seleksi_ujian' => 'Selamat! Anda Lolos Seleksi Ujian!',
            'certificate' => 'Selamat! Pendaftaran Terverifikasi',
            'ujian_link' => 'Mulai Ujian Sekarang',
        };
    }

    public function test_semua_view_fallback_ke_isi_asli_saat_tidak_ada_override(): void
    {
        foreach (\App\Models\EmailTemplate::KEYS as $key) {
            $html = $this->render($key, $this->baseData($key), null);

            $this->assertStringContainsString($this->marker($key), $html, "view {$key}");
            $this->assertStringNotContainsString('ISI BARU', $html, "view {$key}");
        }
    }

    public function test_semua_view_pakai_template_body_saat_ada_override(): void
    {
        foreach (\App\Models\EmailTemplate::KEYS as $key) {
            $html = $this->render($key, $this->baseData($key), '<p>ISI BARU</p>');

            $this->assertStringContainsString('ISI BARU', $html, "view {$key}");
            $this->assertStringNotContainsString($this->marker($key), $html, "view {$key}");
            // cangkang tetap ada
            $this->assertStringContainsString('<html', $html, "view {$key}");
        }
    }

    public function test_lolos_view_cangkang_footer_di_luar_slot(): void
    {
        // Footer tetap tampil walau body di-override (footer di luar slot)
        $html = $this->render('lolos_seleksi_ujian', $this->baseData('lolos_seleksi_ujian'), '<p>ISI BARU</p>');

        $this->assertStringContainsString('ISI BARU', $html);
        $this->assertStringContainsString('All rights reserved.', $html);
        $this->assertStringNotContainsString('Selamat! Anda Lolos Seleksi Ujian!', $html);
    }

    public function test_body_full_document_menggantikan_seluruh_email(): void
    {
        $full = "<!DOCTYPE html>\n<html><body><p>FULL SENDIRI</p></body></html>";

        foreach (\App\Models\EmailTemplate::KEYS as $key) {
            $html = $this->render($key, $this->baseData($key), $full);

            $this->assertStringContainsString('FULL SENDIRI', $html, "view {$key}");
            $this->assertStringNotContainsString('MNC Tower, Jakarta, Indonesia', $html, "view {$key}");
        }
    }
}
