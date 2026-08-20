<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Permohonan SK Penguji</title>
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
        @include('permohonan.partials.public-nav', ['active' => 'penguji'])

        <header class="hero">
            <p class="hero-kicker"><span class="label-mob">Tahap 2 dari 3 · </span>Fakultas Syariah &amp; Hukum Islam</p>
            <h1>Permohonan SK Penguji</h1>
            <p>Masukkan NIM. Formulir terbuka apabila SK Pembimbing telah terbit. Nama penguji diisikan sesuai berkas usul Ketua Program Studi.</p>
        </header>

        <main class="form-shell">
            @if (session('success'))
                <div class="alert alert-ok">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-err">Mohon lengkapi atau perbaiki isian yang belum sesuai.</div>
            @endif

            <p class="steps">Apabila SK Pembimbing belum terbit, ajukan terlebih dahulu pada menu SK Pembimbing. Jika telah terbit, masukkan NIM, unggah berkas usul Ketua Program Studi, dan cantumkan nama penguji sebagaimana tertulis di berkas tersebut.</p>

            <form action="{{ route('penguji.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="section-title">Verifikasi NIM</h2>
                <div class="nim-block">
                    <label class="label" for="nim">NIM <span style="color:#b42318;">*</span></label>
                    <div class="nim-row">
                        <input id="nim" name="nim" type="text" value="{{ old('nim') }}" class="field" required autocomplete="off" placeholder="Contoh: 2010101001" inputmode="numeric">
                        <button type="button" class="btn btn-check" id="btn-cek-nim">Periksa NIM</button>
                    </div>
                    <p class="hint">Masukkan NIM. Identitas, judul, dan pembimbing akan tampil otomatis setelah SK terverifikasi.</p>
                    @error('nim') <p class="error">{{ $message }}</p> @enderror
                </div>
                <div id="sk-pembimbing-info" class="alert" style="display:none;margin-top:1rem;"></div>

                <div id="form-lanjutan" hidden>
                    <div class="divider"></div>

                    <h2 class="section-title">Data dari SK Pembimbing</h2>
                    <p class="hint" style="margin:-0.35rem 0 1rem;">Data ini diambil otomatis dan tidak dapat diubah di form ini.</p>
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

                    <div class="divider"></div>

                    <h2 class="section-title">Penguji sesuai Berkas Usul</h2>
                    @if (empty($dosens))
                        <div class="alert alert-err" style="margin-bottom:1rem;">
                            Daftar dosen belum tersedia. Hubungi akademik untuk menambahkan data dosen penguji.
                        </div>
                    @endif
                    <div class="grid cols-2">
                        <div>
                            <label class="label" for="penguji_1">Penguji 1 <span style="color:#b42318;">*</span></label>
                            <select id="penguji_1" name="penguji_1" class="field js-dosen-select" data-placeholder="— Cari nama sesuai berkas usul —">
                                <option value="">— Cari nama sesuai berkas usul —</option>
                                @foreach ($dosens as $nama)
                                    <option value="{{ $nama }}" @selected(old('penguji_1') === $nama)>{{ $nama }}</option>
                                @endforeach
                            </select>
                            @error('penguji_1') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label" for="penguji_2">Penguji 2 <span style="color:#b42318;">*</span></label>
                            <select id="penguji_2" name="penguji_2" class="field js-dosen-select" data-placeholder="— Cari nama sesuai berkas usul —">
                                <option value="">— Cari nama sesuai berkas usul —</option>
                                @foreach ($dosens as $nama)
                                    <option value="{{ $nama }}" @selected(old('penguji_2') === $nama)>{{ $nama }}</option>
                                @endforeach
                            </select>
                            <p class="hint">Isikan nama penguji sebagaimana tercantum dalam berkas usul Ketua Program Studi. Penguji 1 dan Penguji 2 harus berbeda, dan tidak boleh sama dengan pembimbing pada SK Pembimbing yang sudah terbit.</p>
                            @error('penguji_2') <p class="error">{{ $message }}</p> @enderror
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label class="label" for="file_usul_penguji">Unggah Berkas Usul dari Ketua Program Studi <span style="color:#b42318;">*</span></label>
                            <input id="file_usul_penguji" name="file_usul_penguji" type="file"
                                   accept=".pdf,.jpg,.jpeg,.png" class="field">
                            <p style="margin:.4rem 0 0;font-size:.8rem;color:var(--muted);">Wajib. PDF / JPG / PNG, maksimal 5 MB.</p>
                            @error('file_usul_penguji') <p class="error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="actions">
                        <p style="margin:0;font-size:.9rem;color:var(--muted);">
                            Setelah terkirim, pantau status melalui menu Pelacakan dengan NIM.
                        </p>
                        <button type="submit" class="btn" id="btn-kirim">Kirim permohonan</button>
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
                    '). Cantumkan nama penguji sesuai berkas usul dan unggah usulan Ketua Program Studi.'
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
                        showInfo(false, 'Pemeriksaan NIM gagal. Silakan coba kembali.');
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
