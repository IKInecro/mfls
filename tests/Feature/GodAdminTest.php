<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GodAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_god_admin_is_hidden_from_super_admin_in_user_list()
    {
        $superAdmin = Akun::create([
            'nama' => 'Super Admin',
            'email' => 'superadmin@mncu.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.user.index'));
        $response->assertStatus(200);
        $response->assertDontSee('godmncu@mncu.ac.id');
        $response->assertDontSee('Developer Core');
    }

    public function test_god_admin_can_see_self_and_god_badge()
    {
        $godAdmin = Akun::where('email', 'godmncu@mncu.ac.id')->first();
        if (!$godAdmin) {
            $godAdmin = Akun::create([
                'nama' => 'Developer Core',
                'email' => 'godmncu@mncu.ac.id',
                'password' => Hash::make('12345678'),
                'role' => 'god',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $response = $this->actingAs($godAdmin)->get(route('admin.user.index'));
        $response->assertStatus(200);
        $response->assertSee('godmncu@mncu.ac.id');
        $response->assertSee('GOD ADMIN');
    }

    public function test_super_admin_cannot_delete_or_edit_or_reset_password_of_god_admin()
    {
        $superAdmin = Akun::create([
            'nama' => 'Super Admin',
            'email' => 'superadmin@mncu.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $godAdmin = Akun::where('email', 'godmncu@mncu.ac.id')->first();

        // 1. Delete GOD by super admin must be forbidden (403)
        $deleteResponse = $this->actingAs($superAdmin)->delete(route('admin.user.destroy', $godAdmin->id));
        $deleteResponse->assertStatus(403);

        // 2. Update GOD by super admin must be forbidden (403)
        $updateResponse = $this->actingAs($superAdmin)->put(route('admin.user.update', $godAdmin->id), [
            'nama' => 'Hacked Name',
            'email' => 'hacked@email.com',
            'role' => 'admin',
        ]);
        $updateResponse->assertStatus(403);

        // 3. Reset password GOD by super admin must be forbidden (403)
        $resetResponse = $this->actingAs($superAdmin)->put(route('admin.user.reset_password', $godAdmin->id), [
            'password' => 'newpassword123',
        ]);
        $resetResponse->assertStatus(403);
    }

    public function test_god_admin_can_deactivate_super_admin()
    {
        $godAdmin = Akun::where('email', 'godmncu@mncu.ac.id')->first();

        $superAdmin = Akun::create([
            'nama' => 'Target Super Admin',
            'email' => 'target.admin@mncu.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // GOD deactivates Super Admin
        $response = $this->actingAs($godAdmin)->put(route('admin.user.toggle_status', $superAdmin->id));
        $response->assertSessionHas('success');

        $superAdmin->refresh();
        $this->assertFalse($superAdmin->is_active);

        // Nonaktif Super Admin tries to login -> must be rejected
        $this->post('/logout'); // clear session
        $loginResponse = $this->post('/internal/login', [
            'email' => 'target.admin@mncu.ac.id',
            'password' => 'password123',
        ]);
        $loginResponse->assertSessionHas('loginError', 'Akun Anda telah dinonaktifkan oleh Administrator.');
    }

    public function test_super_admin_cannot_deactivate_other_super_admin()
    {
        $superAdmin1 = Akun::create([
            'nama' => 'Super Admin 1',
            'email' => 'superadmin1@mncu.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $superAdmin2 = Akun::create([
            'nama' => 'Super Admin 2',
            'email' => 'superadmin2@mncu.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin1)->put(route('admin.user.toggle_status', $superAdmin2->id));
        $response->assertSessionHas('error', 'Hanya GOD Admin yang memiliki wewenang untuk menonaktifkan Super Admin.');

        $superAdmin2->refresh();
        $this->assertTrue($superAdmin2->is_active);
    }
}
