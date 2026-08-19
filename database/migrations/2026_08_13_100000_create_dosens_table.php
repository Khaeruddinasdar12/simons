<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosens', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('nama');
            $table->unique('nip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
