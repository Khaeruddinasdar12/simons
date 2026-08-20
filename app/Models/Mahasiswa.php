<?php

namespace App\Models;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    protected $primaryKey = 'nim';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_lengkap',
        'no_hp',
        'email',
        'program_studi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'program_studi' => ProgramStudi::class,
    ];

    public function permohonanPembimbing(): HasMany
    {
        return $this->hasMany(PermohonanPembimbing::class, 'mahasiswa_nim', 'nim');
    }

    public function judulSkripsi(): HasMany
    {
        return $this->hasMany(JudulSkripsi::class, 'mahasiswa_nim', 'nim');
    }

    public function judulSkripsiAktif(): HasOne
    {
        return $this->hasOne(JudulSkripsi::class, 'mahasiswa_nim', 'nim')
            ->where('is_aktif', true)
            ->latest('id');
    }

    public function judulTerkini(): ?string
    {
        $this->loadMissing('judulSkripsiAktif');

        if (filled($this->judulSkripsiAktif?->judul)) {
            return $this->judulSkripsiAktif->judul;
        }

        $this->loadMissing('pembimbingTerbitTerakhir');

        return $this->pembimbingTerbitTerakhir?->judul_skripsi;
    }

    public function permohonanPenguji(): HasMany
    {
        return $this->hasMany(PermohonanPenguji::class, 'mahasiswa_nim', 'nim');
    }

    public function undanganMunaqasyah(): HasMany
    {
        return $this->hasMany(UndanganMunaqasyah::class, 'mahasiswa_nim', 'nim');
    }

    public function pembimbingTerbitTerakhir(): HasOne
    {
        return $this->hasOne(PermohonanPembimbing::class, 'mahasiswa_nim', 'nim')
            ->where('status', StatusPermohonan::SkTerbit->value)
            ->latestOfMany();
    }

    public function pengujiTerbitTerakhir(): HasOne
    {
        return $this->hasOne(PermohonanPenguji::class, 'mahasiswa_nim', 'nim')
            ->where('status', StatusPermohonan::SkTerbit->value)
            ->latestOfMany();
    }

    public function punyaPermohonanPembimbingAktif(): bool
    {
        return $this->permohonanPembimbing()
            ->whereIn('status', [
                StatusPermohonan::Diajukan->value,
                StatusPermohonan::DikirimPimpinan->value,
                StatusPermohonan::DikembalikanAkademik->value,
            ])
            ->exists();
    }

    public function bisaAjukanUlangPembimbing(): bool
    {
        return ! $this->punyaPermohonanPembimbingAktif();
    }

    public function bisaAjukanPenguji(): bool
    {
        return $this->pembimbingTerbitTerakhir()->exists()
            && ! $this->permohonanPenguji()
                ->whereIn('status', [
                    StatusPermohonan::Diajukan->value,
                    StatusPermohonan::DikirimPimpinan->value,
                    StatusPermohonan::DikembalikanAkademik->value,
                ])
                ->exists();
    }

    public function bisaAjukanMunaqasyah(): bool
    {
        return $this->pengujiTerbitTerakhir()->exists()
            && ! $this->undanganMunaqasyah()
                ->whereIn('status', [
                    StatusPermohonan::Diajukan->value,
                    StatusPermohonan::DikirimPimpinan->value,
                    StatusPermohonan::DikembalikanAkademik->value,
                ])
                ->exists();
    }

    public function getTempatTanggalLahirAttribute(): string
    {
        return $this->tempat_lahir.', '.$this->tanggal_lahir?->translatedFormat('d F Y');
    }
}
