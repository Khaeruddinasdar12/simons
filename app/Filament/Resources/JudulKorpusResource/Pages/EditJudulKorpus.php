<?php

namespace App\Filament\Resources\JudulKorpusResource\Pages;

use App\Filament\Resources\JudulKorpusResource;
use Filament\Resources\Pages\EditRecord;

class EditJudulKorpus extends EditRecord
{
    protected static string $resource = JudulKorpusResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
