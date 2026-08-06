<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tracking — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logoiainbone.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|manrope:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #0f241c;
            --forest: #14532d;
            --leaf: #166534;
            --paper: #f8faf8;
            --line: rgba(20, 83, 45, 0.14);
            --muted: rgba(15, 36, 28, 0.68);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 8%, rgba(34, 197, 94, 0.16), transparent 32%),
                linear-gradient(165deg, #f4fbf6 0%, var(--paper) 45%, #eef7f1 100%);
            min-height: 100vh;
        }
        .brand-font { font-family: 'Fraunces', Georgia, serif; }
        .shell { width: min(960px, calc(100% - 2rem)); margin: 0 auto; padding-bottom: 3rem; }
        .nav {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; padding: 1.25rem 0;
        }
        .logo-pair { display: flex; align-items: center; gap: .85rem; }
        .logo-pair img { height: 3.4rem; width: auto; object-fit: contain; }
        .nav a {
            text-decoration: none; color: var(--forest); font-weight: 600; font-size: .9rem;
            padding: .55rem .9rem; border-radius: .7rem;
        }
        .nav a.primary { background: var(--forest); color: #fff; }
        .field {
            width: 100%; border: 1px solid var(--line); background: #fff;
            border-radius: .8rem; padding: .8rem .95rem; outline: none; font: inherit;
        }
        .label { display: block; font-size: .86rem; font-weight: 700; margin-bottom: .35rem; color: var(--forest); }
        .card {
            border: 1px solid var(--line); background: rgba(255,255,255,.9);
            border-radius: 1.25rem; padding: 1.25rem 1.35rem; margin-top: 1rem;
            box-shadow: 0 12px 28px rgba(15, 36, 28, .05);
        }
        .btn {
            border: 0; border-radius: .9rem; padding: .9rem 1.25rem; font: inherit; font-weight: 700;
            color: #fff; cursor: pointer; background: linear-gradient(135deg, var(--forest), #15803d);
            white-space: nowrap;
        }
        .badge {
            display: inline-flex; align-items: center; border-radius: 999px;
            padding: .25rem .75rem; font-size: .75rem; font-weight: 700;
        }
        .pipeline {
            display: grid; gap: .75rem; margin-top: 1rem;
        }
        @media (min-width: 720px) {
            .pipeline { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .logo-pair img { height: 4.2rem; }
            .data-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .pipe-step {
            border: 1px solid var(--line); border-radius: 1rem; padding: .9rem 1rem; background: #fff;
        }
        .pipe-step strong { display: block; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase; color: var(--leaf); }
        .pipe-step p { margin: .4rem 0 0; font-size: .92rem; color: var(--muted); }
        .data-grid { display: grid; gap: .85rem; margin-top: 1rem; }
        .data-item {
            border: 1px solid var(--line); border-radius: .9rem; padding: .8rem .95rem; background: #fff;
        }
        .data-item span {
            display: block; font-size: .72rem; font-weight: 700; letter-spacing: .04em;
            text-transform: uppercase; color: var(--leaf); margin-bottom: .25rem;
        }
        .data-item p { margin: 0; font-size: .95rem; line-height: 1.45; }
        .note-box {
            border-left: 3px solid var(--leaf); background: #f3faf5;
            padding: .85rem 1rem; border-radius: 0 .75rem .75rem 0; margin-top: .65rem;
        }
        .full { grid-column: 1 / -1; }
    </style>
</head>
<body>
    <div class="shell">
        <nav class="nav">
            <div class="logo-pair">
                <img src="{{ asset('logokemenag.png') }}" alt="Logo Kementerian Agama">
                <img src="{{ asset('logoiainbone.png') }}" alt="Logo IAIN Bone">
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <a href="{{ route('home') }}">Ajukan</a>
                <a href="{{ url('/admin') }}" class="primary">Login Admin</a>
            </div>
        </nav>

        <header style="padding: .5rem 0 1rem;">
            <p class="brand-font" style="margin:0;font-size:2rem;color:var(--forest);">{{ config('app.name') }}</p>
            <h1 class="brand-font" style="margin:.4rem 0 0;font-size:clamp(1.5rem,3vw,2.2rem);">Tracking Mahasiswa</h1>
            <p style="margin:.55rem 0 0;color:var(--muted);max-width:36rem;">
                Masukkan NIM untuk melihat profil, status SK Pembimbing, serta tahapan SK Penguji dan Undangan Munaqasyah.
            </p>
        </header>

        @if (session('success'))
            <div class="card" style="background:#ecfdf3;border-color:rgba(22,101,52,.25);color:var(--forest);font-weight:600;">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('permohonan.tracking') }}" class="card">
            <label class="label" for="nim">NIM Mahasiswa</label>
            <div style="display:flex;flex-direction:column;gap:.75rem;">
                <input id="nim" name="nim" type="text" value="{{ $nim }}" class="field" placeholder="Contoh: 2010101001" required>
                <button type="submit" class="btn" style="align-self:flex-start;">Cari Status</button>
            </div>
            @error('nim') <p style="color:#b42318;font-size:.85rem;">{{ $message }}</p> @enderror
        </form>

        @if ($searched)
            @if (! $mahasiswa)
                <div class="card" style="text-align:center;color:var(--muted);">
                    Tidak ditemukan data mahasiswa untuk NIM <strong>{{ $nim }}</strong>.
                </div>
            @else
                @php
                    $pembimbingTerbaru = $permohonans->first();
                    $pengujiTerbaru = $mahasiswa->permohonanPenguji->first();
                    $munaqasyahTerbaru = $mahasiswa->undanganMunaqasyah->first();
                    $labelPembimbing = $pembimbingTerbaru?->status?->label() ?? 'Belum diajukan';
                    $labelPenguji = $pengujiTerbaru?->status ?? 'Belum dibuka';
                    $labelMunaqasyah = $munaqasyahTerbaru?->status ?? 'Belum dibuka';
                @endphp

                <article class="card">
                    <h2 class="brand-font" style="margin:0;font-size:1.45rem;color:var(--forest);">{{ $mahasiswa->nama_lengkap }}</h2>
                    <p style="margin:.35rem 0 0;color:var(--muted);">NIM {{ $mahasiswa->nim }} · {{ $mahasiswa->program_studi?->value }}</p>

                    <div class="pipeline">
                        <div class="pipe-step">
                            <strong>1. SK Pembimbing</strong>
                            <p>{{ $labelPembimbing }}</p>
                        </div>
                        <div class="pipe-step">
                            <strong>2. SK Penguji</strong>
                            <p>{{ is_string($labelPenguji) ? str_replace('_', ' ', $labelPenguji) : $labelPenguji }}</p>
                        </div>
                        <div class="pipe-step">
                            <strong>3. Undangan Munaqasyah</strong>
                            <p>{{ is_string($labelMunaqasyah) ? str_replace('_', ' ', $labelMunaqasyah) : $labelMunaqasyah }}</p>
                        </div>
                    </div>

                    <h3 style="margin:1.35rem 0 0;font-size:.8rem;letter-spacing:.05em;text-transform:uppercase;color:var(--leaf);">
                        Profil Mahasiswa
                    </h3>
                    <div class="data-grid">
                        <div class="data-item"><span>NIM</span><p>{{ $mahasiswa->nim }}</p></div>
                        <div class="data-item"><span>Nama Lengkap</span><p>{{ $mahasiswa->nama_lengkap }}</p></div>
                        <div class="data-item"><span>Tempat / Tanggal Lahir</span><p>{{ $mahasiswa->tempat_tanggal_lahir }}</p></div>
                        <div class="data-item"><span>No. HP</span><p>{{ $mahasiswa->no_hp }}</p></div>
                        <div class="data-item"><span>Email</span><p>{{ $mahasiswa->email ?: '-' }}</p></div>
                        <div class="data-item full"><span>Alamat Lengkap</span><p>{{ $mahasiswa->alamat_lengkap }}</p></div>
                        <div class="data-item"><span>Program Studi</span><p>{{ $mahasiswa->program_studi?->value }}</p></div>
                    </div>
                </article>

                @forelse ($permohonans as $item)
                    @php
                        $statusColor = match ($item->status) {
                            \App\Enums\StatusPermohonan::SkTerbit => 'background:#e8f5ee;color:#14532d;',
                            \App\Enums\StatusPermohonan::Ditolak => 'background:#fde8e8;color:#9b1c1c;',
                            \App\Enums\StatusPermohonan::DikembalikanAkademik => 'background:#e8f0fe;color:#1e40af;',
                            \App\Enums\StatusPermohonan::DikirimPimpinan => 'background:#fff7e6;color:#92400e;',
                            default => 'background:#f3f4f6;color:#374151;',
                        };
                    @endphp
                    <article class="card">
                        <div style="display:flex;flex-wrap:wrap;gap:.75rem;justify-content:space-between;align-items:flex-start;">
                            <div>
                                <h2 class="brand-font" style="margin:0;font-size:1.25rem;color:var(--forest);">SK Pembimbing</h2>
                                <p style="margin:.3rem 0 0;color:var(--muted);font-size:.92rem;">
                                    Diajukan {{ $item->created_at?->translatedFormat('d F Y H:i') }}
                                </p>
                            </div>
                            <span class="badge" style="{{ $statusColor }}">{{ $item->status->label() }}</span>
                        </div>

                        <div class="data-grid">
                            <div class="data-item"><span>Semester</span><p>{{ $item->semester }}</p></div>
                            <div class="data-item full"><span>Judul Skripsi</span><p>{{ $item->judul_skripsi }}</p></div>
                            <div class="data-item"><span>Pembimbing 1</span><p>{{ $item->pembimbing_1 }}</p></div>
                            <div class="data-item"><span>Pembimbing 2</span><p>{{ $item->pembimbing_2 ?: '-' }}</p></div>
                            <div class="data-item full">
                                <span>File Usul dari Prodi</span>
                                <p>
                                    @if ($item->file_usul_url)
                                        <a href="{{ $item->file_usul_url }}" target="_blank" rel="noopener" style="color:var(--forest);font-weight:700;">
                                            Lihat / unduh berkas
                                        </a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            @if ($item->nomor_sk)
                                <div class="data-item"><span>Nomor SK</span><p>{{ $item->nomor_sk }}</p></div>
                                <div class="data-item"><span>Tanggal SK</span><p>{{ $item->tanggal_sk?->translatedFormat('d F Y') ?: '-' }}</p></div>
                            @endif
                            @if ($item->file_sk)
                                <div class="data-item full">
                                    <span>File SK</span>
                                    <p>
                                        <a href="{{ route('sk.download', $item) }}" style="color:var(--forest);font-weight:700;">
                                            Unduh PDF SK Pembimbing
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <h3 style="margin:1.35rem 0 .35rem;font-size:.8rem;letter-spacing:.05em;text-transform:uppercase;color:var(--leaf);">
                            Catatan Perizinan
                        </h3>

                        <div class="note-box">
                            <p style="margin:0;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--leaf);">Akademik</p>
                            <p style="margin:.35rem 0 0;font-size:.92rem;">{{ $item->akademik_catatan ?: 'Belum ada catatan' }}</p>
                            @if ($item->akademik_dikirim_at)
                                <p style="margin:.3rem 0 0;font-size:.78rem;color:var(--muted);">Dikirim {{ $item->akademik_dikirim_at->translatedFormat('d F Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="note-box">
                            <p style="margin:0;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--leaf);">
                                Kabag — {{ $item->formatRoleStatus($item->kabag_status) }}
                            </p>
                            <p style="margin:.35rem 0 0;font-size:.92rem;">{{ $item->kabag_catatan ?: 'Belum ada catatan' }}</p>
                            @if ($item->kabag_verified_at)
                                <p style="margin:.3rem 0 0;font-size:.78rem;color:var(--muted);">{{ $item->kabag_verified_at->translatedFormat('d F Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="note-box">
                            <p style="margin:0;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--leaf);">
                                Wadek 1 — {{ $item->formatRoleStatus($item->wadek1_status) }}
                            </p>
                            <p style="margin:.35rem 0 0;font-size:.92rem;">{{ $item->wadek1_catatan ?: 'Belum ada catatan' }}</p>
                            @if ($item->wadek1_verified_at)
                                <p style="margin:.3rem 0 0;font-size:.78rem;color:var(--muted);">{{ $item->wadek1_verified_at->translatedFormat('d F Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="note-box">
                            <p style="margin:0;font-size:.75rem;font-weight:700;text-transform:uppercase;color:var(--leaf);">
                                Dekan — {{ $item->formatRoleStatus($item->dekan_status) }}
                            </p>
                            <p style="margin:.35rem 0 0;font-size:.92rem;">{{ $item->dekan_catatan ?: 'Belum ada catatan' }}</p>
                            @if ($item->dekan_verified_at)
                                <p style="margin:.3rem 0 0;font-size:.78rem;color:var(--muted);">{{ $item->dekan_verified_at->translatedFormat('d F Y H:i') }}</p>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="card" style="color:var(--muted);">
                        Belum ada permohonan SK Pembimbing untuk mahasiswa ini.
                    </div>
                @endforelse
            @endif
        @endif
    </div>
</body>
</html>
