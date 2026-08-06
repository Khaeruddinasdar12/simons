<?php

namespace App\Models;

use App\Enums\StatusPermohonan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanPembimbing extends Model
{
    use HasFactory;

    protected $table = 'permohonan_pembimbing';

    protected $fillable = [
        'mahasiswa_nim',
        'judul_skripsi',
        'semester',
        'pembimbing_1',
        'pembimbing_2',
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

    public function akademikVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'akademik_verified_by');
    }

    public function kabagVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kabag_verified_by');
    }

    public function wadek1Verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wadek1_verified_by');
    }

    public function dekanVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dekan_verified_by');
    }

    public function isEditableByAkademik(): bool
    {
        return in_array($this->status, [
            StatusPermohonan::Diajukan,
            StatusPermohonan::DikembalikanAkademik,
        ], true);
    }

    public function canBeSentByAkademik(): bool
    {
        return in_array($this->status, [
            StatusPermohonan::Diajukan,
            StatusPermohonan::DikembalikanAkademik,
        ], true);
    }

    public function canBeRejectedByAkademik(): bool
    {
        return in_array($this->status, [
            StatusPermohonan::Diajukan,
            StatusPermohonan::DikembalikanAkademik,
        ], true);
    }

    public function isAwaitingPimpinan(): bool
    {
        return $this->status === StatusPermohonan::DikirimPimpinan;
    }

    public function kabagSudahVerifikasi(): bool
    {
        return $this->kabag_status === 'disetujui';
    }

    public function wadek1SudahVerifikasi(): bool
    {
        return $this->wadek1_status === 'disetujui';
    }

    public function canDekanVerify(): bool
    {
        return $this->isAwaitingPimpinan()
            && $this->kabagSudahVerifikasi()
            && $this->wadek1SudahVerifikasi()
            && $this->dekan_status === 'pending';
    }

    public function getFileUsulUrlAttribute(): ?string
    {
        if (! $this->file_usul_pembimbing) {
            return null;
        }

        return '/storage/'.$this->file_usul_pembimbing;
    }

    public function formatRoleStatus(?string $status): string
    {
        return match ($status) {
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak / Dikembalikan',
            default => 'Menunggu',
        };
    }
}
