<?php

namespace App\Enums;

enum StatusPermohonan: string
{
    case Diajukan = 'diajukan';
    case DikirimPimpinan = 'dikirim_pimpinan';
    case DikembalikanAkademik = 'dikembalikan_akademik';
    case Ditolak = 'ditolak';
    case SkTerbit = 'sk_terbit';

    public function label(): string
    {
        return match ($this) {
            self::Diajukan => 'Diajukan',
            self::DikirimPimpinan => 'Dikirim ke Pimpinan',
            self::DikembalikanAkademik => 'Dikembalikan ke Akademik',
            self::Ditolak => 'Ditolak',
            self::SkTerbit => 'SK Terbit',
        };
    }

    public function labelPublik(): string
    {
        return match ($this) {
            self::Diajukan => 'Menunggu akademik',
            self::DikirimPimpinan => 'Sedang diperiksa pimpinan',
            self::DikembalikanAkademik => 'Dikembalikan',
            self::Ditolak => 'Ditolak',
            self::SkTerbit => 'SK terbit',
        };
    }

    public function keteranganPublik(): string
    {
        return match ($this) {
            self::Diajukan => 'Permohonan sedang diperiksa akademik.',
            self::DikirimPimpinan => 'Permohonan sedang diperiksa Kabag, Wadek 1, dan Dekan.',
            self::DikembalikanAkademik => 'Permohonan dikembalikan ke akademik untuk ditindaklanjuti.',
            self::Ditolak => 'Permohonan tidak disetujui.',
            self::SkTerbit => 'SK telah terbit dan dapat diunduh.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Diajukan => 'gray',
            self::DikirimPimpinan => 'warning',
            self::DikembalikanAkademik => 'info',
            self::Ditolak => 'danger',
            self::SkTerbit => 'success',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}
