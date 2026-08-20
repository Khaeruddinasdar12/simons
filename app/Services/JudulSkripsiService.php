<?php

namespace App\Services;

use App\Enums\SumberJudulSkripsi;
use App\Models\JudulKorpus;
use App\Models\JudulSkripsi;
use App\Models\PermohonanPembimbing;
use App\Models\User;
use App\Support\JudulNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JudulSkripsiService
{
    public function catatDariPermohonan(
        PermohonanPembimbing $permohonan,
        ?User $user = null,
        ?SumberJudulSkripsi $sumber = null,
    ): JudulSkripsi {
        return DB::transaction(function () use ($permohonan, $user, $sumber): JudulSkripsi {
            $judul = trim((string) $permohonan->judul_skripsi);
            $aktif = $this->kunciJudulAktif($permohonan->mahasiswa_nim);

            if ($aktif && $this->judulSama($aktif->judul, $judul)) {
                if (! $aktif->permohonan_pembimbing_id) {
                    $aktif->update(['permohonan_pembimbing_id' => $permohonan->id]);
                }

                return $aktif;
            }

            if ($aktif) {
                $aktif->update(['is_aktif' => false]);
            }

            return JudulSkripsi::query()->create([
                'mahasiswa_nim' => $permohonan->mahasiswa_nim,
                'permohonan_pembimbing_id' => $permohonan->id,
                'judul' => $judul,
                'is_aktif' => true,
                'sumber' => $sumber ?? ($user ? SumberJudulSkripsi::Perubahan : SumberJudulSkripsi::Pengajuan),
                'diubah_oleh' => $user?->id,
            ]);
        });
    }

    /**
     * Ubah judul untuk permohonan berikutnya (SK Penguji, munaqasyah).
     * SK Pembimbing yang sudah terbit tidak digenerate ulang.
     */
    public function ubah(
        PermohonanPembimbing $permohonan,
        string $judulBaru,
        User $user,
        ?string $catatan = null,
    ): JudulSkripsi {
        $judulBaru = trim($judulBaru);

        if ($judulBaru === '') {
            throw ValidationException::withMessages([
                'judul' => 'Judul skripsi wajib diisi.',
            ]);
        }

        return DB::transaction(function () use ($permohonan, $judulBaru, $user, $catatan): JudulSkripsi {
            $aktif = $this->kunciJudulAktif($permohonan->mahasiswa_nim);

            if ($aktif && $this->judulSama($aktif->judul, $judulBaru)) {
                throw ValidationException::withMessages([
                    'judul' => 'Judul baru sama dengan judul skripsi terkini.',
                ]);
            }

            if ($aktif) {
                $aktif->update(['is_aktif' => false]);
            }

            $baru = JudulSkripsi::query()->create([
                'mahasiswa_nim' => $permohonan->mahasiswa_nim,
                'permohonan_pembimbing_id' => $permohonan->id,
                'judul' => $judulBaru,
                'is_aktif' => true,
                'sumber' => SumberJudulSkripsi::Perubahan,
                'diubah_oleh' => $user->id,
                'catatan' => filled($catatan) ? trim($catatan) : null,
            ]);

            $this->sinkronkanKorpus($permohonan, $judulBaru);

            return $baru;
        });
    }

    public function generateUlangFileSkPembimbing(PermohonanPembimbing $permohonan): string
    {
        $permohonan = $permohonan->fresh(['mahasiswa']) ?? $permohonan;

        $lama = $permohonan->file_sk;
        $path = app(SkPembimbingGenerator::class)->generate($permohonan);
        $permohonan->forceFill(['file_sk' => $path])->saveQuietly();

        if (filled($lama) && $lama !== $path) {
            Storage::disk('public')->delete($lama);
        }

        return $path;
    }

    private function kunciJudulAktif(string $nim): ?JudulSkripsi
    {
        return JudulSkripsi::query()
            ->where('mahasiswa_nim', $nim)
            ->where('is_aktif', true)
            ->lockForUpdate()
            ->first();
    }

    private function sinkronkanKorpus(PermohonanPembimbing $permohonan, string $judul): void
    {
        JudulKorpus::query()
            ->where('mahasiswa_nim', $permohonan->mahasiswa_nim)
            ->update([
                'judul_skripsi' => $judul,
                'judul_normalized' => JudulNormalizer::normalize($judul),
            ]);
    }

    private function judulSama(string $a, string $b): bool
    {
        return trim($a) === trim($b);
    }
}
