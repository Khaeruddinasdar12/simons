<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi TTD Digital SK — FSHI</title>
    <link rel="icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link href="https://fonts.bunny.net/css?family=manrope:400,600,700|fraunces:600&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: Manrope, sans-serif;
            background: linear-gradient(165deg, #f4fbf6, #eef7f1);
            color: #0f241c;
            min-height: 100vh;
        }
        .box {
            width: min(640px, calc(100% - 2rem));
            margin: 2.5rem auto;
            background: #fff;
            border: 1px solid rgba(20,83,45,.15);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 12px 28px rgba(15,36,28,.06);
        }
        .ok {
            display: inline-block;
            background: #ecfdf3;
            color: #14532d;
            font-weight: 700;
            border-radius: 999px;
            padding: .35rem .8rem;
            font-size: .85rem;
        }
        h1 { font-family: Fraunces, serif; font-size: 1.6rem; margin: .8rem 0 .4rem; color: #14532d; }
        .row { margin: .55rem 0; }
        .label { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #166534; font-weight: 700; }
        a { color: #14532d; font-weight: 700; }
    </style>
</head>
<body>
    <div class="box">
        <div style="display:flex;align-items:center;gap:.75rem;">
            <img src="{{ asset('logoiainbone.png') }}" alt="Logo" style="height:3rem;">
            <span class="ok">TTD Digital Terverifikasi</span>
        </div>
        <h1>Surat Keputusan Pembimbing Valid</h1>
        <p style="color:rgba(15,36,28,.7);">Dokumen SK ini sah dan telah diterbitkan melalui sistem FSHI IAIN Bone.</p>

        <div class="row"><div class="label">Nomor SK</div>{{ $permohonan->nomor_sk }}</div>
        <div class="row"><div class="label">Tanggal SK</div>{{ $permohonan->tanggal_sk?->translatedFormat('d F Y') }}</div>
        <div class="row"><div class="label">Mahasiswa</div>{{ $permohonan->mahasiswa->nama_lengkap }} ({{ $permohonan->mahasiswa->nim }})</div>
        <div class="row"><div class="label">Program Studi</div>{{ $permohonan->mahasiswa->program_studi?->getLabel() }}</div>
        <div class="row"><div class="label">Pembimbing 1</div>{{ $permohonan->pembimbing_1 }}</div>
        <div class="row"><div class="label">Pembimbing 2</div>{{ $permohonan->pembimbing_2 }}</div>
        <div class="row"><div class="label">Penandatangan</div>{{ $config['penandatangan']['nama'] }} — {{ $config['penandatangan']['jabatan'] }}</div>

        <p style="margin-top:1.25rem;">
            <a href="{{ route('permohonan.tracking', ['nim' => $permohonan->mahasiswa->nim]) }}">Lihat tracking permohonan</a>
            @if ($permohonan->file_sk)
                &nbsp;·&nbsp;
                <a href="{{ route('sk.download', $permohonan) }}">Unduh PDF SK</a>
            @endif
        </p>
    </div>
</body>
</html>
