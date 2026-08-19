<?php

namespace App\Filament\Resources\PermohonanPengujiResource\Pages;

use App\Filament\Resources\PermohonanPengujiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPermohonanPenguji extends EditRecord
{
    protected static string $resource = PermohonanPengujiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
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
