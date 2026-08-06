<?php

namespace App\Filament\Resources\PermohonanPembimbingResource\Pages;

use App\Filament\Resources\PermohonanPembimbingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPermohonanPembimbing extends EditRecord
{
    protected static string $resource = PermohonanPembimbingResource::class;

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
