<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $fillable = [
        'nama',
        'nip',
        'is_active',
        'kuota_pembimbing',
        'kuota_penguji',
        'catatan_minat',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Dosen $dosen): void {
            foreach (['nip', 'kuota_pembimbing', 'kuota_penguji', 'catatan_minat'] as $field) {
                if ($dosen->{$field} === '') {
                    $dosen->{$field} = null;
                }
            }
        });
    }

    public function keahlians(): BelongsToMany
    {
        return $this->belongsToMany(Keahlian::class, 'dosen_keahlian')
            ->withTimestamps();
    }

    public function permohonanSebagaiPembimbing1(): HasMany
    {
        return $this->hasMany(PermohonanPembimbing::class, 'pembimbing_1_dosen_id');
    }

    public function permohonanSebagaiPembimbing2(): HasMany
    {
        return $this->hasMany(PermohonanPembimbing::class, 'pembimbing_2_dosen_id');
    }

    public function permohonanSebagaiPenguji1(): HasMany
    {
        return $this->hasMany(PermohonanPenguji::class, 'penguji_1_dosen_id');
    }

    public function permohonanSebagaiPenguji2(): HasMany
    {
        return $this->hasMany(PermohonanPenguji::class, 'penguji_2_dosen_id');
    }

    public function jumlahKeahlian(): int
    {
        if ($this->relationLoaded('keahlians')) {
            return $this->keahlians->count();
        }

        return $this->keahlians()->count();
    }

    public function siapUntukRekomendasi(): bool
    {
        return $this->is_active && $this->jumlahKeahlian() >= 2;
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Opsi select (nama => nama). Nama lama yang belum ada di master tetap disertakan.
     *
     * @return array<string, string>
     */
    public static function optionsForSelect(?string ...$include): array
    {
        $options = static::query()
            ->aktif()
            ->orderBy('nama')
            ->pluck('nama', 'nama')
            ->all();

        foreach ($include as $nama) {
            $nama = is_string($nama) ? trim($nama) : '';
            if ($nama !== '' && ! isset($options[$nama])) {
                $options[$nama] = $nama;
            }
        }

        return $options;
    }
}
