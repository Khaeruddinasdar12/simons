<?php

namespace App\Filament\Resources\KeahlianResource\Pages;

use App\Filament\Resources\KeahlianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKeahlian extends CreateRecord
{
    protected static string $resource = KeahlianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
