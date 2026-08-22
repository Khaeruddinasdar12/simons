<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Permohonan SK Pembimbing</title>
    <link rel="icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('logoiainbone.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logoiainbone.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.default.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @include('permohonan.partials.public-form-css')
    </style>
</head>
<body>
    <div class="shell">
        @include('permohonan.partials.public-nav', ['active' => 'pembimbing'])

        <header class="hero">
            <p class="hero-kicker"><span class="label-mob">Tahap 1 dari 3 · </span>Fakultas Syariah &amp; Hukum Islam</p>
            <h1>Permohonan SK Pembimbing</h1>
            <p>Lengkapi data mahasiswa sesuai berkas usul Ketua Program Studi, lalu unggah berkas tersebut. Progres dapat dipantau melalui NIM.</p>
        </header>

        <main class="form-shell">
            @if (session('success'))
                <div class="alert alert-ok">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-err">Mohon lengkapi atau perbaiki isian yang belum sesuai.</div>
            @endif

            <p class="steps">Siapkan NIM, data diri, judul skripsi, serta berkas usul Ketua Program Studi yang telah mencantumkan nama dua pembimbing (PDF atau JPG, paling banyak 5 MB).</p>

            <form action="{{ route('permohonan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="section-title">Data Mahasiswa</h2>
                <p class="hint" style="margin:-0.35rem 0 1rem;">Semua isian bertanda <span style="color:#b42318;">*</span> wajib diisi.</p>
                <div class="grid cols-2">
                    <div>
                        <label class="label" for="nim">NIM <span style="color:#b42318;">*</span></label>
                        <input id="nim" name="nim" type="text" value="{{ old('nim') }}" class="field" required inputmode="numeric" autocomplete="username">
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

                <h2 class="section-title">Data Skripsi sesuai Berkas Usul</h2>
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
                                data-placeholder="— Cari nama sesuai berkas usul —">
                            <option value="">— Cari nama sesuai berkas usul —</option>
                            @foreach ($dosens as $id => $nama)
                                <option value="{{ $id }}" @selected((string) old('pembimbing_1') === (string) $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        @error('pembimbing_1') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="label" for="pembimbing_2">Pembimbing 2 <span style="color:#b42318;">*</span></label>
                        <select id="pembimbing_2" name="pembimbing_2" class="field js-dosen-select" required
                                data-placeholder="— Cari nama sesuai berkas usul —">
                            <option value="">— Cari nama sesuai berkas usul —</option>
                            @foreach ($dosens as $id => $nama)
                                <option value="{{ $id }}" @selected((string) old('pembimbing_2') === (string) $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <p class="hint">Isikan nama pembimbing sebagaimana tercantum dalam berkas usul Ketua Program Studi. Pembimbing 1 dan Pembimbing 2 harus berbeda.</p>
                        @error('pembimbing_2') <p class="error">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label class="label" for="file_usul_pembimbing">Unggah Berkas Usul dari Ketua Program Studi <span style="color:#b42318;">*</span></label>
                        <input id="file_usul_pembimbing" name="file_usul_pembimbing" type="file"
                               accept=".pdf,.jpg,.jpeg,.png" class="field" required>
                        <p style="margin:.4rem 0 0;font-size:.8rem;color:var(--muted);">Wajib. PDF / JPG / PNG, maksimal 5 MB.</p>
                        @error('file_usul_pembimbing') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="divider"></div>

                <div class="actions">
                    <p style="margin:0;font-size:.9rem;color:var(--muted);">
                        Setelah terkirim, pantau status melalui menu Pelacakan dengan NIM.
                    </p>
                    <button type="submit" class="btn">Kirim permohonan</button>
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
