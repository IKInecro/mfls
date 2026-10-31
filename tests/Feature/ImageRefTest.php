<?php

namespace Tests\Feature;

use Illuminate\Support\Fluent;
use Tests\TestCase;

/**
 * Referensi gambar literal di view harus menunjuk file yang benar-benar ada
 * di public/ dengan kapitalisasi yang sama — Linux (dev & Hostinger) bersifat
 * case-sensitive, beda kapital = 404 (pernah terjadi: icon/Keuntungan.png vs
 * keuntungan.png, foto testimoni alfiah.png vs Alfiah.png).
 */
class ImageRefTest extends TestCase
{
    public function test_setiap_asset_literal_ada_di_public(): void
    {
        $refs = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'))
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $content = file_get_contents($file->getPathname());
            if (preg_match_all("/asset\\('([^']+)'/", $content, $m)) {
                foreach ($m[1] as $ref) {
                    $refs[$ref] = $file->getFilename();
                }
            }
        }

        $this->assertNotEmpty($refs, 'tidak ada referensi asset() yang terdeteksi — regex perlu diperbaiki');

        foreach ($refs as $ref => $source) {
            $this->assertFileExists(
                public_path($ref),
                "asset('{$ref}') dari {$source} tidak ditemukan di public/ (cek ejaan & kapitalisasi)"
            );
        }
    }

    public function test_foto_testimoni_ada_dengan_kapitalisasi_sama(): void
    {
        $content = file_get_contents(resource_path('views/pendaftar/home.blade.php'));

        $this->assertMatchesRegularExpression("/'photo' => '/", $content);

        preg_match_all("/'photo' => '([^']+)'/", $content, $m);
        $this->assertNotEmpty($m[1], 'tidak ada foto testimoni yang terdeteksi');

        foreach ($m[1] as $foto) {
            $this->assertFileExists(
                public_path('icon/'.$foto),
                "foto testimoni 'icon/{$foto}' tidak ada di public/icon (beda kapital = 404 di Linux)"
            );
        }
    }
}
