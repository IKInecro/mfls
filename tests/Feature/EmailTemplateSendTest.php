<?php

namespace Tests\Feature;

use App\Mail\CertificateMail;
use App\Mail\OtpMail;
use App\Mail\UjianLinkMail;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateSendTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_mail_default_saat_tidak_ada_override(): void
    {
        $mail = new OtpMail('Budi Santoso', '999999');

        $this->assertSame('Kode OTP Verifikasi Pendaftaran MNCU Future Leader Scholarship', $mail->envelope()->subject);
        $html = $mail->render();
        $this->assertStringContainsString('Verifikasi Akun MNCU Future Leader Scholarship', $html);
        $this->assertStringContainsString('999999', $html);
    }

    public function test_otp_mail_pakai_override_subject_dan_body(): void
    {
        EmailTemplate::create([
            'key' => 'otp',
            'subject' => 'Kode Login untuk {nama}',
            'body' => '<p>Halo {nama}, kode Anda {otp}.</p>',
        ]);

        $mail = new OtpMail('Budi Santoso', '999999');

        $this->assertSame('Kode Login untuk Budi Santoso', $mail->envelope()->subject);
        // subject juga disubstitusi bila datanya dikirim
        $html = $mail->render();
        $this->assertStringContainsString('Halo Budi Santoso, kode Anda 999999.', $html);
        $this->assertStringNotContainsString('Verifikasi Akun MNCU Future Leader Scholarship', $html);
    }

    public function test_certificate_mail_override(): void
    {
        EmailTemplate::create(['key' => 'certificate', 'body' => '<p>Sertifikat atas nama {nama} siap diunduh.</p>']);

        $html = (new CertificateMail('Budi Santoso', 'pdf-bytes'))->render();

        $this->assertStringContainsString('Sertifikat atas nama Budi Santoso siap diunduh.', $html);
    }

    public function test_ujian_link_mail_override(): void
    {
        EmailTemplate::create(['key' => 'ujian_link', 'subject' => 'Masuk Ujian', 'body' => '<p>Segera kerjakan: {linkUjian}</p>']);

        $mail = new UjianLinkMail('Budi Santoso', 'http://localhost/ujian/xyz');

        $this->assertSame('Masuk Ujian', $mail->envelope()->subject);
        $this->assertStringContainsString('Segera kerjakan: http://localhost/ujian/xyz', $mail->render());
    }
}
