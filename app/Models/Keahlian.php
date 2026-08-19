<?php

namespace App\Models;

use App\Enums\ProgramStudi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keahlian extends Model
{
    protected $fillable = [
        'nama',
        'program_studi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'program_studi' => ProgramStudi::class,
    ];

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'dosen_keahlian')
            ->withTimestamps();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
