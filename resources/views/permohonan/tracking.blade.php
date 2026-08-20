<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelacakan — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logoiainbone.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #202124;
            --forest: #137333;
            --muted: #5f6368;
            --line: #e8eaed;
            --soft: #f8f9fa;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, Roboto, 'Segoe UI', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: var(--ink);
            background: #fff;
            min-height: 100vh;
            overflow-x: clip;
        }
        .shell { width: min(1080px, calc(100% - 1.75rem)); margin: 0 auto; padding-bottom: 3rem; }
        @include('permohonan.partials.public-nav-css')
        .hero { padding: 1.5rem 0 1.35rem; }
        .hero-kicker {
            margin: 0 0 .35rem;
            font-size: .75rem;
            font-weight: 500;
            color: var(--muted);
        }
        .hero h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3.5vw, 1.75rem);
            font-weight: 500;
            letter-spacing: -.015em;
            line-height: 1.25;
        }
        .hero p { margin: .5rem 0 0; color: var(--muted); max-width: 36rem; font-size: .9375rem; line-height: 1.55; }
        .field {
            width: 100%; border: 1px solid var(--line); background: var(--soft);
            border-radius: .5rem; padding: .85rem .95rem; min-height: 3rem; outline: none; font: inherit; font-size: 16px;
        }
        .field:focus { background: #fff; border-color: var(--forest); box-shadow: 0 0 0 3px rgba(19,115,51,.12); }
        .label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: .35rem; }
        .block { padding: 1.35rem 0; border-top: 1px solid var(--line); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            border: 0; border-radius: .6rem; padding: .75rem 1.05rem; font: inherit; font-weight: 600;
            font-size: .9375rem; color: #fff; cursor: pointer; background: var(--forest);
            text-decoration: none; min-height: 2.75rem;
        }
        .btn.ghost { background: #fff; color: var(--forest); border: 1px solid #c4e4cc; }
        .search-row { display: flex; flex-direction: column; gap: .65rem; }
        .search-row .btn { width: 100%; }
        @media (min-width: 640px) {
            .search-row { flex-direction: row; align-items: stretch; }
            .search-row .field { flex: 1 1 auto; }
            .search-row .btn { width: auto; min-height: 2.6rem; padding: .55rem 1.05rem; font-size: .875rem; }
        }
        @media (min-width: 720px) {
            .shell { width: min(1080px, calc(100% - 2rem)); }
            .hero h1 { font-weight: 500; }
            .btn { min-height: 2.5rem; padding: .65rem 1.05rem; font-size: .9rem; }
        }
        .badge {
            display: inline-flex; align-items: center; border-radius: 999px;
            padding: .2rem .7rem; font-size: .75rem; font-weight: 600; background: var(--soft); color: var(--muted);
            white-space: nowrap;
        }
        .identitas h2 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .identitas p { margin: .3rem 0 0; color: var(--muted); font-size: .9375rem; }
        .group { margin-top: 1.5rem; }
        .group h2 {
            margin: 0 0 .75rem;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .stack { display: grid; gap: .85rem; }
        .panel {
            border: 1px solid var(--line);
            border-radius: .85rem;
            padding: 1.05rem 1.1rem 1.15rem;
            background: #fff;
        }
        .panel.is-empty { background: var(--soft); }
        .panel-head {
            display: flex;
            gap: .75rem;
            justify-content: space-between;
            align-items: flex-start;
        }
        .panel h3 { margin: 0; font-size: 1.05rem; font-weight: 600; }
        .ket { margin: .55rem 0 0; color: var(--muted); font-size: .9rem; }
        .note {
            margin: .75rem 0 0;
            padding: .7rem .85rem;
            border-radius: .5rem;
            background: #fde8e8;
            color: #9b1c1c;
            font-size: .9rem;
        }
        .note.warn { background: #e8f0fe; color: #1e40af; }
        .facts {
            display: grid;
            gap: .75rem 1.25rem;
            margin: 1rem 0 0;
            padding-top: .95rem;
            border-top: 1px solid var(--line);
        }
        @media (min-width: 640px) {
            .facts { grid-template-columns: 1fr 1fr; }
        }
        .facts-full { grid-column: 1 / -1; }
        .facts dt {
            margin: 0;
            font-size: .75rem;
            font-weight: 600;
            color: var(--muted);
        }
        .facts dd {
            margin: .15rem 0 0;
            font-size: .9375rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }
        .actions { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .95rem; }
        .progress {
            list-style: none;
            display: flex;
            margin: 1rem 0 0;
            padding: 0;
        }
        .progress-step {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-width: 0;
        }
        .progress-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 5px;
            left: calc(50% + 8px);
            right: calc(-50% + 8px);
            height: 2px;
            background: var(--line);
        }
        .progress-step.is-done:not(:last-child)::after,
        .progress-step.is-current:not(:last-child)::after {
            background: #c4e4cc;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #dadce0;
            z-index: 1;
        }
        .is-done .dot { background: var(--forest); }
        .is-current .dot {
            background: var(--forest);
            box-shadow: 0 0 0 4px rgba(19, 115, 51, .16);
        }
        .is-reject .dot { background: #d93025; }
        .progress-label {
            margin-top: .4rem;
            font-size: .68rem;
            font-weight: 500;
            color: var(--muted);
            line-height: 1.25;
        }
        .is-done .progress-label,
        .is-current .progress-label { color: #137333; }
        .is-reject .progress-label { color: #d93025; }
        a { color: var(--forest); }
    </style>
</head>
<body>
    <div class="shell">
        @include('permohonan.partials.public-nav', ['active' => 'tracking'])

        <header class="hero">
            <p class="hero-kicker">Fakultas Syariah &amp; Hukum Islam · IAIN Bone</p>
            <h1>Pelacakan pengajuan</h1>
            <p>Masukkan NIM untuk melihat data pengajuan dan progres setiap permohonan.</p>
        </header>

        @if (session('success'))
            <div class="block" style="background:#e6f4ea;color:#137333;border-top:0;padding:.9rem 1rem;border-radius:.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('permohonan.tracking') }}" class="block">
            <label class="label" for="nim">NIM</label>
            <div class="search-row">
                <input id="nim" name="nim" type="text" value="{{ $nim }}" class="field" placeholder="Contoh: 2010101001" inputmode="numeric" required>
                <button type="submit" class="btn">Lacak</button>
            </div>
            @error('nim') <p style="color:#b42318;font-size:.85rem;">{{ $message }}</p> @enderror
        </form>

        @if ($searched)
            @if (! $mahasiswa)
                <div class="block" style="text-align:center;color:var(--muted);">
                    Tidak ditemukan data pengajuan untuk NIM <strong>{{ $nim }}</strong>. Periksa kembali NIM, atau ajukan SK Pembimbing terlebih dahulu.
                </div>
            @else
                @php
                    $bisaAjukanPenguji = $mahasiswa->bisaAjukanPenguji();
                    $bisaAjukanMunaqasyah = $mahasiswa->bisaAjukanMunaqasyah();
                @endphp

                <section class="block identitas">
                    <h2>{{ $mahasiswa->nama_lengkap }}</h2>
                    <p>NIM {{ $mahasiswa->nim }} · {{ $mahasiswa->program_studi?->getLabel() }}</p>
                </section>

                <section class="group">
                    <h2>SK Pembimbing</h2>
                    <div class="stack">
                        @forelse ($permohonans as $item)
                            @include('permohonan.partials.tracking-permohonan', [
                                'item' => $item,
                                'label1' => 'Pembimbing 1',
                                'nama1' => $item->pembimbing_1,
                                'label2' => 'Pembimbing 2',
                                'nama2' => $item->pembimbing_2,
                                'unduhUrl' => ($item->status === \App\Enums\StatusPermohonan::SkTerbit && filled($item->file_sk))
                                    ? route('sk.download', $item)
                                    : null,
                                'unduhLabel' => 'Unduh SK Pembimbing',
                            ])
                        @empty
                            <article class="panel is-empty">
                                <h3>Belum diajukan</h3>
                                <p class="ket">Belum ada permohonan SK Pembimbing untuk NIM ini.</p>
                                <div class="actions">
                                    <a class="btn" href="{{ route('pembimbing.create') }}">Ajukan SK Pembimbing</a>
                                </div>
                            </article>
                        @endforelse
                    </div>
                </section>

                <section class="group">
                    <h2>SK Penguji</h2>
                    <div class="stack">
                        @forelse ($mahasiswa->permohonanPenguji as $item)
                            @include('permohonan.partials.tracking-permohonan', [
                                'item' => $item,
                                'label1' => 'Penguji 1',
                                'nama1' => $item->penguji_1,
                                'label2' => 'Penguji 2',
                                'nama2' => $item->penguji_2,
                                'unduhUrl' => ($item->status === \App\Enums\StatusPermohonan::SkTerbit && filled($item->file_sk))
                                    ? route('sk.penguji.download', $item)
                                    : null,
                                'unduhLabel' => 'Unduh SK Penguji',
                            ])
                        @empty
                            <article class="panel is-empty">
                                <h3>Belum diajukan</h3>
                                <p class="ket">
                                    @if ($bisaAjukanPenguji)
                                        SK Pembimbing telah terbit. Permohonan SK Penguji dapat diajukan.
                                    @else
                                        Dapat diajukan setelah SK Pembimbing terbit.
                                    @endif
                                </p>
                                @if ($bisaAjukanPenguji)
                                    <div class="actions">
                                        <a class="btn" href="{{ route('penguji.create') }}">Ajukan SK Penguji</a>
                                    </div>
                                @endif
                            </article>
                        @endforelse
                    </div>
                </section>

                <section class="group">
                    <h2>Undangan Munaqasyah</h2>
                    <article class="panel is-empty">
                        <div class="panel-head">
                            <h3>Belum dibuka</h3>
                            <span class="badge">Segera</span>
                        </div>
                        <p class="ket">
                            @if ($bisaAjukanMunaqasyah)
                                SK Penguji telah terbit. Pengajuan undangan munaqasyah akan dibuka pada tahap ini.
                            @else
                                Dapat diajukan setelah SK Penguji terbit.
                            @endif
                        </p>
                    </article>
                </section>
            @endif
        @endif
    </div>
</body>
</html>
