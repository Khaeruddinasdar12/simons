<?php

namespace App\Filament\Resources\PermohonanPembimbingResource\Pages;

use App\Filament\Resources\PermohonanPembimbingResource;
use App\Filament\Support\HapusPermohonanUi;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPermohonanPembimbings extends ListRecords
{
    protected static string $resource = PermohonanPembimbingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('hapusDataNim')
                ->label('Hapus seluruh data NIM')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn (): bool => Auth::user()?->isSuperadmin() ?? false)
                ->requiresConfirmation()
                ->modalHeading('Hapus seluruh data mahasiswa')
                ->modalDescription('Semua permohonan SK Pembimbing, SK Penguji, riwayat judul, undangan munaqasyah, berkas, dan data mahasiswa dengan NIM tersebut akan dihapus permanen.')
                ->form([
                    Forms\Components\TextInput::make('nim')
                        ->label('NIM')
                        ->required()
                        ->exists('mahasiswas', 'nim')
                        ->validationMessages([
                            'exists' => 'NIM tidak ditemukan.',
                        ]),
                    Forms\Components\TextInput::make('konfirmasi_nim')
                        ->label('Ketik ulang NIM')
                        ->required()
                        ->same('nim')
                        ->validationMessages([
                            'same' => 'NIM konfirmasi tidak sesuai.',
                        ]),
                ])
                ->action(function (array $data): void {
                    HapusPermohonanUi::hapusDataNim(trim((string) $data['nim']));
                }),
        ];
    }
}
