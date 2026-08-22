<?php

namespace App\Jobs;

use App\Models\PermohonanPembimbing;
use App\Services\SkPembimbingGenerator;
use App\Services\SkPembimbingMailService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FinalisasiSkPembimbingJob
{
    use Dispatchable;

    public function __construct(
        public int $permohonanId,
        public bool $kirimEmail = true,
        public bool $forcePdf = false,
    ) {}

    public function handle(): void
    {
        @set_time_limit(120);

        $record = PermohonanPembimbing::query()->find($this->permohonanId);

        if ($record === null) {
            return;
        }

        if ($this->forcePdf || blank($record->file_sk)) {
            try {
                $lama = $record->file_sk;
                $path = app(SkPembimbingGenerator::class)->generate($record);
                $record->forceFill(['file_sk' => $path])->saveQuietly();
                $record->refresh();

                if ($this->forcePdf && filled($lama) && $lama !== $path) {
                    Storage::disk('public')->delete($lama);
                }
            } catch (Throwable $e) {
                report($e);
            }
        }

        if (! $this->kirimEmail) {
            return;
        }

        try {
            app(SkPembimbingMailService::class)->sendTerbitNotification($record);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
