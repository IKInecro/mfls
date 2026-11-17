<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageCompressor
{
    /**
     * Compress an image to a specific quality and return the new path.
     * 
     * @param string $path Path relative to storage/app/public
     * @param int $quality Quality from 0 to 100
     * @param int|null $maxWidth Optional max width to resize
     * @return bool
     */
    public function compressImage($path, $quality = 60, $maxWidth = 1200)
    {
        // Jika GD extension tidak aktif, skip kompresi (file tetap tersimpan)
        if (!extension_loaded('gd')) {
            return true;
        }

        try {
            $fullPath = storage_path('app/public/' . $path);

            if (!file_exists($fullPath)) {
                return false;
            }

            // Naikkan memory limit sementara jika memungkinkan agar foto resolusi tinggi tidak crash
            @ini_set('memory_limit', '256M');

            $info = @getimagesize($fullPath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'];

            // Lewati jika resolusi terlampau ekstrem untuk diproses GD guna mencegah memory leak
            if ($info[0] > 7000 || $info[1] > 7000) {
                return true;
            }

            // Load image
            switch ($mime) {
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($fullPath);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($fullPath);
                    if ($image) {
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                    }
                    break;
                case 'image/webp':
                    $image = @imagecreatefromwebp($fullPath);
                    break;
                default:
                    return false;
            }

            if (!$image) {
                return false;
            }

            // Resize if exceeds maxWidth
            $width = imagesx($image);
            $height = imagesy($image);

            if ($maxWidth && $width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) floor($height * ($maxWidth / $width));

                $tmpImg = imagecreatetruecolor($newWidth, $newHeight);

                if ($mime == 'image/png') {
                    imagealphablending($tmpImg, false);
                    imagesavealpha($tmpImg, true);
                    $transparent = imagecolorallocatealpha($tmpImg, 255, 255, 255, 127);
                    imagefilledrectangle($tmpImg, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($tmpImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $tmpImg;
            }

            // Save back with compression
            switch ($mime) {
                case 'image/jpeg':
                    imagejpeg($image, $fullPath, $quality);
                    break;
                case 'image/png':
                    $pngQuality = (int) (9 - floor($quality / 11));
                    imagepng($image, $fullPath, $pngQuality);
                    break;
                case 'image/webp':
                    imagewebp($image, $fullPath, $quality);
                    break;
            }

            imagedestroy($image);
            return true;
        } catch (\Throwable $e) {
            // Tangani error agar proses upload berkas utama tidak pernah crash bagi pendaftar
            \Illuminate\Support\Facades\Log::warning('Image compression skipped: ' . $e->getMessage());
            return true;
        }
    }
}
