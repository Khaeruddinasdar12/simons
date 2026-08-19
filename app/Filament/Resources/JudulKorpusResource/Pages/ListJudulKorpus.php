<?php

namespace App\Filament\Resources\JudulKorpusResource\Pages;

use App\Filament\Resources\JudulKorpusResource;
use Filament\Resources\Pages\ListRecords;

class ListJudulKorpus extends ListRecords
{
    protected static string $resource = JudulKorpusResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
