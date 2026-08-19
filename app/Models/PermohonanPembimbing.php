<?php

namespace App\Models;

use App\Concerns\HasPermohonanWorkflow;
use App\Concerns\SyncsAiTrainingData;
use App\Enums\StatusPermohonan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermohonanPembimbing extends Model
{
    use HasFactory;
    use HasPermohonanWorkflow;
    use SyncsAiTrainingData;

    protected $table = 'permohonan_pembimbing';

    protected $fillable = [
        'mahasiswa_nim',
        'judul_skripsi',
        'semester',
        'pembimbing_1',
        'pembimbing_1_dosen_id',
        'pembimbing_2',
        'pembimbing_2_dosen_id',
        'file_usul_pembimbing',
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

    public function permohonanPenguji(): HasMany
    {
        return $this->hasMany(PermohonanPenguji::class, 'permohonan_pembimbing_id');
    }

    public function pembimbing1Dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_1_dosen_id');
    }

    public function pembimbing2Dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_2_dosen_id');
    }

    /**
     * @return array<string, string>
     */
    protected function dosenNamaToIdMap(): array
    {
        return [
            'pembimbing_1' => 'pembimbing_1_dosen_id',
            'pembimbing_2' => 'pembimbing_2_dosen_id',
        ];
    }

    public function getFileUsulUrlAttribute(): ?string
    {
        if (! $this->file_usul_pembimbing) {
            return null;
        }

        return '/storage/'.$this->file_usul_pembimbing;
    }
}
