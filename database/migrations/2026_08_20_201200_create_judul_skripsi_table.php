<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judul_skripsi', function (Blueprint $table) {
            $table->id();
            $table->string('mahasiswa_nim', 30);
            $table->foreignId('permohonan_pembimbing_id')
                ->nullable()
                ->constrained('permohonan_pembimbing')
                ->nullOnDelete();
            $table->string('judul', 500);
            $table->boolean('is_aktif')->default(true);
            $table->string('sumber', 20)->default('pengajuan');
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('mahasiswa_nim')
                ->references('nim')
                ->on('mahasiswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['mahasiswa_nim', 'is_aktif']);
        });

        $this->backfillDariPermohonanPembimbing();
    }

    public function down(): void
    {
        Schema::dropIfExists('judul_skripsi');
    }

    private function backfillDariPermohonanPembimbing(): void
    {
        $permohonans = DB::table('permohonan_pembimbing')
            ->orderBy('mahasiswa_nim')
            ->orderBy('id')
            ->get(['id', 'mahasiswa_nim', 'judul_skripsi', 'created_at', 'updated_at']);

        $now = now();
        $judulSebelumnya = [];
        $idAktifPerNim = [];

        foreach ($permohonans as $permohonan) {
            $nim = $permohonan->mahasiswa_nim;
            $judul = trim((string) $permohonan->judul_skripsi);

            if ($judul === '') {
                continue;
            }

            if (($judulSebelumnya[$nim] ?? null) === $judul) {
                continue;
            }

            $id = DB::table('judul_skripsi')->insertGetId([
                'mahasiswa_nim' => $nim,
                'permohonan_pembimbing_id' => $permohonan->id,
                'judul' => $judul,
                'is_aktif' => false,
                'sumber' => 'pengajuan',
                'diubah_oleh' => null,
                'catatan' => null,
                'created_at' => $permohonan->created_at ?? $now,
                'updated_at' => $permohonan->updated_at ?? $now,
            ]);

            $judulSebelumnya[$nim] = $judul;
            $idAktifPerNim[$nim] = $id;
        }

        if ($idAktifPerNim !== []) {
            DB::table('judul_skripsi')
                ->whereIn('id', array_values($idAktifPerNim))
                ->update(['is_aktif' => true]);
        }
    }
};
