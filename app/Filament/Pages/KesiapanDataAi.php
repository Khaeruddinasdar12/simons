<?php

namespace App\Filament\Pages;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Models\Dosen;
use App\Models\IstilahProdi;
use App\Models\JudulKorpus;
use App\Models\Keahlian;
use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use Filament\Pages\Page;

class KesiapanDataAi extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.kesiapan-data-ai';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kesiapan Data AI';

    protected static ?string $title = 'Kesiapan Data AI';

    protected static ?int $navigationSort = 14;

    public static function canAccess(): bool
    {
        return auth()->user()?->isAkademik() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $dosenAktif = Dosen::query()->aktif()->count();
        $dosenSiap = Dosen::query()->aktif()->has('keahlians', '>=', 2)->count();
        $skPembimbingTerbit = PermohonanPembimbing::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->count();
        $skPengujiTerbit = PermohonanPenguji::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->count();
        $pembimbingTerhubung = PermohonanPembimbing::query()
            ->whereNotNull('pembimbing_1_dosen_id')
            ->count();
        $pembimbingTotal = PermohonanPembimbing::query()->count();

        $istilahPerProdi = [];
        foreach (ProgramStudi::cases() as $prodi) {
            $istilahPerProdi[$prodi->value] = IstilahProdi::query()
                ->aktif()
                ->where('program_studi', $prodi->value)
                ->count();
        }

        return [
            'dosenAktif' => $dosenAktif,
            'dosenSiap' => $dosenSiap,
            'dosenBelumSiap' => max(0, $dosenAktif - $dosenSiap),
            'keahlianAktif' => Keahlian::query()->aktif()->count(),
            'istilahAktif' => IstilahProdi::query()->aktif()->count(),
            'istilahPerProdi' => $istilahPerProdi,
            'korpus' => JudulKorpus::query()->count(),
            'korpusDitandaiMirip' => JudulKorpus::query()->where('ditandai_mirip', true)->count(),
            'skPembimbingTerbit' => $skPembimbingTerbit,
            'skPengujiTerbit' => $skPengujiTerbit,
            'pembimbingTerhubung' => $pembimbingTerhubung,
            'pembimbingTotal' => $pembimbingTotal,
        ];
    }
}
