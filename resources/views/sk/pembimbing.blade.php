<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Pembimbing - {{ $permohonan->nomor_sk }}</title>
    <style>
        @include('sk.partials.document-css')
    </style>
    @if (! empty($isBrowserPreview))
    <style>
        html, body {
            margin: 0;
            padding: 0;
            background: #e8e8e8;
        }
        body.sk-browser-preview {
            padding: 0 0 24px;
        }
        .sk-toolbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            padding: 10px 16px;
            background: #1f2937;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            box-shadow: 0 1px 4px rgba(0,0,0,.25);
        }
        .sk-toolbar button {
            cursor: pointer;
            border: 0;
            border-radius: 4px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
        }
        .sk-toolbar .btn-print {
            background: #2563eb;
            color: #fff;
        }
        .sk-toolbar .btn-close {
            background: #e5e7eb;
            color: #111827;
        }
        .sk-sheet {
            width: 210mm;
            min-height: 330mm;
            margin: 16px auto;
            padding: 12mm 14mm;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,.18);
            box-sizing: border-box;
        }
        @@media print {
            html, body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .sk-toolbar {
                display: none !important;
            }
            .sk-sheet {
                width: auto !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
    @endif
</head>
<body class="{{ ! empty($isBrowserPreview) ? 'sk-browser-preview' : '' }}">
    @php
        $mhs = $mahasiswa ?? $permohonan->mahasiswa;
        $namaMhs = strtoupper((string) ($mhs->nama_lengkap ?? ''));
        $nimMhs = $mhs->nim ?? $permohonan->mahasiswa_nim;
        $judul = $judulSkripsi ?? $permohonan->judul_skripsi;
    @endphp

    @if (! empty($isBrowserPreview))
        <div class="sk-toolbar">
            <button type="button" class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
            <button type="button" class="btn-close" onclick="window.close()">Tutup</button>
        </div>
        <div class="sk-sheet">
    @endif

    @if (! empty($isPreview))
        <div class="preview-banner">PREVIEW - Dokumen ini belum resmi diterbitkan</div>
    @endif

    <div class="center">
        @if ($logoData)
            <img src="{{ $logoData }}" class="logo" alt="Logo IAIN Bone">
        @endif
        <p class="header-title">KEPUTUSAN</p>
        <p class="header-title">DEKAN FAKULTAS SYARIAH DAN HUKUM ISLAM</p>
        <p class="header-title">INSTITUT AGAMA ISLAM NEGERI BONE</p>
        <p class="nomor">NOMOR : {{ $permohonan->nomor_sk }}</p>
        <p class="tentang">TENTANG<br>PENETAPAN SK PEMBIMBING PROPOSAL SKRIPSI MAHASISWA</p>
        <p class="bismillah">DENGAN RAHMAT TUHAN YANG MAHA ESA</p>
        <p class="intro">DEKAN FAKULTAS SYARIAH DAN HUKUM ISLAM<br>INSTITUT AGAMA ISLAM NEGERI BONE</p>
    </div>

    <table class="meta" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="label" width="1%" valign="top">Membaca</td>
            <td class="colon" width="1%" valign="top">:</td>
            <td valign="top">
                Persetujuan pembimbing untuk menempuh ujian Proposal Skripsi mahasiswa dengan data sebagai berikut:
                <table class="kv" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="k" width="1%" valign="top">Nama</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td valign="top">{{ $namaMhs }}</td>
                    </tr>
                    <tr>
                        <td class="k" width="1%" valign="top">NIM</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td valign="top">{{ $nimMhs }}</td>
                    </tr>
                    <tr>
                        <td class="k" width="1%" valign="top">Prodi</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td valign="top">{{ $prodiLengkap }}</td>
                    </tr>
                    <tr>
                        <td class="k" width="1%" valign="top">Judul Skripsi</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td valign="top">{{ $judul }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" width="1%" valign="top">Menimbang</td>
            <td class="colon" width="1%" valign="top">:</td>
            <td valign="top">
                <table class="olist" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="n" width="1%" valign="top">1.</td>
                        <td valign="top">Bahwa mahasiswa yang tersebut namanya dalam Surat Keputusan ini telah memenuhi syarat untuk melakukan ujian proposal skripsi Program Studi {{ $prodiLengkap }} Fakultas Syariah dan Hukum Islam IAIN Bone;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">2.</td>
                        <td valign="top">
                            Bahwa menunjuk Saudara:
                            <table class="kv" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="k" width="1%" valign="top">Pembimbing 1</td>
                                    <td class="c" width="1%" valign="top">:</td>
                                    <td class="bold" valign="top">{{ $permohonan->pembimbing_1 }}</td>
                                </tr>
                                <tr>
                                    <td class="k" width="1%" valign="top">Pembimbing 2</td>
                                    <td class="c" width="1%" valign="top">:</td>
                                    <td class="bold" valign="top">{{ $permohonan->pembimbing_2 }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" width="1%" valign="top">Mengingat</td>
            <td class="colon" width="1%" valign="top">:</td>
            <td class="small" valign="top">
                <table class="olist" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="n" width="1%" valign="top">1.</td>
                        <td valign="top">Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">2.</td>
                        <td valign="top">Undang-Undang Nomor 14 Tahun 2005 tentang Guru dan Dosen;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">3.</td>
                        <td valign="top">Undang-Undang Nomor 12 Tahun 2012 tentang Pendidikan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">4.</td>
                        <td valign="top">Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">5.</td>
                        <td valign="top">Peraturan Menteri Agama RI Nomor 3 Tahun 2019 tentang Statuta Institut Agama Islam Negeri Bone (Berita Negara Republik Indonesia Tahun 2019 Nomor 148);</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">6.</td>
                        <td valign="top">Peraturan Menteri Agama RI Nomor 29 Tahun 2018 tentang Struktur Organisasi dan Tata Kerja Institut Agama Islam Negeri Bone;</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">7.</td>
                        <td valign="top">Peraturan Menteri Riset, Teknologi dan Pendidikan Tinggi Republik Indonesia Nomor 44 Tahun 2015 tentang Standar Nasional Pendidikan Tinggi (Berita Negara Republik Indonesia Tahun 2015 Nomor 1952);</td>
                    </tr>
                    <tr>
                        <td class="n" width="1%" valign="top">8.</td>
                        <td valign="top">Peraturan Menteri Keuangan Republik Indonesia Nomor 78/PMK.02/2019 tentang Standar Biaya Masukan Tahun Anggaran 2019 (Berita Negara Republik Indonesia Tahun 2019 Nomor 567);</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label" width="1%" valign="top">Memperhatikan</td>
            <td class="colon" width="1%" valign="top">:</td>
            <td valign="top">Surat persetujuan pembimbing proposal skripsi mahasiswa bersangkutan.</td>
        </tr>
    </table>

    <p class="memutuskan">MEMUTUSKAN</p>

    <table class="meta" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="label" width="1%" valign="top">Menetapkan</td>
            <td class="colon" width="1%" valign="top">:</td>
            <td valign="top">
                Surat Keputusan Dekan Fakultas Syariah dan Hukum Islam Institut Agama Islam Negeri Bone tentang pembimbing proposal skripsi mahasiswa Program Studi {{ $prodiLengkap }}.
            </td>
        </tr>
    </table>

    <table class="dict" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="dict-key" width="1%" valign="top">KESATU</td>
            <td class="dict-colon" width="1%" valign="top">:</td>
            <td class="dict-val" valign="top">
                Menunjuk Saudara(i):
                <table class="kv" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="k" width="1%" valign="top">Pembimbing 1</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td class="bold" valign="top">{{ $permohonan->pembimbing_1 }}</td>
                    </tr>
                    <tr>
                        <td class="k" width="1%" valign="top">Pembimbing 2</td>
                        <td class="c" width="1%" valign="top">:</td>
                        <td class="bold" valign="top">{{ $permohonan->pembimbing_2 }}</td>
                    </tr>
                </table>
                sebagai Pembimbing Proposal Skripsi saudara: {{ $namaMhs }}
            </td>
        </tr>
        <tr>
            <td class="dict-key" width="1%" valign="top">KEDUA</td>
            <td class="dict-colon" width="1%" valign="top">:</td>
            <td class="dict-val" valign="top">
                Segala biaya akibat diterbitkannya Surat Keputusan ini dibebankan kepada anggaran belanja DIPA Institut Agama Islam Negeri Bone tahun {{ $config['tahun_dipa'] }}.
            </td>
        </tr>
        <tr>
            <td class="dict-key" width="1%" valign="top">KETIGA</td>
            <td class="dict-colon" width="1%" valign="top">:</td>
            <td class="dict-val" valign="top">
                Segala sesuatu akan diubah dan dipertimbangkan kembali sebagaimana mestinya, apabila di kemudian hari terdapat kekeliruan dalam surat keputusan ini.
            </td>
        </tr>
    </table>

    <p class="closing">
        Salinan Surat Keputusan ini disampaikan kepada masing-masing yang bersangkutan untuk diketahui dan dilaksanakan dengan penuh tanggung jawab.
    </p>

    <table class="end-block" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="50%"></td>
            <td width="50%" class="ttd-box" valign="top">
                <div>Ditetapkan di : {{ $config['kota'] }}</div>
                <div>Pada Tanggal : {{ $permohonan->tanggal_sk?->translatedFormat('d F Y') }}</div>
                <div style="margin-top:3px;">{{ $config['penandatangan']['jabatan'] }}</div>
                <img src="{{ $qrTtd }}" class="qr-ttd" alt="QR Tanda Tangan">
                <div class="small">Scan QR untuk verifikasi TTD digital</div>
                <div class="nama">{{ $config['penandatangan']['nama'] }}</div>
                @if (! empty($config['penandatangan']['nip']))
                    <div>NIP. {{ $config['penandatangan']['nip'] }}</div>
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="2" class="footer-qr">
                <img src="{{ $qrTracking }}" alt="QR Tracking">
                <strong>QR Tracking:</strong> pindai untuk membuka halaman tracking status permohonan mahasiswa (NIM {{ $nimMhs }}).
            </td>
        </tr>
    </table>

    @if (! empty($isBrowserPreview))
        </div>
    @endif
</body>
</html>
