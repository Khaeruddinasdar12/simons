<nav class="nav" aria-label="Navigasi utama">
    <a href="{{ route('home') }}" class="logo-pair">
        <img src="{{ asset('logokemenag.png') }}" alt="Logo Kementerian Agama">
        <img src="{{ asset('logoiainbone.png') }}" alt="Logo IAIN Bone">
        <div class="nav-brand">
            <strong>{{ config('app.name') }}</strong>
            <span>{{ config('app.full_name') }}</span>
        </div>
    </a>
    <input type="checkbox" id="nav-toggle" class="nav-toggle" aria-hidden="true" tabindex="-1">
    <label for="nav-toggle" class="nav-burger" aria-label="Buka atau tutup menu">
        <span></span>
        <span></span>
        <span></span>
        <em>Menu</em>
    </label>
    <div class="nav-panel">
        <div class="nav-links">
            <a href="{{ route('home') }}"@class(['primary' => ($active ?? '') === 'alur'])>Alur</a>
            <a href="{{ route('pembimbing.create') }}"@class(['primary' => ($active ?? '') === 'pembimbing'])>SK Pembimbing</a>
            <a href="{{ route('penguji.create') }}"@class(['primary' => ($active ?? '') === 'penguji'])>SK Penguji</a>
            <a href="{{ route('permohonan.tracking') }}"@class(['primary' => ($active ?? '') === 'tracking'])>Pelacakan</a>
        </div>
        <a href="{{ url('/admin') }}" class="nav-login">Masuk Admin</a>
    </div>
</nav>
