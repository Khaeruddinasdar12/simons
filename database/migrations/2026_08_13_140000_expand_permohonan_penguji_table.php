<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur sama dengan SK Pembimbing:
     * diajukan → dikirim_pimpinan → (kabag & wadek1) → sk_terbit
     */
    public function up(): void
    {
        Schema::dropIfExists('permohonan_penguji');

        Schema::create('permohonan_penguji', function (Blueprint $table) {
            $table->id();
            $table->string('mahasiswa_nim', 30);
            $table->foreignId('permohonan_pembimbing_id')
                ->constrained('permohonan_pembimbing')
                ->restrictOnDelete();
            $table->string('judul_skripsi');
            $table->unsignedTinyInteger('semester');
            $table->string('penguji_1');
            $table->string('penguji_2');
            $table->string('file_usul_penguji');

            $table->string('status', 40)->default('diajukan')->index();

            $table->foreignId('akademik_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('akademik_verified_at')->nullable();
            $table->timestamp('akademik_dikirim_at')->nullable();
            $table->text('akademik_catatan')->nullable();

            $table->foreignId('kabag_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('kabag_verified_at')->nullable();
            $table->enum('kabag_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('kabag_catatan')->nullable();

            $table->foreignId('wadek1_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('wadek1_verified_at')->nullable();
            $table->enum('wadek1_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('wadek1_catatan')->nullable();

            $table->foreignId('dekan_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dekan_verified_at')->nullable();
            $table->enum('dekan_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('dekan_catatan')->nullable();

            $table->string('nomor_sk')->nullable()->unique();
            $table->date('tanggal_sk')->nullable();
            $table->string('file_sk')->nullable();
            $table->string('sk_token', 64)->nullable()->unique();

            $table->timestamps();

            $table->foreign('mahasiswa_nim')
                ->references('nim')
                ->on('mahasiswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->index('mahasiswa_nim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_penguji');

        Schema::create('permohonan_penguji', function (Blueprint $table) {
            $table->id();
            $table->string('mahasiswa_nim', 30);
            $table->string('status', 40)->default('belum_dibuka')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('mahasiswa_nim')
                ->references('nim')
                ->on('mahasiswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->index('mahasiswa_nim');
        });
    }
};
