<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProgramStudi: string implements HasLabel
{
    case HukumTataNegara = 'Hukum Tata Negara';
    case HukumEkonomiSyariah = 'Hukum Ekonomi Syariah';
    case HukumKeluargaIslam = 'Hukum Keluarga Islam';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $item) => [$item->value => $item->value])
            ->all();
    }
}
