<?php

namespace App\Http\Requests;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Models\Dosen;
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

    protected function prepareForValidation(): void
    {
        $trimKeys = [
            'nim',
            'nama_lengkap',
            'tempat_lahir',
            'alamat_lengkap',
            'no_hp',
            'email',
            'judul_skripsi',
            'pembimbing_1',
            'pembimbing_2',
        ];

        $trimmed = [];
        foreach ($trimKeys as $key) {
            if ($this->has($key) && is_string($this->input($key))) {
                $trimmed[$key] = trim($this->input($key));
            }
        }

        if ($trimmed !== []) {
            $this->merge($trimmed);
        }
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
            'pembimbing_1' => ['required', 'integer', Dosen::aktifIdRule()],
            'pembimbing_2' => [
                'required',
                'integer',
                Dosen::aktifIdRule(),
                'different:pembimbing_1',
            ],
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

    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'program_studi.required' => 'Program studi wajib dipilih.',
            'judul_skripsi.required' => 'Judul skripsi wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
            'pembimbing_1.required' => 'Pembimbing 1 wajib dipilih.',
            'pembimbing_2.required' => 'Pembimbing 2 wajib dipilih.',
            'pembimbing_1.integer' => 'Pembimbing 1 harus dipilih dari daftar dosen aktif.',
            'pembimbing_2.integer' => 'Pembimbing 2 harus dipilih dari daftar dosen aktif.',
            'pembimbing_1.exists' => 'Pembimbing 1 harus dipilih dari daftar dosen aktif.',
            'pembimbing_2.exists' => 'Pembimbing 2 harus dipilih dari daftar dosen aktif.',
            'pembimbing_2.different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
            'file_usul_pembimbing.required' => 'File usul pembimbing dari Prodi wajib diunggah.',
            'file_usul_pembimbing.mimes' => 'File usul pembimbing harus berformat PDF, JPG, atau PNG.',
            'file_usul_pembimbing.max' => 'Ukuran file usul pembimbing maksimal 5 MB.',
        ];
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
        $pembimbing1Id = (int) $this->validated('pembimbing_1');
        $pembimbing2Id = (int) $this->validated('pembimbing_2');

        return [
            'judul_skripsi' => $this->validated('judul_skripsi'),
            'semester' => $this->validated('semester'),
            'pembimbing_1' => Dosen::namaById($pembimbing1Id),
            'pembimbing_2' => Dosen::namaById($pembimbing2Id),
            'pembimbing_1_dosen_id' => $pembimbing1Id,
            'pembimbing_2_dosen_id' => $pembimbing2Id,
        ];
    }
}
