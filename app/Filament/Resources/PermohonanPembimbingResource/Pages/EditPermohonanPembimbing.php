<?php

namespace App\Filament\Resources\PermohonanPembimbingResource\Pages;

use App\Filament\Resources\PermohonanPembimbingResource;
use App\Filament\Support\HapusPermohonanUi;
use App\Models\PermohonanPembimbing;
use App\Services\HapusPermohonanService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPermohonanPembimbing extends EditRecord
{
    protected static string $resource = PermohonanPembimbingResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            Actions\ViewAction::make(),
            Actions\Action::make('generateUlangSk')
                ->label('Generate Ulang SK')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (): bool => auth()->user()?->can('generateUlangSk', $record) ?? false)
                ->fillForm(fn (): array => PermohonanPembimbingResource::generateUlangSkFillForm($this->getRecord()))
                ->form(PermohonanPembimbingResource::generateUlangSkFormSchema())
                ->modalHeading('Generate Ulang SK Pembimbing')
                ->modalDescription('Semua isian dapat diubah. Nomor SK dan tanggal penetapan tidak berubah. PDF SK akan dibuat ulang dari data ini.')
                ->modalWidth('7xl')
                ->modalSubmitActionLabel('Simpan & Generate Ulang SK')
                ->action(function (array $data): void {
                    PermohonanPembimbingResource::prosesGenerateUlangSk($this->getRecord(), $data);
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
                }),
            Actions\DeleteAction::make()
                ->label('Hapus permohonan')
                ->modalHeading('Hapus permohonan SK Pembimbing')
                ->modalDescription(fn (): string => HapusPermohonanUi::deskripsiHapusPembimbing($this->getRecord()))
                ->successNotificationTitle('Permohonan SK Pembimbing dihapus')
                ->successRedirectUrl(PermohonanPembimbingResource::getUrl('index'))
                ->using(function (PermohonanPembimbing $record): void {
                    app(HapusPermohonanService::class)->hapusPembimbing($record);
                }),
            Actions\Action::make('hapusDataNim')
                ->label('Hapus seluruh data NIM')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn (): bool => auth()->user()?->can('hapusDataNim', $record) ?? false)
                ->requiresConfirmation()
                ->modalHeading('Hapus seluruh data mahasiswa')
                ->modalDescription(fn (): string => HapusPermohonanUi::deskripsiHapusNim(
                    (string) $this->getRecord()->mahasiswa_nim,
                    $this->getRecord()->mahasiswa?->nama_lengkap,
                ))
                ->form(fn (): array => [
                    HapusPermohonanUi::fieldKonfirmasiNim((string) $this->getRecord()->mahasiswa_nim),
                ])
                ->action(function (): void {
                    $nim = (string) $this->getRecord()->mahasiswa_nim;
                    HapusPermohonanUi::hapusDataNim($nim);
                    $this->redirect(PermohonanPembimbingResource::getUrl('index'));
                }),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        return $record->fresh(['mahasiswa']);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
