<?php

namespace App\Services;

class SkDocumentAssets
{
    /**
     * Logo SK dalam data URI berukuran kecil.
     * File asli 5125x5125 membuat DomPDF timeout sebelum file_sk tersimpan.
     */
    public function logoDataUri(): ?string
    {
        $path = $this->cachedLogoPath();

        if ($path === null) {
            return null;
        }

        $binary = file_get_contents($path);

        return $binary === false
            ? null
            : 'data:image/png;base64,'.base64_encode($binary);
    }

    public function cachedLogoPath(): ?string
    {
        $source = public_path('logoiainbone.png');

        if (! is_file($source)) {
            return null;
        }

        $dir = storage_path('app/sk');
        $cached = $dir.DIRECTORY_SEPARATOR.'logo-sk.png';

        if (is_file($cached) && filemtime($cached) >= filemtime($source) && filesize($cached) > 0) {
            return $cached;
        }

        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return $source;
        }

        if (! function_exists('imagecreatefrompng')) {
            return $source;
        }

        $created = $this->writeResizedPng($source, $cached, 240);

        return $created ? $cached : $source;
    }

    protected function writeResizedPng(string $source, string $target, int $maxWidth): bool
    {
        $src = @imagecreatefrompng($source);

        if ($src === false) {
            return false;
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        if ($srcW <= $maxWidth) {
            imagedestroy($src);

            return @copy($source, $target);
        }

        $dstH = (int) max(1, round($srcH * ($maxWidth / $srcW)));
        $dst = imagecreatetruecolor($maxWidth, $dstH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $maxWidth, $dstH, $transparent);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $dstH, $srcW, $srcH);

        $ok = imagepng($dst, $target, 6);
        imagedestroy($src);
        imagedestroy($dst);

        return $ok !== false;
    }
}
