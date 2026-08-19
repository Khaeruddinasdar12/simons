<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Usul Pembimbing Skripsi</title>
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
                <a href="{{ route('home') }}" class="primary">SK Pembimbing</a>
                <a href="{{ route('penguji.create') }}">SK Penguji</a>
                <a href="{{ route('permohonan.tracking') }}">Tracking</a>
                <a href="{{ url('/admin') }}">Login Admin</a>
            </div>
        </nav>

        <header class="hero">
            <div class="hero-panel">
                <div class="hero-kicker">Fakultas Syariah &amp; Hukum Islam · IAIN Bone</div>
                <h1>{{ config('app.name') }}</h1>
                <p class="hero-expand">{{ config('app.full_name') }}</p>
                <p>Ajukan usul pembimbing skripsi secara daring, pantau progres, dan ikuti alur verifikasi akademik hingga penerbitan SK.</p>
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
                    <span>Isi data mahasiswa &amp; skripsi</span>
                </div>
                <div class="step">
                    <strong>Langkah 2</strong>
                    <span>Unggah usul dari Prodi, lalu kirim</span>
                </div>
            </div>

            <form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="section-title">Data Mahasiswa</h2>
                <p class="hint" style="margin:-0.35rem 0 1rem;">Semua isian bertanda <span style="color:#b42318;">*</span> wajib diisi.</p>
                <div class="grid cols-2">
                    <div>
                        <label class="label" for="nim">NIM <span style="color:#b42318;">*</span></label>
                        <input id="nim" name="nim" type="text" value="{{ old('nim') }}" class="field" required>
                        @error('nim') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="nama_lengkap">Nama Lengkap <span style="color:#b42318;">*</span></label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" class="field" required>
                        @error('nama_lengkap') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="tempat_lahir">Tempat Lahir <span style="color:#b42318;">*</span></label>
                        <input id="tempat_lahir" name="tempat_lahir" type="text" value="{{ old('tempat_lahir') }}" class="field" required>
                        @error('tempat_lahir') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="tanggal_lahir">Tanggal Lahir <span style="color:#b42318;">*</span></label>
                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir') }}" class="field" required>
                        @error('tanggal_lahir') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label class="label" for="alamat_lengkap">Alamat Lengkap <span style="color:#b42318;">*</span></label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" class="field" required>{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="no_hp">No. HP <span style="color:#b42318;">*</span></label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" class="field" placeholder="08xxxxxxxxxx" required>
                        @error('no_hp') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="email">Email <span style="color:#b42318;">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="field" placeholder="nama@email.com" required>
                        <p style="margin:.4rem 0 0;font-size:.8rem;color:var(--muted);line-height:1.45;">
                            Mohon menginput email yang aktif/benar. Notifikasi penolakan maupun penerbitan SK akan dikirim ke alamat email ini.
                        </p>
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="program_studi">Program Studi <span style="color:#b42318;">*</span></label>
                        <select id="program_studi" name="program_studi" class="field" required>
                            <option value="">— Pilih Program Studi —</option>
                            @foreach ($programStudi as $value => $label)
                                <option value="{{ $value }}" @selected(old('program_studi') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('program_studi') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="semester">Semester <span style="color:#b42318;">*</span></label>
                        <input id="semester" name="semester" type="number" min="1" max="14" value="{{ old('semester') }}" class="field" required>
                        @error('semester') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="divider"></div>

                <h2 class="section-title">Data Skripsi &amp; Pembimbing</h2>
                @if (empty($dosens))
                    <div class="alert alert-err" style="margin-bottom:1rem;">
                        Daftar dosen belum tersedia. Hubungi akademik untuk menambahkan data dosen pembimbing.
                    </div>
                @endif
                <div class="grid cols-2">
                    <div style="grid-column: 1 / -1;">
                        <label class="label" for="judul_skripsi">Judul Skripsi <span style="color:#b42318;">*</span></label>
                        <textarea id="judul_skripsi" name="judul_skripsi" rows="2" class="field" required>{{ old('judul_skripsi') }}</textarea>
                        @error('judul_skripsi') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="pembimbing_1">Pembimbing 1 <span style="color:#b42318;">*</span></label>
                        <select id="pembimbing_1" name="pembimbing_1" class="field js-dosen-select" required
                                data-placeholder="— Cari / pilih Pembimbing 1 —">
                            <option value="">— Cari / pilih Pembimbing 1 —</option>
                            @foreach ($dosens as $nama)
                                <option value="{{ $nama }}" @selected(old('pembimbing_1') === $nama)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        @error('pembimbing_1') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="pembimbing_2">Pembimbing 2 <span style="color:#b42318;">*</span></label>
                        <select id="pembimbing_2" name="pembimbing_2" class="field js-dosen-select" required
                                data-placeholder="— Cari / pilih Pembimbing 2 —">
                            <option value="">— Cari / pilih Pembimbing 2 —</option>
                            @foreach ($dosens as $nama)
                                <option value="{{ $nama }}" @selected(old('pembimbing_2') === $nama)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <p class="hint">Pembimbing 1 dan Pembimbing 2 harus berbeda. Daftar dosen dikelola akademik.</p>
                        @error('pembimbing_2') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label class="label" for="file_usul_pembimbing">Upload Usul Pembimbing dari Prodi <span style="color:#b42318;">*</span></label>
                        <input id="file_usul_pembimbing" name="file_usul_pembimbing" type="file"
                               accept=".pdf,.jpg,.jpeg,.png" class="field" required>
                        <p style="margin:.4rem 0 0;font-size:.8rem;color:var(--muted);">Wajib. PDF / JPG / PNG, maksimal 5 MB.</p>
                        @error('file_usul_pembimbing') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="divider"></div>

                <div class="actions">
                    <p style="margin:0;font-size:.9rem;color:var(--muted);">
                        Setelah dikirim, pantau status di halaman tracking dengan NIM Anda.
                    </p>
                    <button type="submit" class="btn">Kirim Permohonan</button>
                </div>
            </form>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        (function () {
            const selects = {};
            const form = document.querySelector('form[action="{{ route('permohonan.store') }}"]');

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
                const otherId = changedId === 'pembimbing_1' ? 'pembimbing_2' : 'pembimbing_1';
                const changed = selects[changedId];
                const other = selects[otherId];
                if (!changed || !other) return;

                const value = changed.getValue();
                const otherValue = other.getValue();

                if (value && otherValue && value === otherValue) {
                    other.clear(true);
                }
            }

            ['pembimbing_1', 'pembimbing_2'].forEach(function (id) {
                if (selects[id]) {
                    selects[id].on('change', function () {
                        syncDifferent(id);
                    });
                }
            });

            if (form) {
                form.addEventListener('submit', function (event) {
                    const missing = [];
                    ['pembimbing_1', 'pembimbing_2'].forEach(function (id) {
                        const value = selects[id] ? selects[id].getValue() : '';
                        if (!value) {
                            missing.push(id === 'pembimbing_1' ? 'Pembimbing 1' : 'Pembimbing 2');
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
