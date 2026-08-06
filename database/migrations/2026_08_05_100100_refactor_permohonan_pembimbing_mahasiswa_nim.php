<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->string('mahasiswa_nim', 30)->nullable()->after('id');
        });

        $rows = DB::table('permohonan_pembimbing')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('mahasiswas')->updateOrInsert(
                ['nim' => $row->nim],
                [
                    'nama_lengkap' => $row->nama_lengkap,
                    'tempat_lahir' => $row->tempat_lahir,
                    'tanggal_lahir' => $row->tanggal_lahir,
                    'alamat_lengkap' => $row->alamat_lengkap,
                    'no_hp' => $row->no_hp,
                    'email' => $row->email ?? null,
                    'program_studi' => $row->program_studi,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::table('permohonan_pembimbing')
                ->where('id', $row->id)
                ->update(['mahasiswa_nim' => $row->nim]);
        }

        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->string('mahasiswa_nim', 30)->nullable(false)->change();
            $table->foreign('mahasiswa_nim')
                ->references('nim')
                ->on('mahasiswas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->index('mahasiswa_nim');
        });

        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->dropIndex(['nim']);
            $table->dropColumn([
                'nim',
                'nama_lengkap',
                'tempat_lahir',
                'tanggal_lahir',
                'alamat_lengkap',
                'no_hp',
                'email',
                'program_studi',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->string('nim', 30)->nullable()->after('id');
            $table->string('nama_lengkap')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('program_studi', [
                'Hukum Tata Negara',
                'Hukum Ekonomi Syariah',
                'Hukum Keluarga Islam',
            ])->nullable();
        });

        $rows = DB::table('permohonan_pembimbing')
            ->join('mahasiswas', 'mahasiswas.nim', '=', 'permohonan_pembimbing.mahasiswa_nim')
            ->select(
                'permohonan_pembimbing.id',
                'mahasiswas.nim',
                'mahasiswas.nama_lengkap',
                'mahasiswas.tempat_lahir',
                'mahasiswas.tanggal_lahir',
                'mahasiswas.alamat_lengkap',
                'mahasiswas.no_hp',
                'mahasiswas.email',
                'mahasiswas.program_studi'
            )
            ->get();

        foreach ($rows as $row) {
            DB::table('permohonan_pembimbing')->where('id', $row->id)->update([
                'nim' => $row->nim,
                'nama_lengkap' => $row->nama_lengkap,
                'tempat_lahir' => $row->tempat_lahir,
                'tanggal_lahir' => $row->tanggal_lahir,
                'alamat_lengkap' => $row->alamat_lengkap,
                'no_hp' => $row->no_hp,
                'email' => $row->email,
                'program_studi' => $row->program_studi,
            ]);
        }

        Schema::table('permohonan_pembimbing', function (Blueprint $table) {
            $table->dropForeign(['mahasiswa_nim']);
            $table->dropColumn('mahasiswa_nim');
            $table->index('nim');
        });
    }
};
