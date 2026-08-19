<?php

namespace App\Filament\Resources\IstilahProdiResource\Pages;

use App\Filament\Resources\IstilahProdiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIstilahProdi extends CreateRecord
{
    protected static string $resource = IstilahProdiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
