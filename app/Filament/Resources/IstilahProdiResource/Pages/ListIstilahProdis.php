<?php

namespace App\Filament\Resources\IstilahProdiResource\Pages;

use App\Filament\Resources\IstilahProdiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIstilahProdis extends ListRecords
{
    protected static string $resource = IstilahProdiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
