<?php

namespace App\Services;

use App\Models\JudulKorpus;
use App\Models\JudulSkripsi;
use App\Models\Mahasiswa;
use App\Models\PermohonanPembimbing;
use App\Models\PermohonanPenguji;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class HapusPermohonanService
{
    public function hapusPenguji(PermohonanPenguji $permohonan): void
    {
        DB::transaction(fn () => $this->deletePenguji($permohonan));
    }

    public function hapusPembimbing(PermohonanPembimbing $permohonan): void
    {
        DB::transaction(fn () => $this->deletePembimbing($permohonan));
    }

    public function hapusDataNim(string $nim): void
    {
        $nim = trim($nim);

        DB::transaction(function () use ($nim): void {
            $mahasiswa = Mahasiswa::query()->find($nim);

            if (! $mahasiswa) {
                throw ValidationException::withMessages([
                    'nim' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
                ]);
            }

            foreach ($mahasiswa->permohonanPenguji()->get() as $penguji) {
                $this->deletePenguji($penguji);
            }

            foreach ($mahasiswa->undanganMunaqasyah()->get() as $undangan) {
                $undangan->delete();
            }

            JudulSkripsi::query()->where('mahasiswa_nim', $nim)->delete();

            foreach ($mahasiswa->permohonanPembimbing()->get() as $pembimbing) {
                $this->deletePembimbing($pembimbing);
            }

            JudulKorpus::query()->where('mahasiswa_nim', $nim)->delete();

            $mahasiswa->delete();
        });
    }

    protected function deletePenguji(PermohonanPenguji $permohonan): void
    {
        $berkas = [
            $permohonan->file_usul_penguji,
            $permohonan->file_sk,
        ];

        $permohonan->delete();
        $this->hapusBerkas($berkas);
    }

    protected function deletePembimbing(PermohonanPembimbing $permohonan): void
    {
        foreach ($permohonan->permohonanPenguji()->get() as $penguji) {
            $this->deletePenguji($penguji);
        }

        $berkas = [
            $permohonan->file_usul_pembimbing,
            $permohonan->file_sk,
        ];

        $permohonan->delete();
        $this->hapusBerkas($berkas);
    }

    /**
     * @param  list<string|null>  $paths
     */
    protected function hapusBerkas(array $paths): void
    {
        foreach ($paths as $path) {
            if (! filled($path)) {
                continue;
            }

            Storage::disk('public')->delete($path);
        }
    }
}
