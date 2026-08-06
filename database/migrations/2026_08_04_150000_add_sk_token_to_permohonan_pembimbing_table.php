<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->string('sk_token', 64)->nullable()->unique()->after('file_sk');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->dropColumn('sk_token');
        });
    }
};
