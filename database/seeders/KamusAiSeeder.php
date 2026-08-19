<?php

namespace Database\Seeders;

use App\Enums\ProgramStudi;
use App\Models\IstilahProdi;
use App\Models\Keahlian;
use Illuminate\Database\Seeder;

class KamusAiSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->keahlians() as $item) {
            Keahlian::query()->updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'program_studi' => $item['program_studi'],
                    'is_active' => true,
                ]
            );
        }

        foreach ($this->istilah() as $prodi => $daftar) {
            foreach ($daftar as $istilah) {
                IstilahProdi::query()->updateOrCreate(
                    [
                        'istilah' => $istilah,
                        'program_studi' => $prodi,
                    ],
                    ['is_active' => true]
                );
            }
        }
    }

    /**
     * @return list<array{nama: string, program_studi: ?string}>
     */
    protected function keahlians(): array
    {
        return [
            ['nama' => 'Siyasah Dusturiyah', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Hukum Tata Negara', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Konstitusi dan Perundang-undangan', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Hukum Pemerintahan Daerah', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Hak Asasi Manusia', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Fiqh Siyasah', 'program_studi' => ProgramStudi::HukumTataNegara->value],
            ['nama' => 'Hukum Pidana Islam / Jinayah', 'program_studi' => ProgramStudi::HukumTataNegara->value],

            ['nama' => 'Muamalah', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Perbankan Syariah', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Akad dan Kontrak Syariah', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Wakaf Produktif', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Zakat dan Filantropi Islam', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Pasar Modal / Fintech Syariah', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],
            ['nama' => 'Hukum Bisnis Syariah', 'program_studi' => ProgramStudi::HukumEkonomiSyariah->value],

            ['nama' => 'Ahwal Syakhshiyyah', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],
            ['nama' => 'Hukum Perkawinan Islam', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],
            ['nama' => 'Hukum Kewarisan Islam', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],
            ['nama' => 'Perceraian dan Hadhanah', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],
            ['nama' => 'Kompilasi Hukum Islam', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],
            ['nama' => 'Hukum Keluarga Kontemporer', 'program_studi' => ProgramStudi::HukumKeluargaIslam->value],

            ['nama' => 'Ushul Fiqh', 'program_studi' => null],
            ['nama' => 'Metodologi Penelitian Hukum', 'program_studi' => null],
            ['nama' => 'Peradilan Agama', 'program_studi' => null],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    protected function istilah(): array
    {
        return [
            ProgramStudi::HukumTataNegara->value => [
                'siyasah', 'dusturiyah', 'konstitusi', 'undang-undang dasar', 'pilkada',
                'pemilu', 'dprd', 'kepala daerah', 'otonomi daerah', 'peraturan daerah',
                'hak asasi manusia', 'kebebasan berpendapat', 'judicial review', 'mahkamah konstitusi',
                'jinayah', 'hudud', 'ta zir', 'siyasah syar iyyah', 'imamah', 'bai at',
                'good governance', 'korupsi', 'ombudsman', 'kewarganegaraan',
            ],
            ProgramStudi::HukumEkonomiSyariah->value => [
                'muamalah', 'murabahah', 'mudharabah', 'musyarakah', 'ijarah', 'istisna',
                'salam', 'wakalah', 'kafalah', 'qardh', 'perbankan syariah', 'lembaga keuangan syariah',
                'fatwa dsn', 'dsn-mui', 'fintech syariah', 'sukuk', 'gadai syariah', 'rahn',
                'zakat produktif', 'wakaf produktif', 'baitul mal', 'koperasi syariah',
                'akad', 'wanprestasi', 'sengketa ekonomi syariah', 'ojk',
            ],
            ProgramStudi::HukumKeluargaIslam->value => [
                'ahwal', 'syakhshiyyah', 'nikah', 'perkawinan', 'talak', 'cerai', 'fasakh',
                'khuluk', 'rujuk', 'hadhanah', 'nafkah', 'mahar', 'wali nikah', 'poligami',
                'waris', 'faraid', 'wasiat', 'hibah', 'harta bersama', 'dispensasi nikah',
                'itsbat nikah', 'kompilasi hukum islam', 'khi', 'peradilan agama',
                'hak asuh anak', 'kawin kontrak', 'sirri',
            ],
        ];
    }
}
