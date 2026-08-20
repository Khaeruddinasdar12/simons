<?php

namespace App\Enums;

enum SumberJudulSkripsi: string
{
    case Pengajuan = 'pengajuan';
    case Perubahan = 'perubahan';

    public function label(): string
    {
        return match ($this) {
            self::Pengajuan => 'Pengajuan mahasiswa',
            self::Perubahan => 'Diubah akademik / superadmin',
        };
    }
}
