<?php

namespace App\Models;

use App\Concerns\HasPermohonanWorkflow;
use App\Concerns\SyncsAiTrainingData;
use App\Enums\StatusPermohonan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanPenguji extends Model
{
    use HasPermohonanWorkflow;
    use SyncsAiTrainingData;

    protected $table = 'permohonan_penguji';

    protected $fillable = [
        'mahasiswa_nim',
        'permohonan_pembimbing_id',
        'judul_skripsi',
        'semester',
        'penguji_1',
        'penguji_1_dosen_id',
        'penguji_2',
        'penguji_2_dosen_id',
        'file_usul_penguji',
        'status',
        'akademik_verified_by',
        'akademik_verified_at',
        'akademik_dikirim_at',
        'akademik_catatan',
        'kabag_verified_by',
        'kabag_verified_at',
        'kabag_status',
        'kabag_catatan',
        'wadek1_verified_by',
        'wadek1_verified_at',
        'wadek1_status',
        'wadek1_catatan',
        'dekan_verified_by',
        'dekan_verified_at',
        'dekan_status',
        'dekan_catatan',
        'nomor_sk',
        'tanggal_sk',
        'file_sk',
        'sk_token',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'semester' => 'integer',
        'status' => StatusPermohonan::class,
        'akademik_verified_at' => 'datetime',
        'akademik_dikirim_at' => 'datetime',
        'kabag_verified_at' => 'datetime',
        'wadek1_verified_at' => 'datetime',
        'dekan_verified_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }

    public function permohonanPembimbing(): BelongsTo
    {
        return $this->belongsTo(PermohonanPembimbing::class, 'permohonan_pembimbing_id');
    }

    public function judulTerkini(): string
    {
        return $this->mahasiswa?->judulTerkini() ?: (string) $this->judul_skripsi;
    }

    public function penguji1Dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'penguji_1_dosen_id');
    }

    public function penguji2Dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'penguji_2_dosen_id');
    }

    /**
     * @return array<string, string>
     */
    protected function dosenNamaToIdMap(): array
    {
        return [
            'penguji_1' => 'penguji_1_dosen_id',
            'penguji_2' => 'penguji_2_dosen_id',
        ];
    }

    public function getFileUsulUrlAttribute(): ?string
    {
        if (! $this->file_usul_penguji) {
            return null;
        }

        return '/storage/'.$this->file_usul_penguji;
    }
}
