<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Pastikan rute debug publik yang membocorkan data telah dihapus (404).
     */
    public function test_debug_routes_are_not_accessible(): void
    {
        $this->get('/debug-candidates')->assertNotFound();
        $this->get('/debug-migrate')->assertNotFound();
        $this->get('/debug-beasiswa-100')->assertNotFound();
    }

    /**
     * Pastikan respons web memuat header keamanan OWASP.
     */
    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * Pastikan rute login dilindungi throttle rate limiter.
     */
    public function test_login_route_has_throttle_middleware(): void
    {
        // 10 request pertama diizinkan
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', [
                'email' => 'fake' . $i . '@example.com',
                'password' => 'wrongpass',
            ]);
        }

        // Request ke-11 harus terkena HTTP 429 Too Many Requests
        $response = $this->post('/login', [
            'email' => 'fake11@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429);
    }
}
