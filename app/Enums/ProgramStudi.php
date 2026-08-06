<?php

namespace App\Enums;

enum ProgramStudi: string
{
    case HukumTataNegara = 'Hukum Tata Negara';
    case HukumEkonomiSyariah = 'Hukum Ekonomi Syariah';
    case HukumKeluargaIslam = 'Hukum Keluarga Islam';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $item) => [$item->value => $item->value])
            ->all();
    }
}
