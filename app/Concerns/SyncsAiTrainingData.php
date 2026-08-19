<?php

namespace App\Concerns;

use App\Models\Dosen;
use App\Models\JudulKorpus;

trait SyncsAiTrainingData
{
    public static function bootSyncsAiTrainingData(): void
    {
        static::saving(function (self $model): void {
            $model->resolveDosenForeignKeys();
        });

        static::saved(function (self $model): void {
            if (
                ! $model->wasRecentlyCreated
                && ! $model->wasChanged(['status', 'judul_skripsi', 'mahasiswa_nim', 'tanggal_sk'])
            ) {
                return;
            }

            JudulKorpus::syncFromPermohonan($model);
        });

        static::deleted(function (self $model): void {
            JudulKorpus::query()
                ->where('sumber_type', $model::class)
                ->where('sumber_id', $model->getKey())
                ->delete();
        });
    }

    /**
     * @return array<string, string> nama field => id field
     */
    abstract protected function dosenNamaToIdMap(): array;

    protected function resolveDosenForeignKeys(): void
    {
        foreach ($this->dosenNamaToIdMap() as $namaField => $idField) {
            $nama = trim((string) $this->getAttribute($namaField));

            $this->setAttribute(
                $idField,
                $nama === ''
                    ? null
                    : Dosen::query()->where('nama', $nama)->value('id')
            );
        }
    }
}
