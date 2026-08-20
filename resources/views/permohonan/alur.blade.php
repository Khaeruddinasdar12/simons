<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Alur Aplikasi</title>
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
        .shell { width: min(1080px, calc(100% - 1.75rem)); margin: 0 auto; padding-bottom: 3.5rem; }
        @include('permohonan.partials.public-nav-css')
        .hero { padding: 1.35rem 0 1.5rem; }
        .hero-kicker {
            margin: 0 0 .4rem;
            font-size: .8125rem;
            font-weight: 500;
            color: var(--muted);
        }
        .hero h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -.02em;
            line-height: 1.3;
        }
        .hero p {
            margin: .5rem 0 0;
            max-width: 36rem;
            font-size: 1rem;
            line-height: 1.55;
            color: var(--muted);
        }
        .start {
            display: block;
            text-decoration: none;
            background: var(--forest);
            color: #fff;
            border-radius: .75rem;
            padding: 1rem 1.1rem;
            margin-bottom: 1.5rem;
        }
        .start strong { display: block; font-size: 1.05rem; font-weight: 600; }
        .start span { display: block; margin-top: .25rem; font-size: .9rem; opacity: .92; }
        .flow { display: grid; gap: 0; }
        .flow-step {
            position: relative;
            padding: 0 0 1.65rem 3rem;
        }
        .flow-step:last-child { padding-bottom: 0; }
        .flow-step::before {
            content: '';
            position: absolute;
            left: .7rem;
            top: 1.85rem;
            bottom: .15rem;
            width: 2px;
            background: var(--line);
        }
        .flow-step:last-child::before { display: none; }
        .flow-badge {
            position: absolute;
            left: 0;
            top: 0;
            width: 1.6rem;
            height: 1.6rem;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 700;
            background: var(--soft);
            color: var(--muted);
            border: 2px solid var(--line);
        }
        .flow-step.is-start .flow-badge {
            background: var(--forest);
            color: #fff;
            border-color: var(--forest);
        }
        .flow-num { display: none; }
        .flow-step h2 {
            margin: .05rem 0 .35rem;
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.35;
        }
        .flow-step .lead,
        .flow-step .when {
            margin: 0 0 .7rem;
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.5;
        }
        .flow-step ol {
            display: none;
            margin: 0 0 1.1rem;
            padding-left: 1.1rem;
            font-size: .875rem;
            line-height: 1.55;
        }
        .flow-step li { margin: .28rem 0; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
            border-radius: .6rem;
            padding: .9rem 1rem;
            min-height: 3rem;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            background: var(--forest);
            width: 100%;
        }
        .btn.ghost {
            background: #fff;
            color: var(--forest);
            border: 1px solid var(--line);
        }
        .btn.disabled {
            background: var(--soft);
            color: #80868b;
            pointer-events: none;
        }
        .track-row { padding-top: 1.75rem; }
        .track-row h3 { margin: 0 0 .35rem; font-size: 1.05rem; font-weight: 600; }
        .track-row p { margin: 0 0 .85rem; color: var(--muted); font-size: .95rem; }
        @media (min-width: 900px) {
            body { font-size: 15px; }
            .shell { width: min(1080px, calc(100% - 2rem)); }
            .hero { padding: 1.75rem 0 2rem; }
            .hero h1 { font-size: 1.75rem; font-weight: 500; }
            .hero p { font-size: .9375rem; }
            .start { display: none; }
            .flow {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 2.75rem;
            }
            .flow-step {
                padding: 0;
            }
            .flow-step::before,
            .flow-badge { display: none; }
            .flow-num {
                display: block;
                margin: 0;
                font-size: .75rem;
                font-weight: 500;
                color: var(--muted);
            }
            .flow-step h2 {
                margin: .3rem 0 .4rem;
                font-size: 1rem;
                font-weight: 500;
            }
            .flow-step .lead,
            .flow-step .when { font-size: .875rem; }
            .flow-step ol { display: block; }
            .btn {
                width: auto;
                min-width: 0;
                min-height: 0;
                padding: .55rem .95rem;
                font-size: .875rem;
                font-weight: 500;
                border-radius: .375rem;
            }
            .track-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding-top: 2rem;
            }
            .track-row p { margin: .25rem 0 0; }
            .track-row .btn { width: auto; }
        }
    </style>
</head>
<body>
    <div class="shell">
        @include('permohonan.partials.public-nav', ['active' => 'alur'])

        <header class="hero">
            <p class="hero-kicker">Fakultas Syariah &amp; Hukum Islam · IAIN Bone</p>
            <h1>Alur pengajuan skripsi</h1>
            <p>Pengajuan menempuh tiga tahap: penetapan SK Pembimbing, penetapan SK Penguji, dan undangan munaqasyah. Setiap tahap dilanjutkan setelah SK tahap sebelumnya terbit.</p>
        </header>

        <a class="start" href="{{ route('pembimbing.create') }}">
            <strong>Langkah awal</strong>
            <span>Permohonan SK Pembimbing — lengkapi data dan unggah berkas usul Ketua Program Studi.</span>
        </a>

        <section class="flow" aria-label="Alur aplikasi">
            <article class="flow-step is-start">
                <span class="flow-badge" aria-hidden="true">1</span>
                <p class="flow-num">Langkah 1</p>
                <h2>SK Pembimbing</h2>
                <p class="lead">Unggah berkas usul Ketua Program Studi, lalu isikan data sesuai yang tercantum di berkas tersebut.</p>
                <ol>
                    <li>Lengkapi data mahasiswa dan judul skripsi.</li>
                    <li>Cantumkan nama dua pembimbing sebagaimana tertulis dalam berkas usul.</li>
                    <li>Unggah berkas usul dari Ketua Program Studi.</li>
                    <li>Akademik, Kabag, Wadek 1, dan Dekan memverifikasi hingga SK Pembimbing terbit.</li>
                </ol>
                <a class="btn" href="{{ route('pembimbing.create') }}">Ajukan SK Pembimbing</a>
            </article>

            <article class="flow-step">
                <span class="flow-badge" aria-hidden="true">2</span>
                <p class="flow-num">Langkah 2</p>
                <h2>SK Penguji</h2>
                <p class="when">Dapat diajukan setelah SK Pembimbing terbit.</p>
                <p class="lead">Masukkan NIM dan unggah usulan Ketua Program Studi, kemudian isikan nama penguji sesuai berkas tersebut.</p>
                <ol>
                    <li>Masukkan NIM untuk memuat data dari SK Pembimbing.</li>
                    <li>Cantumkan nama dua penguji sebagaimana tertulis dalam berkas usul.</li>
                    <li>Unggah berkas usul dari Ketua Program Studi.</li>
                    <li>Akademik hingga Dekan memverifikasi hingga SK Penguji terbit.</li>
                </ol>
                <a class="btn ghost" href="{{ route('penguji.create') }}">Ajukan SK Penguji</a>
            </article>

            <article class="flow-step">
                <span class="flow-badge" aria-hidden="true">3</span>
                <p class="flow-num">Langkah 3</p>
                <h2>Undangan Munaqasyah</h2>
                <p class="when">Dapat diajukan setelah SK Penguji terbit.</p>
                <p class="lead">Undangan ujian munaqasyah akan dibuka pada tahap ini.</p>
                <ol>
                    <li>Pengajuan undangan dibuka setelah SK Penguji terbit.</li>
                    <li>Data mahasiswa dan judul terkini dipakai otomatis.</li>
                    <li>Status dapat dipantau pada halaman pelacakan.</li>
                </ol>
                <span class="btn disabled">Segera dibuka</span>
            </article>
        </section>

        <aside class="track-row">
            <div>
                <h3>Memantau permohonan</h3>
                <p>Gunakan NIM untuk meninjau status pengajuan. Akun tidak diperlukan.</p>
            </div>
            <a class="btn ghost" href="{{ route('permohonan.tracking') }}">Lacak pengajuan</a>
        </aside>
    </div>
</body>
</html>
