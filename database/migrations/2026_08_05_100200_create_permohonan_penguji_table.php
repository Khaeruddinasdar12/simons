<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('permohonan_penguji');
    }
};
