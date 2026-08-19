<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosens', function (Blueprint $table) {
            $table->unsignedTinyInteger('kuota_pembimbing')->nullable()->after('is_active');
            $table->unsignedTinyInteger('kuota_penguji')->nullable()->after('kuota_pembimbing');
            $table->text('catatan_minat')->nullable()->after('kuota_penguji');
        });

        Schema::create('keahlians', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('program_studi')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('nama');
        });

        Schema::create('dosen_keahlian', function (Blueprint $table) {
            $table->foreignId('dosen_id')->constrained('dosens')->cascadeOnDelete();
            $table->foreignId('keahlian_id')->constrained('keahlians')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['dosen_id', 'keahlian_id']);
        });

        Schema::create('istilah_prodis', function (Blueprint $table) {
            $table->id();
            $table->string('istilah');
            $table->string('program_studi')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['istilah', 'program_studi']);
        });

        Schema::create('judul_korpus', function (Blueprint $table) {
            $table->id();
            $table->string('sumber_type');
            $table->unsignedBigInteger('sumber_id');
            $table->string('jenis', 20)->index();
            $table->string('mahasiswa_nim', 30)->index();
            $table->string('program_studi')->nullable()->index();
            $table->string('judul_skripsi');
            $table->string('judul_normalized')->index();
            $table->date('tanggal_sk')->nullable();
            $table->boolean('ditandai_mirip')->default(false);
            $table->text('catatan_kurasi')->nullable();
            $table->timestamps();

            $table->unique(['sumber_type', 'sumber_id']);
            $table->index(['jenis', 'program_studi']);
        });

        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->foreignId('pembimbing_1_dosen_id')->nullable()->after('pembimbing_1')->constrained('dosens')->nullOnDelete();
            $table->foreignId('pembimbing_2_dosen_id')->nullable()->after('pembimbing_2')->constrained('dosens')->nullOnDelete();
        });

        Schema::table('permohonan_penguji', function (Blueprint $table) {
            $table->foreignId('penguji_1_dosen_id')->nullable()->after('penguji_1')->constrained('dosens')->nullOnDelete();
            $table->foreignId('penguji_2_dosen_id')->nullable()->after('penguji_2')->constrained('dosens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_penguji', function (Blueprint $table) {
            $table->dropConstrainedForeignId('penguji_1_dosen_id');
            $table->dropConstrainedForeignId('penguji_2_dosen_id');
        });

        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pembimbing_1_dosen_id');
            $table->dropConstrainedForeignId('pembimbing_2_dosen_id');
        });

        Schema::dropIfExists('judul_korpus');
        Schema::dropIfExists('istilah_prodis');
        Schema::dropIfExists('dosen_keahlian');
        Schema::dropIfExists('keahlians');

        Schema::table('dosens', function (Blueprint $table) {
            $table->dropColumn(['kuota_pembimbing', 'kuota_penguji', 'catatan_minat']);
        });
    }
};
