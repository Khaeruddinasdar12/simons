<?php

return [
    'fakultas' => 'Fakultas Syariah dan Hukum Islam',
    'institusi' => 'Institut Agama Islam Negeri Bone',
    'kota' => 'Watampone',

    'penandatangan' => [
        'jabatan' => 'Dekan Fakultas Syariah dan Hukum Islam IAIN Bone',
        'nama' => 'Dr. Syawaluddin Hanafi, S.H.I., M.H.',
        'nip' => null,
    ],

    'prodi_lengkap' => \App\Enums\ProgramStudi::options(),

    'tahun_dipa' => (string) date('Y'),

    // F4 / folio (kertas panjang): 210mm x 330mm in points
    'paper' => [0, 0, 595.28, 935.43],
];
