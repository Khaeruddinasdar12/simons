<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remap status lama ke alur baru:
     * - diverifikasi_akademik → diajukan
     * - ditolak_* (pimpinan) → dikembalikan_akademik
     * - ditolak_akademik → ditolak
     */
    public function up(): void
    {
        DB::table('permohonan_pembimbing')
            ->where('status', 'diverifikasi_akademik')
            ->update(['status' => 'diajukan']);

        DB::table('permohonan_pembimbing')
            ->whereIn('status', ['ditolak_kabag', 'ditolak_wadek1', 'ditolak_dekan'])
            ->update(['status' => 'dikembalikan_akademik']);

        DB::table('permohonan_pembimbing')
            ->where('status', 'ditolak_akademik')
            ->update(['status' => 'ditolak']);
    }

    public function down(): void
    {
        // Tidak dikembalikan ke status lama secara aman.
    }
};
