<?php

namespace App\Console\Commands;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use App\Services\SkPembimbingGenerator;
use App\Services\SkPengujiGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateMissingSkPdfCommand extends Command
{
    protected $signature = 'sk:generate-missing-pdf';

    protected $description = 'Buat PDF SK untuk permohonan yang sudah SK Terbit tetapi file PDF-nya belum ada.';

    public function handle(
        SkPembimbingGenerator $pembimbingGenerator,
        SkPengujiGenerator $pengujiGenerator,
    ): int {
        $this->generatePembimbing($pembimbingGenerator);
        $this->generatePenguji($pengujiGenerator);

        return self::SUCCESS;
    }

    protected function generatePembimbing(SkPembimbingGenerator $generator): void
    {
        $rows = PermohonanPembimbing::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->orderBy('id')
            ->get()
            ->filter(fn (PermohonanPembimbing $row): bool => $this->pdfMissing($row->file_sk));

        $this->info('SK Pembimbing tanpa PDF: '.$rows->count());

        foreach ($rows as $row) {
            try {
                $path = $generator->generate($row);
                $row->forceFill(['file_sk' => $path])->saveQuietly();
                $this->line("  #{$row->id} {$row->nomor_sk} -> {$path}");
            } catch (Throwable $e) {
                report($e);
                $this->error("  #{$row->id} gagal: ".$e->getMessage());
            }
        }
    }

    protected function generatePenguji(SkPengujiGenerator $generator): void
    {
        $rows = PermohonanPenguji::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->orderBy('id')
            ->get()
            ->filter(fn (PermohonanPenguji $row): bool => $this->pdfMissing($row->file_sk));

        $this->info('SK Penguji tanpa PDF: '.$rows->count());

        foreach ($rows as $row) {
            try {
                $path = $generator->generate($row);
                $row->forceFill(['file_sk' => $path])->saveQuietly();
                $this->line("  #{$row->id} {$row->nomor_sk} -> {$path}");
            } catch (Throwable $e) {
                report($e);
                $this->error("  #{$row->id} gagal: ".$e->getMessage());
            }
        }
    }

    protected function pdfMissing(?string $path): bool
    {
        return blank($path) || ! Storage::disk('public')->exists($path);
    }
}
