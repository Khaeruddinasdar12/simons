<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Alur status:
     * diajukan → diverifikasi_akademik → dikirim_pimpinan
     * → (kabag & wadek1 bebas urutan) → disetujui_dekan / sk_terbit
     * atau ditolak di salah satu tahap.
     */
    public function up(): void
    {
        Schema::create('permohonan_pembimbing', function (Blueprint $table) {
            $table->id();

            // Data mahasiswa
            $table->string('nim', 30)->index();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat_lengkap');
            $table->string('no_hp', 20);
            $table->enum('program_studi', [
                'Hukum Tata Negara',
                'Hukum Ekonomi Syariah',
                'Hukum Keluarga Islam',
            ]);
            $table->string('judul_skripsi');
            $table->unsignedTinyInteger('semester');
            $table->string('pembimbing_1');
            $table->string('pembimbing_2')->nullable();
            $table->string('file_usul_pembimbing'); // path storage

            // Status keseluruhan
            $table->string('status', 40)->default('diajukan')->index();
            /*
             * diajukan
             * diverifikasi_akademik
             * dikirim_pimpinan
             * ditolak_akademik
             * ditolak_kabag
             * ditolak_wadek1
             * ditolak_dekan
             * sk_terbit
             */

            // —— Akademik ——
            $table->foreignId('akademik_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('akademik_verified_at')->nullable();
            $table->timestamp('akademik_dikirim_at')->nullable();
            $table->text('akademik_catatan')->nullable();

            // —— Kabag ——
            $table->foreignId('kabag_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('kabag_verified_at')->nullable();
            $table->enum('kabag_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('kabag_catatan')->nullable();

            // —— Wadek 1 ——
            $table->foreignId('wadek1_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('wadek1_verified_at')->nullable();
            $table->enum('wadek1_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('wadek1_catatan')->nullable();

            // —— Dekan (hanya setelah kabag & wadek1 disetujui) ——
            $table->foreignId('dekan_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dekan_verified_at')->nullable();
            $table->enum('dekan_status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('dekan_catatan')->nullable();

            // Penerbitan SK
            $table->string('nomor_sk')->nullable()->unique();
            $table->date('tanggal_sk')->nullable();
            $table->string('file_sk')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_pembimbing');
    }
};
