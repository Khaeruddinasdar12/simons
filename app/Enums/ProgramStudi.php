<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProgramStudi: string implements HasLabel
{
    case HukumTataNegara = 'Hukum Tata Negara';
    case HukumKeluargaIslam = 'Hukum Keluarga Islam';
    case HukumEkonomiSyariah = 'Hukum Ekonomi Syariah';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HukumTataNegara => 'Hukum Tata Negara (Siyasah Syar\'iyyah)',
            self::HukumKeluargaIslam => 'Hukum Keluarga Islam (Ahwal Syakhshiyyah)',
            self::HukumEkonomiSyariah => 'Hukum Ekonomi Syariah (Muamalah)',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $item) => [$item->value => $item->getLabel()])
            ->all();
    }
}
