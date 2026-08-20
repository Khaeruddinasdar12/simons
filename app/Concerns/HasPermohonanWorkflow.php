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

    /**
     * @return list<array{key: string, label: string, state: 'done'|'current'|'wait'|'reject'}>
     */
    public function progresPublik(): array
    {
        $step = fn (string $key, string $label, string $state): array => [
            'key' => $key,
            'label' => $label,
            'state' => $state,
        ];

        if ($this->status === StatusPermohonan::SkTerbit) {
            return [
                $step('akademik', 'Akademik', 'done'),
                $step('kabag', 'Kabag', 'done'),
                $step('wadek1', 'Wadek 1', 'done'),
                $step('dekan', 'Dekan', 'done'),
                $step('sk', 'SK terbit', 'done'),
            ];
        }

        if ($this->status === StatusPermohonan::Ditolak) {
            return [
                $step('akademik', 'Akademik', 'reject'),
                $step('kabag', 'Kabag', 'wait'),
                $step('wadek1', 'Wadek 1', 'wait'),
                $step('dekan', 'Dekan', 'wait'),
                $step('sk', 'SK terbit', 'wait'),
            ];
        }

        if ($this->status === StatusPermohonan::Diajukan) {
            return [
                $step('akademik', 'Akademik', 'current'),
                $step('kabag', 'Kabag', 'wait'),
                $step('wadek1', 'Wadek 1', 'wait'),
                $step('dekan', 'Dekan', 'wait'),
                $step('sk', 'SK terbit', 'wait'),
            ];
        }

        $kabag = $this->roleProgresState($this->kabag_status, $this->status === StatusPermohonan::DikirimPimpinan);
        $wadek = $this->roleProgresState($this->wadek1_status, $this->status === StatusPermohonan::DikirimPimpinan);
        $pimpinanSelesai = $this->kabag_status === 'disetujui' && $this->wadek1_status === 'disetujui';
        $dekan = $this->roleProgresState(
            $this->dekan_status,
            $this->status === StatusPermohonan::DikirimPimpinan && $pimpinanSelesai
        );

        if ($this->status === StatusPermohonan::DikembalikanAkademik) {
            return [
                $step('akademik', 'Akademik', 'current'),
                $step('kabag', 'Kabag', $this->roleProgresState($this->kabag_status, false)),
                $step('wadek1', 'Wadek 1', $this->roleProgresState($this->wadek1_status, false)),
                $step('dekan', 'Dekan', $this->roleProgresState($this->dekan_status, false)),
                $step('sk', 'SK terbit', 'wait'),
            ];
        }

        return [
            $step('akademik', 'Akademik', 'done'),
            $step('kabag', 'Kabag', $kabag),
            $step('wadek1', 'Wadek 1', $wadek),
            $step('dekan', 'Dekan', $dekan),
            $step('sk', 'SK terbit', 'wait'),
        ];
    }

    public function catatanPublik(): ?string
    {
        $isi = match ($this->status) {
            StatusPermohonan::Ditolak => $this->akademik_catatan,
            StatusPermohonan::DikembalikanAkademik => $this->dekan_catatan
                ?: $this->wadek1_catatan
                ?: $this->kabag_catatan
                ?: $this->akademik_catatan,
            default => null,
        };

        return filled($isi) ? $isi : null;
    }

    protected function roleProgresState(?string $status, bool $pendingIsCurrent): string
    {
        return match ($status) {
            'disetujui' => 'done',
            'ditolak' => 'reject',
            default => $pendingIsCurrent ? 'current' : 'wait',
        };
    }
}
