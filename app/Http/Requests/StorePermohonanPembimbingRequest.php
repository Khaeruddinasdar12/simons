<?php

namespace App\Http\Requests;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Models\PermohonanPembimbing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePermohonanPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nim' => ['required', 'string', 'max:30'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'alamat_lengkap' => ['required', 'string', 'max:2000'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'program_studi' => ['required', Rule::enum(ProgramStudi::class)],
            'judul_skripsi' => ['required', 'string', 'max:500'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'pembimbing_1' => ['required', 'string', 'max:255'],
            'pembimbing_2' => ['required', 'string', 'max:255'],
            'file_usul_pembimbing' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $nim = trim((string) $this->input('nim', ''));

            if ($nim === '') {
                return;
            }

            $aktif = PermohonanPembimbing::query()
                ->where('mahasiswa_nim', $nim)
                ->whereIn('status', [
                    StatusPermohonan::Diajukan->value,
                    StatusPermohonan::DikirimPimpinan->value,
                    StatusPermohonan::DikembalikanAkademik->value,
                ])
                ->exists();

            if ($aktif) {
                $validator->errors()->add(
                    'nim',
                    'Masih ada permohonan SK Pembimbing yang aktif untuk NIM ini. Pantau di halaman tracking atau ajukan ulang setelah ditolak/SK terbit.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nim' => 'NIM',
            'nama_lengkap' => 'nama lengkap',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'alamat_lengkap' => 'alamat lengkap',
            'no_hp' => 'nomor HP',
            'email' => 'email',
            'program_studi' => 'program studi',
            'judul_skripsi' => 'judul skripsi',
            'semester' => 'semester',
            'pembimbing_1' => 'pembimbing 1',
            'pembimbing_2' => 'pembimbing 2',
            'file_usul_pembimbing' => 'file usul pembimbing dari Prodi',
        ];
    }

    public function mahasiswaAttributes(): array
    {
        return $this->safe()->only([
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat_lengkap',
            'no_hp',
            'email',
            'program_studi',
        ]);
    }

    public function permohonanAttributes(): array
    {
        return $this->safe()->only([
            'judul_skripsi',
            'semester',
            'pembimbing_1',
            'pembimbing_2',
        ]);
    }
}
