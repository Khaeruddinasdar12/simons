<?php

namespace App\Services;

final class SkNomorAllocator
{
    /**
     * Nomor urut diambil dari nilai terbesar yang sudah terpakai, bukan dari id terbaru.
     * SK sering terbit tidak sesuai urutan pengajuan, jadi orderBy id menabrak unique nomor_sk.
     *
     * @param  iterable<int, mixed>  $existingNomors
     */
    public static function next(string $jenis, iterable $existingNomors, ?\DateTimeInterface $at = null): string
    {
        $at ??= now();
        $max = 0;
        $pattern = '/^(\d+)\/'.preg_quote($jenis, '/').'\//';

        foreach ($existingNomors as $nomor) {
            if (is_string($nomor) && preg_match($pattern, $nomor, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return sprintf('%03d/%s/%s/%s', $max + 1, $jenis, $at->format('m'), $at->format('Y'));
    }
}
