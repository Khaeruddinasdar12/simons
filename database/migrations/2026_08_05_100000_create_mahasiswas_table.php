<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->string('nim', 30)->primary();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat_lengkap');
            $table->string('no_hp', 20);
            $table->string('email')->nullable();
            $table->enum('program_studi', [
                'Hukum Tata Negara',
                'Hukum Ekonomi Syariah',
                'Hukum Keluarga Islam',
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
