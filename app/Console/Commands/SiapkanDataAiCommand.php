<?php

namespace App\Console\Commands;

use App\Enums\StatusPermohonan;
use App\Models\JudulKorpus;
use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use Illuminate\Console\Command;

class SiapkanDataAiCommand extends Command
{
    protected $signature = 'simons:siapkan-data-ai';

    protected $description = 'Tautkan nama dosen pada SK ke master dosen dan isi ulang korpus judul SK terbit.';

    public function handle(): int
    {
        $this->info('Menautkan nama pembimbing/penguji ke master dosen...');

        $pembimbingLinked = 0;
        PermohonanPembimbing::query()->orderBy('id')->each(function (PermohonanPembimbing $row) use (&$pembimbingLinked): void {
            $row->save();
            $pembimbingLinked++;
        });

        $pengujiLinked = 0;
        PermohonanPenguji::query()->orderBy('id')->each(function (PermohonanPenguji $row) use (&$pengujiLinked): void {
            $row->save();
            $pengujiLinked++;
        });

        $this->info("Diproses {$pembimbingLinked} permohonan pembimbing dan {$pengujiLinked} permohonan penguji.");

        $this->info('Mengisi korpus judul dari SK terbit...');

        $korpus = 0;
        PermohonanPembimbing::query()
            ->with('mahasiswa')
            ->where('status', StatusPermohonan::SkTerbit)
            ->orderBy('id')
            ->each(function (PermohonanPembimbing $row) use (&$korpus): void {
                JudulKorpus::syncFromPermohonan($row);
                $korpus++;
            });

        PermohonanPenguji::query()
            ->with('mahasiswa')
            ->where('status', StatusPermohonan::SkTerbit)
            ->orderBy('id')
            ->each(function (PermohonanPenguji $row) use (&$korpus): void {
                JudulKorpus::syncFromPermohonan($row);
                $korpus++;
            });

        $this->info("Korpus judul tersinkron: {$korpus} SK terbit.");
        $this->info('Selesai. Lanjut isi keahlian dosen di menu Master Data → Data Dosen.');

        return self::SUCCESS;
    }
}
