<?php

namespace App\Services;

class SkDocumentAssets
{
    private static ?string $cachedLogoDataUri = null;

    private static bool $logoResolved = false;

    /**
     * Logo SK (data URI). Memakai file kecil yang ikut di-deploy.
     * Jangan pernah me-load logoiainbone.png penuh (5125px) ke GD — itu ~100MB dan meledakkan memory_limit 128M.
     */
    public function logoDataUri(): ?string
    {
        if (self::$logoResolved) {
            return self::$cachedLogoDataUri;
        }

        self::$logoResolved = true;
        $path = $this->logoPath();

        if ($path === null) {
            return self::$cachedLogoDataUri = null;
        }

        $binary = file_get_contents($path);

        return self::$cachedLogoDataUri = $binary === false
            ? null
            : 'data:image/png;base64,'.base64_encode($binary);
    }

    public function logoPath(): ?string
    {
        $small = public_path('logoiainbone-sk.png');

        if (is_file($small) && filesize($small) > 0) {
            return $small;
        }

        return null;
    }
}
