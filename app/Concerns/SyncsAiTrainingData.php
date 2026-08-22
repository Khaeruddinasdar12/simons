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

            try {
                JudulKorpus::syncFromPermohonan($model);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        static::deleted(function (self $model): void {
            try {
                JudulKorpus::query()
                    ->where('sumber_type', $model::class)
                    ->where('sumber_id', $model->getKey())
                    ->delete();
            } catch (\Throwable $e) {
                report($e);
            }
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

            if ($nama === '') {
                $this->setAttribute($idField, null);

                continue;
            }

            $id = Dosen::query()->where('nama', $nama)->value('id');

            if ($id !== null) {
                $this->setAttribute($idField, $id);
            }
        }
    }
}
