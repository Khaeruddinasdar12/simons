<?php

namespace App\Filament\Support;

use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use App\Services\HapusPermohonanService;
use Filament\Forms;
use Filament\Notifications\Notification;

class HapusPermohonanUi
{
    public static function deskripsiHapusPembimbing(PermohonanPembimbing $record): string
    {
        $jumlahPenguji = $record->permohonanPenguji()->count();
        $lanjutan = $jumlahPenguji > 0
            ? " Termasuk {$jumlahPenguji} permohonan SK Penguji yang terhubung ke SK Pembimbing ini."
            : '';

        return 'Permohonan SK Pembimbing ini akan dihapus permanen, termasuk berkas usul dan file SK.'.$lanjutan;
    }

    public static function deskripsiHapusPenguji(): string
    {
        return 'Permohonan SK Penguji ini akan dihapus permanen, termasuk berkas usul dan file SK. Data SK Pembimbing tidak ikut terhapus.';
    }

    public static function deskripsiHapusNim(string $nim, ?string $nama = null): string
    {
        $identitas = filled($nama) ? "{$nim} — {$nama}" : $nim;

        return "Seluruh data NIM {$identitas} akan dihapus permanen: permohonan SK Pembimbing, SK Penguji, riwayat judul, undangan munaqasyah, berkas, dan data mahasiswa. Tindakan ini tidak dapat dibatalkan.";
    }

    public static function fieldKonfirmasiNim(string $nim): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('konfirmasi_nim')
            ->label('Ketik NIM untuk konfirmasi')
            ->placeholder($nim)
            ->required()
            ->rules([
                fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($nim): void {
                    if (trim((string) $value) !== $nim) {
                        $fail('NIM tidak sesuai.');
                    }
                },
            ]);
    }

    public static function hapusDataNim(string $nim): void
    {
        app(HapusPermohonanService::class)->hapusDataNim($nim);

        Notification::make()
            ->title("Seluruh data NIM {$nim} telah dihapus")
            ->success()
            ->send();
    }
}
