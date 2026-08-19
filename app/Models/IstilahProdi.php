<?php

namespace App\Models;

use App\Enums\ProgramStudi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class IstilahProdi extends Model
{
    protected $fillable = [
        'istilah',
        'program_studi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'program_studi' => ProgramStudi::class,
    ];

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
