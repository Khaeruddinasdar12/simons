<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UndanganMunaqasyah extends Model
{
    protected $table = 'undangan_munaqasyah';

    protected $fillable = [
        'mahasiswa_nim',
        'status',
        'catatan',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }
}
