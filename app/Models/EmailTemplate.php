<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';

    protected $fillable = [
        'key',
        'subject',
        'body',
    ];

    public const KEYS = [
        'otp',
        'reset_password',
        'email_change_otp',
        'admin_changed_email',
        'lolos_seleksi_ujian',
        'certificate',
        'ujian_link',
    ];

    public const LABELS = [
        'otp' => 'Kode OTP Pendaftaran',
        'reset_password' => 'Reset Password',
        'email_change_otp' => 'OTP Ubah Email',
        'admin_changed_email' => 'Notif Perubahan Email oleh Admin',
        'lolos_seleksi_ujian' => 'Pengumuman Lolos Seleksi Ujian',
        'certificate' => 'Sertifikat Partisipasi',
        'ujian_link' => 'Undangan Ujian Online',
    ];

    /**
     * Placeholder yang boleh dipakai per key + nilai contoh untuk preview.
     * INI whitelist-nya — placeholder di luar daftar tidak pernah disubstitusi.
     */
    public const SAMPLES = [
        'otp' => ['nama' => 'Budi Santoso', 'otp' => '123456'],
        'reset_password' => [
            'nama' => 'Budi Santoso',
            'linkReset' => 'http://localhost/reset-password/abc123?email=budi%40example.com',
        ],
        'email_change_otp' => ['nama' => 'Budi Santoso', 'otp' => '123456'],
        'admin_changed_email' => [
            'nama' => 'Budi Santoso',
            'emailLama' => 'lama@example.com',
            'emailBaru' => 'baru@example.com',
        ],
        'lolos_seleksi_ujian' => ['nama' => 'Budi Santoso', 'asalSekolah' => 'SMAN 1 Surabaya'],
        'certificate' => ['nama' => 'Budi Santoso'],
        'ujian_link' => [
            'nama' => 'Budi Santoso',
            'linkUjian' => 'http://localhost/ujian/abc123',
        ],
    ];

    /**
     * Placeholder wajib per key — hilang dari body = peringatan lembut di UI admin.
     */
    public const REQUIRED = [
        'otp' => ['{otp}'],
        'reset_password' => ['{linkReset}'],
        'email_change_otp' => ['{otp}'],
        'admin_changed_email' => ['{emailBaru}'],
        'lolos_seleksi_ujian' => ['{nama}'],
        'certificate' => ['{nama}'],
        'ujian_link' => ['{linkUjian}'],
    ];

    public static function get(string $key): ?self
    {
        return static::query()->where('key', $key)->first();
    }

    public static function subjectOrDefault(string $key, string $default): string
    {
        return static::get($key)?->subject ?: $default;
    }

    public static function body(string $key): ?string
    {
        return static::get($key)?->body;
    }

    /**
     * Body ter-override untuk pengiriman nyata: null kalau tidak ada override
     * (→ view pakai isi Blade asli).
     */
    public static function renderBody(string $key, array $data): ?string
    {
        $body = static::body($key);

        return $body === null ? null : static::render($key, $body, $data);
    }

    /**
     * Substitusi placeholder {nama} dst dari whitelist per key.
     * HANYA string replace — body dari DB TIDAK PERNAH dikompile jadi Blade
     * (mencegah eksekusi kode dari konten yang ditulis lewat web).
     * Placeholder tak dikenal dibiarkan literal.
     */
    public static function render(string $key, string $body, array $data): string
    {
        $allowed = array_keys(self::SAMPLES[$key] ?? []);

        return preg_replace_callback('/{(\w+)}/', function ($m) use ($allowed, $data) {
            if (!in_array($m[1], $allowed, true) || !array_key_exists($m[1], $data)) {
                return $m[0];
            }

            return (string) $data[$m[1]];
        }, $body);
    }

    public static function reset(string $key): void
    {
        static::query()->where('key', $key)->delete();
    }

    /**
     * Placeholder wajib yang tidak ada di body — untuk peringatan lembut di UI.
     */
    public function missingRequired(): array
    {
        $missing = [];

        foreach (self::REQUIRED[$this->key] ?? [] as $ph) {
            if (!str_contains((string) $this->body, $ph)) {
                $missing[] = $ph;
            }
        }

        return $missing;
    }
}
