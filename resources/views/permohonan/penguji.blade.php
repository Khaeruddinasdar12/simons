<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Usul Penguji Skripsi</title>
    <link rel="icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logoiainbone.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700|manrope:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.default.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #0f241c;
            --forest: #14532d;
            --leaf: #166534;
            --glow: #22c55e;
            --mist: #ecfdf3;
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
                radial-gradient(circle at 88% 0%, rgba(20, 83, 45, 0.12), transparent 28%),
                linear-gradient(165deg, #f4fbf6 0%, var(--paper) 45%, #eef7f1 100%);
            min-height: 100vh;
        }
        .brand-font { font-family: 'Fraunces', Georgia, serif; }
        .shell { width: min(1120px, calc(100% - 2rem)); margin: 0 auto; }
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 0;
            animation: rise .7s ease both;
        }
        .logo-pair { display: flex; align-items: center; gap: .85rem; }
        .logo-pair img { height: 3.4rem; width: auto; object-fit: contain; }
        .nav-links { display: flex; gap: .75rem; flex-wrap: wrap; }
        .nav-links a {
            text-decoration: none;
            color: var(--forest);
            font-weight: 600;
            font-size: .9rem;
            padding: .55rem .9rem;
            border-radius: .7rem;
            transition: background .2s ease, transform .2s ease;
        }
        .nav-links a:hover { background: rgba(22, 101, 52, .08); transform: translateY(-1px); }
        .nav-links a.primary {
            background: var(--forest);
            color: #fff;
        }
        .hero {
            display: grid;
            gap: 1.5rem;
            padding: 1.5rem 0 2rem;
            animation: rise .8s .08s ease both;
        }
        .hero-panel {
            position: relative;
            overflow: hidden;
            border-radius: 1.75rem;
            padding: clamp(1.5rem, 4vw, 2.75rem);
            color: #fff;
            background:
                linear-gradient(135deg, rgba(15, 61, 36, .92), rgba(22, 101, 52, .88)),
                url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M0 0h40v40H0V0zm40 40h40v40H40V40z'/%3E%3C/g%3E%3C/svg%3E");
            box-shadow: 0 24px 60px rgba(15, 61, 36, .22);
        }
        .hero-panel::after {
            content: '';
            position: absolute;
            right: -4rem;
            bottom: -5rem;
            width: 18rem;
            height: 18rem;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34,197,94,.35), transparent 70%);
            animation: pulse 5s ease-in-out infinite;
        }
        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            opacity: .9;
            margin-bottom: .85rem;
        }
        .hero h1 {
            position: relative;
            z-index: 1;
            margin: 0;
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(2.4rem, 6vw, 3.8rem);
            line-height: 1.08;
            letter-spacing: .02em;
        }
        .hero-expand {
            position: relative;
            z-index: 1;
            margin: .55rem 0 0;
            max-width: 36rem;
            font-size: clamp(.95rem, 2.2vw, 1.1rem);
            font-weight: 600;
            line-height: 1.45;
            color: rgba(255,255,255,.95);
        }
        .hero p {
            position: relative;
            z-index: 1;
            margin: .85rem 0 0;
            max-width: 38rem;
            font-size: 1.05rem;
            line-height: 1.6;
            color: rgba(255,255,255,.88);
        }
        .nav-brand {
            display: flex;
            flex-direction: column;
            gap: .1rem;
            min-width: 0;
        }
        .nav-brand strong {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.15rem;
            color: var(--forest);
            letter-spacing: .03em;
        }
        .nav-brand span {
            font-size: .72rem;
            color: var(--muted);
            line-height: 1.3;
        }
        .form-shell {
            margin-bottom: 3rem;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: clamp(1.25rem, 3vw, 2rem);
            box-shadow: 0 18px 40px rgba(15, 36, 28, .06);
            animation: rise .85s .16s ease both;
        }
        .steps {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
            margin-bottom: 1.5rem;
        }
        .step {
            border: 1px solid var(--line);
            border-radius: 1rem;
            padding: .85rem 1rem;
            background: #fff;
        }
        .step strong {
            display: block;
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--leaf);
        }
        .step span { font-size: .92rem; color: var(--muted); }
        .section-title {
            margin: 0 0 1rem;
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.25rem;
            color: var(--forest);
        }
        .grid { display: grid; gap: 1rem; }
        @media (min-width: 720px) {
            .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .logo-pair img { height: 4.2rem; }
        }
        .field {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: .8rem;
            padding: .8rem .95rem;
            outline: none;
            font: inherit;
            transition: border-color .15s, box-shadow .15s, transform .15s;
        }
        .field:focus {
            border-color: var(--leaf);
            box-shadow: 0 0 0 4px rgba(22, 101, 52, .12);
            transform: translateY(-1px);
        }
        .label {
            display: block;
            font-size: .86rem;
            font-weight: 700;
            margin-bottom: .35rem;
            color: var(--forest);
        }
        .error { color: #b42318; font-size: .8rem; margin-top: .25rem; }
        .divider {
            height: 1px;
            background: var(--line);
            margin: 1.75rem 0;
        }
        .actions {
            display: flex;
            flex-direction: column-reverse;
            gap: 1rem;
            align-items: stretch;
        }
        @media (min-width: 640px) {
            .actions {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: .9rem;
            padding: .95rem 1.4rem;
            font: inherit;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, var(--forest), #15803d);
            box-shadow: 0 12px 24px rgba(20, 83, 45, .22);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 28px rgba(20, 83, 45, .28); }
        .alert {
            margin-bottom: 1rem;
            border-radius: .9rem;
            padding: .9rem 1rem;
            font-size: .92rem;
            font-weight: 600;
        }
        .alert-ok { background: #ecfdf3; color: var(--forest); border: 1px solid rgba(22,101,52,.25); }
        .alert-err { background: #fef2f2; color: #9b1c1c; border: 1px solid #fecaca; }
        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: .8; }
            50% { transform: scale(1.08); opacity: 1; }
        }
        .ts-wrapper.single .ts-control {
            border: 1px solid var(--line);
            border-radius: .8rem;
            padding: .72rem .95rem;
            background: #fff;
            font: inherit;
            box-shadow: none;
            min-height: auto;
        }
        .ts-wrapper.single.focus .ts-control {
            border-color: var(--leaf);
            box-shadow: 0 0 0 4px rgba(22, 101, 52, .12);
        }
        .ts-dropdown {
            border: 1px solid var(--line);
            border-radius: .8rem;
            box-shadow: 0 12px 28px rgba(15, 36, 28, .12);
            overflow: hidden;
        }
        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background: var(--mist);
            color: var(--forest);
        }
        .ts-wrapper.single .ts-control input {
            font: inherit;
        }
        .hint {
            margin: .4rem 0 0;
            font-size: .8rem;
            color: var(--muted);
            line-height: 1.45;
        }
        .nim-row {
            display: grid;
            gap: .75rem;
            align-items: end;
        }
        @media (min-width: 640px) {
            .nim-row { grid-template-columns: 1fr auto; }
        }
        .btn-check {
            background: #fff;
            color: var(--forest);
            border: 1px solid var(--line);
            box-shadow: none;
        }
        .btn-check:hover { background: var(--mist); box-shadow: none; }
        .btn-check:disabled { opacity: .65; cursor: wait; }
        .readonly-card {
            border: 1px solid var(--line);
            background: var(--mist);
            border-radius: 1rem;
            padding: 1rem 1.1rem;
        }
        .readonly-grid {
            display: grid;
            gap: .85rem 1.25rem;
        }
        @media (min-width: 720px) {
            .readonly-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .readonly-item dt {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--leaf);
            margin: 0 0 .2rem;
        }
        .readonly-item dd {
            margin: 0;
            font-size: .95rem;
            line-height: 1.45;
            color: var(--ink);
        }
        .readonly-item.span-2 { grid-column: 1 / -1; }
        #form-lanjutan[hidden] { display: none !important; }
    </style>
</head>
<body>
    <div class="shell">
        <nav class="nav">
            <div class="logo-pair">
                <img src="{{ asset('logokemenag.png') }}" alt="Logo Kementerian Agama">
                <img src="{{ asset('logoiainbone.png') }}" alt="Logo IAIN Bone">
                <div class="nav-brand">
                    <strong>{{ config('app.name') }}</strong>
                    <span>{{ config('app.full_name') }}</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="{{ route('home') }}">SK Pembimbing</a>
                <a href="{{ route('penguji.create') }}" class="primary">SK Penguji</a>
                <a href="{{ route('permohonan.tracking') }}">Tracking</a>
                <a href="{{ url('/admin') }}">Login Admin</a>
            </div>
        </nav>

        <header class="hero">
            <div class="hero-panel">
                <div class="hero-kicker">Fakultas Syariah &amp; Hukum Islam · IAIN Bone</div>
                <h1>{{ config('app.name') }}</h1>
                <p class="hero-expand">{{ config('app.full_name') }}</p>
                <p>Masukkan NIM terlebih dahulu. Form penguji hanya terbuka jika SK Pembimbing untuk NIM tersebut sudah terbit.</p>
            </div>
        </header>

        <main class="form-shell">
            @if (session('success'))
                <div class="alert alert-ok">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-err">Mohon perbaiki isian yang masih kurang tepat.</div>
            @endif

            <div class="steps">
                <div class="step">
                    <strong>Langkah 1</strong>
                    <span>Masukkan NIM — sistem memeriksa SK Pembimbing</span>
                </div>
                <div class="step">
                    <strong>Langkah 2</strong>
                    <span>Jika SK sudah terbit: pilih penguji &amp; unggah usulan Kaprodi</span>
                </div>
            </div>

            <form action="{{ route('penguji.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="section-title">Verifikasi NIM</h2>
                <div class="nim-row">
                    <div>
                        <label class="label" for="nim">NIM <span style="color:#b42318;">*</span></label>
                        <input id="nim" name="nim" type="text" value="{{ old('nim') }}" class="field" required autocomplete="off">
                        <p class="hint">Hanya NIM. Data mahasiswa dan skripsi akan ditampilkan setelah SK Pembimbing terverifikasi.</p>
                        @error('nim') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" class="btn btn-check" id="btn-cek-nim">Periksa NIM</button>
                </div>
                <div id="sk-pembimbing-info" class="alert" style="display:none;margin-top:1rem;"></div>

                <div id="form-lanjutan" hidden>
                    <div class="divider"></div>

                    <h2 class="section-title">Data dari SK Pembimbing</h2>
                    <p class="hint" style="margin:-0.35rem 0 1rem;">Data ini diambil otomatis dan tidak dapat diubah di form ini.</p>
                    <div class="readonly-card">
                        <dl class="readonly-grid">
                            <div class="readonly-item">
                                <dt>NIM</dt>
                                <dd id="tampil-nim">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Nama Lengkap</dt>
                                <dd id="tampil-nama">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Tempat, Tanggal Lahir</dt>
                                <dd id="tampil-ttl">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Program Studi</dt>
                                <dd id="tampil-prodi">—</dd>
                            </div>
                            <div class="readonly-item span-2">
                                <dt>Alamat</dt>
                                <dd id="tampil-alamat">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>No. HP</dt>
                                <dd id="tampil-hp">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Email</dt>
                                <dd id="tampil-email">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Semester</dt>
                                <dd id="tampil-semester">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Nomor SK Pembimbing</dt>
                                <dd id="tampil-nomor-sk">—</dd>
                            </div>
                            <div class="readonly-item span-2">
                                <dt>Judul Skripsi</dt>
                                <dd id="tampil-judul">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Pembimbing 1</dt>
                                <dd id="tampil-pembimbing-1">—</dd>
                            </div>
                            <div class="readonly-item">
                                <dt>Pembimbing 2</dt>
                                <dd id="tampil-pembimbing-2">—</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="divider"></div>

                    <h2 class="section-title">Usulan Penguji</h2>
                    @if (empty($dosens))
                        <div class="alert alert-err" style="margin-bottom:1rem;">
                            Daftar dosen belum tersedia. Hubungi akademik untuk menambahkan data dosen penguji.
                        </div>
                    @endif
                    <div class="grid cols-2">
                        <div>
                            <label class="label" for="penguji_1">Penguji 1 <span style="color:#b42318;">*</span></label>
                            <select id="penguji_1" name="penguji_1" class="field js-dosen-select" data-placeholder="— Cari / pilih Penguji 1 —">
                                <option value="">— Cari / pilih Penguji 1 —</option>
                                @foreach ($dosens as $nama)
                                    <option value="{{ $nama }}" @selected(old('penguji_1') === $nama)>{{ $nama }}</option>
                                @endforeach
                            </select>
                            @error('penguji_1') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="penguji_2">Penguji 2 <span style="color:#b42318;">*</span></label>
                            <select id="penguji_2" name="penguji_2" class="field js-dosen-select" data-placeholder="— Cari / pilih Penguji 2 —">
                                <option value="">— Cari / pilih Penguji 2 —</option>
                                @foreach ($dosens as $nama)
                                    <option value="{{ $nama }}" @selected(old('penguji_2') === $nama)>{{ $nama }}</option>
                                @endforeach
                            </select>
                            <p class="hint">Penguji 1 dan Penguji 2 harus berbeda, dan tidak boleh sama dengan pembimbing pada SK Pembimbing yang sudah terbit.</p>
                            @error('penguji_2') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label class="label" for="file_usul_penguji">Upload Usulan Penguji dari Kaprodi <span style="color:#b42318;">*</span></label>
                            <input id="file_usul_penguji" name="file_usul_penguji" type="file"
                                   accept=".pdf,.jpg,.jpeg,.png" class="field">
                            <p style="margin:.4rem 0 0;font-size:.8rem;color:var(--muted);">Wajib. PDF / JPG / PNG, maksimal 5 MB.</p>
                            @error('file_usul_penguji') <p class="error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="actions">
                        <p style="margin:0;font-size:.9rem;color:var(--muted);">
                            Setelah dikirim, pantau status di halaman tracking dengan NIM Anda.
                        </p>
                        <button type="submit" class="btn" id="btn-kirim">Kirim Permohonan</button>
                    </div>
                </div>
            </form>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        (function () {
            const selects = {};
            const form = document.querySelector('form[action="{{ route('penguji.store') }}"]');
            const lookupUrl = @json(route('penguji.lookup'));
            const nimInput = document.getElementById('nim');
            const btnCek = document.getElementById('btn-cek-nim');
            const infoBox = document.getElementById('sk-pembimbing-info');
            const formLanjutan = document.getElementById('form-lanjutan');
            const fileInput = document.getElementById('file_usul_penguji');
            let eligible = false;
            let tomReady = false;

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '—';
            }

            function showInfo(ok, message) {
                if (!infoBox) return;
                infoBox.className = ok ? 'alert alert-ok' : 'alert alert-err';
                infoBox.style.display = 'block';
                infoBox.textContent = message;
            }

            function hideForm() {
                eligible = false;
                if (formLanjutan) formLanjutan.hidden = true;
                if (fileInput) fileInput.required = false;
                ['penguji_1', 'penguji_2'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) el.required = false;
                });
            }

            function fillReadonly(data) {
                const m = data.mahasiswa || {};
                const ttl = [m.tempat_lahir, m.tanggal_lahir].filter(Boolean).join(', ');
                setText('tampil-nim', m.nim);
                setText('tampil-nama', m.nama_lengkap);
                setText('tampil-ttl', ttl);
                setText('tampil-prodi', m.program_studi);
                setText('tampil-alamat', m.alamat_lengkap);
                setText('tampil-hp', m.no_hp);
                setText('tampil-email', m.email);
                setText('tampil-semester', data.semester ? String(data.semester) : '—');
                setText('tampil-nomor-sk', data.nomor_sk_pembimbing);
                setText('tampil-judul', data.judul_skripsi);
                setText('tampil-pembimbing-1', data.pembimbing_1);
                setText('tampil-pembimbing-2', data.pembimbing_2);
            }

            function initDosenSelects() {
                if (tomReady) return;
                document.querySelectorAll('.js-dosen-select').forEach(function (el) {
                    selects[el.id] = new TomSelect(el, {
                        create: false,
                        allowEmptyOption: true,
                        maxOptions: null,
                        sortField: { field: 'text', direction: 'asc' },
                        placeholder: el.dataset.placeholder || 'Cari dosen…',
                        plugins: ['clear_button'],
                    });
                });

                function syncDifferent(changedId) {
                    const otherId = changedId === 'penguji_1' ? 'penguji_2' : 'penguji_1';
                    const changed = selects[changedId];
                    const other = selects[otherId];
                    if (!changed || !other) return;
                    const value = changed.getValue();
                    const otherValue = other.getValue();
                    if (value && otherValue && value === otherValue) {
                        other.clear(true);
                    }
                }

                ['penguji_1', 'penguji_2'].forEach(function (id) {
                    if (selects[id]) {
                        selects[id].on('change', function () {
                            syncDifferent(id);
                        });
                    }
                });

                tomReady = true;
            }

            function showForm(data) {
                eligible = true;
                fillReadonly(data);
                if (formLanjutan) formLanjutan.hidden = false;
                if (fileInput) fileInput.required = true;
                ['penguji_1', 'penguji_2'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) el.required = true;
                });
                initDosenSelects();
                showInfo(
                    true,
                    'SK Pembimbing terbit (' + (data.nomor_sk_pembimbing || '-') +
                    '). Lanjutkan dengan memilih penguji dan mengunggah usulan Kaprodi.'
                );
            }

            function lookupNim() {
                const nim = (nimInput.value || '').trim();
                if (!nim) {
                    hideForm();
                    if (infoBox) infoBox.style.display = 'none';
                    return;
                }

                if (btnCek) {
                    btnCek.disabled = true;
                    btnCek.textContent = 'Memeriksa…';
                }

                fetch(lookupUrl + '?nim=' + encodeURIComponent(nim), {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!data.eligible) {
                            hideForm();
                            showInfo(false, data.message || 'NIM belum dapat mengajukan SK Penguji.');
                            return;
                        }
                        showForm(data);
                    })
                    .catch(function () {
                        hideForm();
                        showInfo(false, 'Gagal memeriksa NIM. Coba lagi.');
                    })
                    .finally(function () {
                        if (btnCek) {
                            btnCek.disabled = false;
                            btnCek.textContent = 'Periksa NIM';
                        }
                    });
            }

            if (btnCek) {
                btnCek.addEventListener('click', lookupNim);
            }

            if (nimInput) {
                nimInput.addEventListener('input', function () {
                    hideForm();
                    if (infoBox) infoBox.style.display = 'none';
                });
                nimInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        lookupNim();
                    }
                });
                if (nimInput.value) {
                    lookupNim();
                }
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!eligible) {
                        event.preventDefault();
                        lookupNim();
                        return;
                    }

                    const missing = [];
                    ['penguji_1', 'penguji_2'].forEach(function (id) {
                        const value = selects[id] ? selects[id].getValue() : '';
                        if (!value) {
                            missing.push(id === 'penguji_1' ? 'Penguji 1' : 'Penguji 2');
                        }
                    });

                    if (missing.length) {
                        event.preventDefault();
                        alert(missing.join(' dan ') + ' wajib dipilih.');
                    }
                });
            }
        })();
    </script>
</body>
</html>
