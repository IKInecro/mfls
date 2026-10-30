<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateAdminTest extends TestCase
{
    use RefreshDatabase;

    private function akun(string $role): Akun
    {
        return Akun::create([
            'nama' => 'Staf Tes',
            'email' => "{$role}-tes@example.com",
            'password' => 'password',
            'role' => $role,
            'email_verified_at' => now(),
        ]);
    }

    public function test_tamu_diredirect_ke_login(): void
    {
        $this->get('/admin/email-templates')->assertRedirect('/login');
    }

    public function test_role_non_admin_ditolak(): void
    {
        $this->actingAs($this->akun('akademik'))
            ->get('/admin/email-templates')
            ->assertForbidden();
    }

    public function test_admin_bisa_lihat_daftar_7_template(): void
    {
        $resp = $this->actingAs($this->akun('admin'))->get('/admin/email-templates');

        $resp->assertOk();
        foreach (EmailTemplate::LABELS as $label) {
            $resp->assertSee($label);
        }
    }

    public function test_admin_buka_form_edit_key_valid_dan_404_key_asing(): void
    {
        $admin = $this->akun('admin');

        $this->actingAs($admin)->get('/admin/email-templates/otp/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/email-templates/aneh/edit')->assertNotFound();
    }

    public function test_simpan_subject_dan_body(): void
    {
        $this->actingAs($this->akun('admin'))->post('/admin/email-templates/otp', [
            'subject' => 'Judul Baru',
            'body' => '<p>Halo {nama}, kode {otp}</p>',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('email_templates', [
            'key' => 'otp',
            'subject' => 'Judul Baru',
            'body' => '<p>Halo {nama}, kode {otp}</p>',
        ]);
    }

    public function test_simpan_body_kosong_jadi_null_fallback(): void
    {
        $this->actingAs($this->akun('admin'))->post('/admin/email-templates/otp', [
            'subject' => 'Judul Saja',
            'body' => '',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('email_templates', ['key' => 'otp', 'subject' => 'Judul Saja', 'body' => null]);
    }

    public function test_update_key_asing_ditolak(): void
    {
        $this->actingAs($this->akun('admin'))
            ->post('/admin/email-templates/tidak-ada', ['body' => 'x'])
            ->assertNotFound();

        $this->assertDatabaseMissing('email_templates', ['key' => 'tidak-ada']);
    }

    public function test_reset_ke_default_menghapus_baris(): void
    {
        EmailTemplate::create(['key' => 'otp', 'subject' => 'X', 'body' => 'Y']);

        $this->actingAs($this->akun('admin'))
            ->delete('/admin/email-templates/otp')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('email_templates', ['key' => 'otp']);
    }

    public function test_preview_render_draft_di_cangkang_asli(): void
    {
        $resp = $this->actingAs($this->akun('admin'))->post('/admin/email-templates/otp/preview', [
            'subject' => 'Draft Judul',
            'body' => '<p>Halo {nama}, kode Anda {otp}.</p>',
        ]);

        $resp->assertOk();
        $resp->assertSee('Halo Budi Santoso, kode Anda 123456.', false);
        // cangkang asli tetap ada
        $resp->assertSee('MNC Tower, Jakarta, Indonesia');
        // draft TIDAK ikut tersimpan
        $this->assertDatabaseMissing('email_templates', ['key' => 'otp']);
    }

    public function test_form_edit_menampilkan_peringatan_placeholder_wajib_hilang(): void
    {
        EmailTemplate::create(['key' => 'otp', 'body' => 'Halo {nama} tanpa kode']);

        $this->actingAs($this->akun('admin'))
            ->get('/admin/email-templates/otp/edit')
            ->assertSee('{otp}');
    }
}
