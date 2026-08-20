<?php

namespace App\Models;

use App\Enums\SumberJudulSkripsi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudulSkripsi extends Model
{
    protected $table = 'judul_skripsi';

    protected $fillable = [
        'mahasiswa_nim',
        'permohonan_pembimbing_id',
        'judul',
        'is_aktif',
        'sumber',
        'diubah_oleh',
        'catatan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'sumber' => SumberJudulSkripsi::class,
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }

    public function permohonanPembimbing(): BelongsTo
    {
        return $this->belongsTo(PermohonanPembimbing::class, 'permohonan_pembimbing_id');
    }

    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function labelSumber(): string
    {
        return $this->sumber?->label() ?? '-';
    }

    public function labelPengubah(): string
    {
        if ($this->sumber === SumberJudulSkripsi::Pengajuan) {
            return 'Mahasiswa (pengajuan)';
        }

        return $this->diubahOleh?->name ?? 'Akademik / Superadmin';
    }
}
