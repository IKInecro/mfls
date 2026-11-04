<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('email_templates')) {
            return;
        }

        $templates = DB::table('email_templates')->get();
        foreach ($templates as $t) {
            if (!$t->body && !$t->subject) {
                continue;
            }

            $heals = [
                '{Budi Santoso}' => '{nama}',
                '{123456}' => '{otp}',
                '{SMAN 1 Surabaya}' => '{asalSekolah}',
                '{lama@example.com}' => '{emailLama}',
                '{baru@example.com}' => '{emailBaru}',
                '{http://localhost/ujian/abc123}' => '{linkUjian}',
                '{http://localhost/reset-password/abc123?email=budi%40example.com}' => '{linkReset}',
            ];

            $updates = [];

            if ($t->body) {
                $body = str_replace(array_keys($heals), array_values($heals), $t->body);
                if (in_array($t->key, ['otp', 'email_change_otp'], true) && !str_contains($body, '{otp}') && str_contains($body, '123456')) {
                    $body = str_replace('123456', '{otp}', $body);
                }
                if (!str_contains($body, '{nama}') && str_contains($body, 'Budi Santoso')) {
                    $body = str_replace('Budi Santoso', '{nama}', $body);
                }
                if ($body !== $t->body) {
                    $updates['body'] = $body;
                }
            }

            if ($t->subject) {
                $subject = str_replace(array_keys($heals), array_values($heals), $t->subject);
                if (!str_contains($subject, '{nama}') && str_contains($subject, 'Budi Santoso')) {
                    $subject = str_replace('Budi Santoso', '{nama}', $subject);
                }
                if ($subject !== $t->subject) {
                    $updates['subject'] = $subject;
                }
            }

            if (!empty($updates)) {
                $updates['updated_at'] = now();
                DB::table('email_templates')->where('id', $t->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for data cleanup
    }
};
