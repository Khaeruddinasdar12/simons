<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Penguji - {{ $permohonan->nomor_sk }}</title>
    <style>
        @page { margin: 12mm 14mm 12mm 14mm; size: 210mm 330mm; }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 9.5pt;
            color: #000;
            line-height: 1.18;
        }
        .center { text-align: center; }
        .logo { width: 52px; height: auto; margin-bottom: 2px; }
        .header-title {
            font-weight: bold;
            font-size: 10.5pt;
            line-height: 1.12;
            margin: 0;
        }
        .nomor {
            margin: 4px 0 2px;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .tentang {
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0 4px;
            font-size: 9.5pt;
            line-height: 1.15;
        }
        .bismillah {
            font-weight: bold;
            margin: 4px 0 2px;
            font-size: 9.5pt;
        }
        .intro {
            font-weight: bold;
            margin: 0 0 6px;
            font-size: 9.5pt;
            text-transform: uppercase;
            line-height: 1.15;
        }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        table.meta td { vertical-align: top; padding: 0.5px 0; }
        .label { width: 88px; font-weight: bold; }
        .colon { width: 10px; }
        .sub { margin: 0 0 0 14px; }
        .bold { font-weight: bold; }
        .memutuskan {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            margin: 5px 0 3px;
            letter-spacing: 1px;
        }
        .point { margin: 2px 0; }
        .point-label { font-weight: bold; }
        table.dict {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0 4px;
        }
        table.dict td {
            vertical-align: top;
            padding: 1px 0;
        }
        table.dict .dict-key {
            width: 78px;
            font-weight: bold;
            white-space: nowrap;
        }
        table.dict .dict-colon {
            width: 10px;
        }
        table.dict .dict-val {
            /* isi rata kiri sejajar */
        }
        table.dict-indent,
        table.detail-indent {
            width: 100%;
            border-collapse: collapse;
            margin: 1px 0 1px 0;
        }
        table.dict-indent td,
        table.detail-indent td {
            vertical-align: top;
            padding: 0.5px 0;
        }
        table.dict-indent .pad,
        table.detail-indent .pad {
            width: 14px;
        }
        table.dict-indent .k,
        table.detail-indent .k {
            width: 92px;
            white-space: nowrap;
        }
        table.dict-indent .c,
        table.detail-indent .c {
            width: 10px;
        }
        .ttd-wrap { width: 100%; margin-top: 8px; }
        .ttd-box {
            width: 46%;
            float: right;
            text-align: left;
        }
        .ttd-box .nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 2px;
            font-size: 9.5pt;
        }
        .qr-ttd {
            width: 58px;
            height: 58px;
            margin: 3px 0;
        }
        .footer-qr {
            clear: both;
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #999;
            font-size: 7.5pt;
        }
        .footer-qr img {
            width: 52px;
            height: 52px;
            vertical-align: middle;
            margin-right: 6px;
        }
        .small { font-size: 8pt; line-height: 1.15; }
        .clear { clear: both; }
        .preview-banner {
            background: #fff3cd;
            border: 1px solid #f0c36d;
            color: #7a4d00;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            padding: 3px;
            margin-bottom: 5px;
        }
        .closing { margin-top: 4px; }
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
        <p class="tentang">TENTANG<br>PENETAPAN SK PENGUJI SKRIPSI MAHASISWA</p>
        <p class="bismillah">DENGAN RAHMAT TUHAN YANG MAHA ESA</p>
        <p class="intro">DEKAN FAKULTAS SYARIAH DAN HUKUM ISLAM<br>INSTITUT AGAMA ISLAM NEGERI BONE</p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Membaca</td>
            <td class="colon">:</td>
            <td>
                Surat Permohonan mahasiswa a.n
                <table class="detail-indent">
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Nama</td>
                        <td class="c">:</td>
                        <td>{{ strtoupper(($mahasiswa ?? $permohonan->mahasiswa)->nama_lengkap) }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">NIM</td>
                        <td class="c">:</td>
                        <td>{{ ($mahasiswa ?? $permohonan->mahasiswa)->nim }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Prodi</td>
                        <td class="c">:</td>
                        <td>{{ $prodiLengkap }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Judul Skripsi</td>
                        <td class="c">:</td>
                        <td>{{ $permohonan->judul_skripsi }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label">Menimbang</td>
            <td class="colon">:</td>
            <td>
                <div>1.&nbsp;bahwa untuk menjamin kelancaran pelaksanaan ujian skripsi mahasiswa perlu ditetapkan SK Penguji Skripsi;</div>
                <div>2.&nbsp;bahwa berdasarkan pertimbangan sebagaimana dimaksud dalam huruf a, perlu menetapkan Keputusan Dekan tentang Penetapan SK Penguji Skripsi Mahasiswa atas nama:</div>
                <table class="detail-indent">
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Penguji 1</td>
                        <td class="c">:</td>
                        <td class="bold">{{ $permohonan->penguji_1 }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Penguji 2</td>
                        <td class="c">:</td>
                        <td class="bold">{{ $permohonan->penguji_2 }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="label">Mengingat</td>
            <td class="colon">:</td>
            <td class="small">
                <div>1.&nbsp;Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</div>
                <div>2.&nbsp;Undang-Undang Nomor 12 Tahun 2012 tentang Pendidikan Tinggi;</div>
                <div>3.&nbsp;Undang-Undang Nomor 5 Tahun 2014 tentang Aparatur Sipil Negara;</div>
                <div>4.&nbsp;Peraturan Pemerintah Nomor 4 Tahun 2014 tentang Penyelenggaraan Pendidikan Tinggi dan Pengelolaan Perguruan Tinggi;</div>
                <div>5.&nbsp;Peraturan Presiden Nomor 87 Tahun 2021 tentang Penggajian Pegawai Negeri Sipil;</div>
                <div>6.&nbsp;Keputusan Menteri Agama Nomor 289 Tahun 1997 tentang Pendirian STAIN Watampone;</div>
                <div>7.&nbsp;Peraturan Menteri Agama Nomor 11 Tahun 2024 tentang Organisasi dan Tata Kerja Institut Agama Islam Negeri Bone;</div>
                <div>8.&nbsp;Peraturan Menteri Agama Nomor 12 Tahun 2024 tentang Statuta Institut Agama Islam Negeri Bone;</div>
            </td>
        </tr>
        <tr>
            <td class="label">Memperhatikan</td>
            <td class="colon">:</td>
            <td>
                Surat Usulan Penguji Skripsi dari Ketua Program Studi {{ $prodiLengkap }}.
            </td>
        </tr>
    </table>

    <p class="memutuskan">MEMUTUSKAN</p>

    <table class="meta">
        <tr>
            <td class="label">Menetapkan</td>
            <td class="colon">:</td>
            <td>
                KEPUTUSAN DEKAN FAKULTAS SYARIAH DAN HUKUM ISLAM INSTITUT AGAMA ISLAM NEGERI BONE TENTANG PENETAPAN SK PENGUJI SKRIPSI MAHASISWA.
            </td>
        </tr>
    </table>

    <table class="dict">
        <tr>
            <td class="dict-key">KESATU</td>
            <td class="dict-colon">:</td>
            <td class="dict-val">
                Menunjuk dan menetapkan saudara tersebut di bawah ini:
                <table class="detail-indent">
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Penguji 1</td>
                        <td class="c">:</td>
                        <td class="bold">{{ $permohonan->penguji_1 }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Penguji 2</td>
                        <td class="c">:</td>
                        <td class="bold">{{ $permohonan->penguji_2 }}</td>
                    </tr>
                </table>
                sebagai Penguji Skripsi mahasiswa:
                <table class="detail-indent">
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Nama</td>
                        <td class="c">:</td>
                        <td>{{ strtoupper(($mahasiswa ?? $permohonan->mahasiswa)->nama_lengkap) }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">NIM</td>
                        <td class="c">:</td>
                        <td>{{ ($mahasiswa ?? $permohonan->mahasiswa)->nim }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Prodi</td>
                        <td class="c">:</td>
                        <td>{{ $prodiLengkap }}</td>
                    </tr>
                    <tr>
                        <td class="pad"></td>
                        <td class="k">Judul Skripsi</td>
                        <td class="c">:</td>
                        <td>{{ $permohonan->judul_skripsi }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="dict-key">KEDUA</td>
            <td class="dict-colon">:</td>
            <td class="dict-val">
                Segala biaya yang timbul akibat keputusan ini dibebankan pada DIPA IAIN Bone Tahun {{ $config['tahun_dipa'] }}.
            </td>
        </tr>
        <tr>
            <td class="dict-key">KETIGA</td>
            <td class="dict-colon">:</td>
            <td class="dict-val">
                Keputusan ini mulai berlaku pada tanggal ditetapkan dengan ketentuan apabila di kemudian hari terdapat kekeliruan dalam keputusan ini akan diadakan perbaikan sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <p class="closing">
        Keputusan Dekan ini disampaikan kepada yang bersangkutan untuk diketahui dan dilaksanakan sebagaimana mestinya.
    </p>

    <div class="ttd-wrap">
        <div class="ttd-box">
            <div>Ditetapkan di : {{ $config['kota'] }}</div>
            <div>Pada Tanggal : {{ $permohonan->tanggal_sk?->translatedFormat('d F Y') }}</div>
            <div style="margin-top:5px;">{{ $config['penandatangan']['jabatan'] }}</div>
            <img src="{{ $qrTtd }}" class="qr-ttd" alt="QR Tanda Tangan">
            <div class="small">Scan QR untuk verifikasi TTD digital</div>
            <div class="nama">{{ $config['penandatangan']['nama'] }}</div>
            @if (! empty($config['penandatangan']['nip']))
                <div>NIP. {{ $config['penandatangan']['nip'] }}</div>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer-qr">
        <img src="{{ $qrTracking }}" alt="QR Tracking">
        <strong>QR Tracking:</strong> pindai untuk membuka halaman tracking status permohonan mahasiswa (NIM {{ ($mahasiswa ?? $permohonan->mahasiswa)->nim }}).
    </div>

    @if (! empty($isBrowserPreview))
        </div>
    @endif
</body>
</html>