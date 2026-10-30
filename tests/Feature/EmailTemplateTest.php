<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_null_saat_belum_ada_baris(): void
    {
        $this->assertNull(EmailTemplate::get('otp'));
    }

    public function test_subject_override_atau_default(): void
    {
        EmailTemplate::create(['key' => 'otp', 'subject' => 'Judul Baru', 'body' => 'Isi']);

        $this->assertSame('Judul Baru', EmailTemplate::subjectOrDefault('otp', 'Judul Asli'));

        // subject NULL → pakai default
        EmailTemplate::create(['key' => 'reset_password', 'subject' => null, 'body' => 'Isi']);
        $this->assertSame('Judul Asli Reset', EmailTemplate::subjectOrDefault('reset_password', 'Judul Asli Reset'));
    }

    public function test_body_null_saat_tidak_ada_override(): void
    {
        $this->assertNull(EmailTemplate::body('otp'));

        EmailTemplate::create(['key' => 'otp', 'body' => 'Halo {nama}']);
        $this->assertSame('Halo {nama}', EmailTemplate::body('otp'));
    }

    public function test_render_substitusi_placeholder_valid(): void
    {
        $out = EmailTemplate::render('otp', 'Halo {nama}, kode {otp}!', ['nama' => 'Budi', 'otp' => '9999']);

        $this->assertSame('Halo Budi, kode 9999!', $out);
    }

    public function test_render_placeholder_tak_dikenal_dibiarkan_literal(): void
    {
        $out = EmailTemplate::render('otp', 'Halo {nama}, rahasia {wah} {otp}', ['nama' => 'Budi', 'otp' => '1']);

        $this->assertSame('Halo Budi, rahasia {wah} 1', $out);
    }

    public function test_render_tidak_mengikuti_placeholder_key_lain(): void
    {
        // {linkUjian} bukan whitelist otp → tidak boleh disubstitusi meski dikirim di data
        $out = EmailTemplate::render('otp', 'Kode {otp} aja {linkUjian}', ['otp' => '77', 'linkUjian' => 'https://x']);

        $this->assertSame('Kode 77 aja {linkUjian}', $out);
    }

    public function test_render_body_null_kalau_tidak_ada_baris(): void
    {
        $this->assertNull(EmailTemplate::renderBody('otp', ['nama' => 'Budi', 'otp' => '1']));

        EmailTemplate::create(['key' => 'otp', 'body' => 'Halo {nama} ({otp})']);
        $this->assertSame('Halo Budi (1)', EmailTemplate::renderBody('otp', ['nama' => 'Budi', 'otp' => '1']));
    }

    public function test_reset_menghapus_baris(): void
    {
        EmailTemplate::create(['key' => 'otp', 'subject' => 'X', 'body' => 'Y']);
        EmailTemplate::reset('otp');

        $this->assertNull(EmailTemplate::get('otp'));
        $this->assertDatabaseMissing('email_templates', ['key' => 'otp']);
    }

    public function test_missing_required_terdeteksi(): void
    {
        $t = EmailTemplate::create(['key' => 'otp', 'body' => 'Halo {nama}']);

        $this->assertSame(['{otp}'], $t->missingRequired());
    }

    public function test_key_unik(): void
    {
        EmailTemplate::create(['key' => 'otp', 'body' => 'A']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        EmailTemplate::create(['key' => 'otp', 'body' => 'B']);
    }
}
