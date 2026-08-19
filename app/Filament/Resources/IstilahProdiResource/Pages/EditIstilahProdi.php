<?php

namespace App\Filament\Resources\IstilahProdiResource\Pages;

use App\Filament\Resources\IstilahProdiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIstilahProdi extends EditRecord
{
    protected static string $resource = IstilahProdiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
