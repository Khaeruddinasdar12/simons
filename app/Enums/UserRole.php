<?php

namespace App\Enums;

enum UserRole: string
{
    case Akademik = 'akademik';
    case Kabag = 'kabag';
    case Wadek1 = 'wadek1';
    case Dekan = 'dekan';
    case Superadmin = 'superadmin';

    public function label(): string
    {
        return match ($this) {
            self::Akademik => 'Akademik',
            self::Kabag => 'Kabag',
            self::Wadek1 => 'Wakil Dekan 1',
            self::Dekan => 'Dekan',
            self::Superadmin => 'Super Admin',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [$role->value => $role->label()])
            ->all();
    }
}
