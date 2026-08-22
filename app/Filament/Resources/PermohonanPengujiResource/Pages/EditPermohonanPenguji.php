<?php

namespace App\Filament\Resources\PermohonanPengujiResource\Pages;

use App\Filament\Resources\PermohonanPengujiResource;
use App\Filament\Support\HapusPermohonanUi;
use App\Models\PermohonanPenguji;
use App\Services\HapusPermohonanService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPermohonanPenguji extends EditRecord
{
    protected static string $resource = PermohonanPengujiResource::class;

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
                ->fillForm(fn (): array => PermohonanPengujiResource::generateUlangSkFillForm($this->getRecord()))
                ->form(PermohonanPengujiResource::generateUlangSkFormSchema())
                ->modalHeading('Generate Ulang SK Penguji')
                ->modalDescription('Semua isian dapat diubah. Nomor SK dan tanggal penetapan tidak berubah. PDF SK akan dibuat ulang dari data ini.')
                ->modalWidth('7xl')
                ->modalSubmitActionLabel('Simpan & Generate Ulang SK')
                ->action(function (array $data): void {
                    PermohonanPengujiResource::prosesGenerateUlangSk($this->getRecord(), $data);
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->getRecord()]));
                }),
            Actions\DeleteAction::make()
                ->label('Hapus permohonan')
                ->modalHeading('Hapus permohonan SK Penguji')
                ->modalDescription(HapusPermohonanUi::deskripsiHapusPenguji())
                ->successNotificationTitle('Permohonan SK Penguji dihapus')
                ->successRedirectUrl(PermohonanPengujiResource::getUrl('index'))
                ->using(function (PermohonanPenguji $record): void {
                    app(HapusPermohonanService::class)->hapusPenguji($record);
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
                    $this->redirect(PermohonanPengujiResource::getUrl('index'));
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
