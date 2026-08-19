<?php

namespace App\Concerns;

use App\Enums\StatusPermohonan;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasPermohonanWorkflow
{
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

    public function formatRoleStatus(?string $status): string
    {
        return match ($status) {
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak / Dikembalikan',
            default => 'Menunggu',
        };
    }
}
