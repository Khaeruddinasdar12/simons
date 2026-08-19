<?php

namespace App\Models;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Support\JudulNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JudulKorpus extends Model
{
    protected $table = 'judul_korpus';

    protected $fillable = [
        'sumber_type',
        'sumber_id',
        'jenis',
        'mahasiswa_nim',
        'program_studi',
        'judul_skripsi',
        'judul_normalized',
        'tanggal_sk',
        'ditandai_mirip',
        'catatan_kurasi',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'ditandai_mirip' => 'boolean',
        'program_studi' => ProgramStudi::class,
    ];

    public function sumber(): MorphTo
    {
        return $this->morphTo();
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }

    public static function syncFromPermohonan(PermohonanPembimbing|PermohonanPenguji $permohonan): ?self
    {
        $permohonan->loadMissing('mahasiswa');

        if ($permohonan->status !== StatusPermohonan::SkTerbit) {
            static::query()
                ->where('sumber_type', $permohonan::class)
                ->where('sumber_id', $permohonan->getKey())
                ->delete();

            return null;
        }

        $judul = (string) $permohonan->judul_skripsi;

        return static::query()->updateOrCreate(
            [
                'sumber_type' => $permohonan::class,
                'sumber_id' => $permohonan->getKey(),
            ],
            [
                'jenis' => $permohonan instanceof PermohonanPenguji ? 'penguji' : 'pembimbing',
                'mahasiswa_nim' => $permohonan->mahasiswa_nim,
                'program_studi' => $permohonan->mahasiswa?->program_studi?->value
                    ?? $permohonan->mahasiswa?->program_studi,
                'judul_skripsi' => $judul,
                'judul_normalized' => JudulNormalizer::normalize($judul),
                'tanggal_sk' => $permohonan->tanggal_sk,
            ]
        );
    }
}
