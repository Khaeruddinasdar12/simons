<?php

namespace App\Support;

class JudulNormalizer
{
    public static function normalize(?string $judul): string
    {
        $judul = mb_strtolower(trim((string) $judul));

        if ($judul === '') {
            return '';
        }

        $judul = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $judul) ?? $judul;
        $judul = preg_replace('/\s+/u', ' ', $judul) ?? $judul;

        return trim($judul);
    }
}
