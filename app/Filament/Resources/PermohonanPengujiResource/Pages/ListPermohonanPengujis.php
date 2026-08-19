<?php

namespace App\Filament\Resources\PermohonanPengujiResource\Pages;

use App\Filament\Resources\PermohonanPengujiResource;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanPengujis extends ListRecords
{
    protected static string $resource = PermohonanPengujiResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
