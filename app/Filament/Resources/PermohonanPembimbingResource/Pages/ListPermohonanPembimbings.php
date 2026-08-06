<?php

namespace App\Filament\Resources\PermohonanPembimbingResource\Pages;

use App\Filament\Resources\PermohonanPembimbingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanPembimbings extends ListRecords
{
    protected static string $resource = PermohonanPembimbingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
