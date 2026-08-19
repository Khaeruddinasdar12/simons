<?php

namespace App\Http\Requests;

use App\Enums\StatusPermohonan;
use App\Models\Mahasiswa;
use App\Models\PermohonanPenguji;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePermohonanPengujiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $trimKeys = ['nim', 'penguji_1', 'penguji_2'];
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
            'penguji_1' => [
                'required',
                'string',
                'max:255',
                Rule::exists('dosens', 'nama')->where('is_active', true),
            ],
            'penguji_2' => [
                'required',
                'string',
                'max:255',
                Rule::exists('dosens', 'nama')->where('is_active', true),
                'different:penguji_1',
            ],
            'file_usul_penguji' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $nim = trim((string) $this->input('nim', ''));

            if ($nim === '') {
                return;
            }

            $mahasiswa = Mahasiswa::query()
                ->with('pembimbingTerbitTerakhir')
                ->find($nim);

            if (! $mahasiswa?->pembimbingTerbitTerakhir) {
                $validator->errors()->add(
                    'nim',
                    'SK Pembimbing untuk NIM ini belum terbit. Pengajuan SK Penguji belum dapat dilanjutkan.'
                );

                return;
            }

            $aktif = PermohonanPenguji::query()
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
                    'Masih ada permohonan SK Penguji yang aktif untuk NIM ini. Pantau di halaman tracking atau ajukan ulang setelah ditolak/SK terbit.'
                );

                return;
            }

            $skPembimbing = $mahasiswa->pembimbingTerbitTerakhir;
            $pembimbing = array_filter([
                $skPembimbing->pembimbing_1,
                $skPembimbing->pembimbing_2,
            ]);
            $penguji1 = trim((string) $this->input('penguji_1', ''));
            $penguji2 = trim((string) $this->input('penguji_2', ''));

            if ($penguji1 !== '' && in_array($penguji1, $pembimbing, true)) {
                $validator->errors()->add(
                    'penguji_1',
                    'Penguji 1 tidak boleh sama dengan dosen pembimbing pada SK Pembimbing yang sudah terbit.'
                );
            }

            if ($penguji2 !== '' && in_array($penguji2, $pembimbing, true)) {
                $validator->errors()->add(
                    'penguji_2',
                    'Penguji 2 tidak boleh sama dengan dosen pembimbing pada SK Pembimbing yang sudah terbit.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'nim.required' => 'NIM wajib diisi.',
            'penguji_1.required' => 'Penguji 1 wajib dipilih.',
            'penguji_2.required' => 'Penguji 2 wajib dipilih.',
            'penguji_1.exists' => 'Penguji 1 harus dipilih dari daftar dosen aktif.',
            'penguji_2.exists' => 'Penguji 2 harus dipilih dari daftar dosen aktif.',
            'penguji_2.different' => 'Penguji 1 dan Penguji 2 tidak boleh sama.',
            'file_usul_penguji.required' => 'File usulan penguji dari Kaprodi wajib diunggah.',
            'file_usul_penguji.mimes' => 'File usulan penguji harus berformat PDF, JPG, atau PNG.',
            'file_usul_penguji.max' => 'Ukuran file usulan penguji maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nim' => 'NIM',
            'penguji_1' => 'penguji 1',
            'penguji_2' => 'penguji 2',
            'file_usul_penguji' => 'file usulan penguji dari Kaprodi',
        ];
    }

    public function permohonanAttributes(): array
    {
        return $this->safe()->only([
            'penguji_1',
            'penguji_2',
        ]);
    }
}
