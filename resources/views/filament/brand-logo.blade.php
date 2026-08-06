@php
    $appName = config('app.name');
    $appFullName = config('app.full_name');
@endphp

@auth
    <div style="display:flex;align-items:center;gap:.65rem;min-width:0;height:100%;">
        <img
            src="{{ asset('logoiainbone.png') }}"
            alt="Logo IAIN Bone"
            style="height:2.35rem;width:auto;object-fit:contain;flex-shrink:0;"
        >
        <div style="display:flex;flex-direction:column;justify-content:center;gap:.1rem;min-width:0;line-height:1.2;">
            <span style="font-weight:700;font-size:.95rem;letter-spacing:.03em;color:inherit;">{{ $appName }}</span>
            <span style="font-size:.58rem;font-weight:500;opacity:.72;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:11.5rem;">{{ $appFullName }}</span>
        </div>
    </div>
@else
    <div style="display:flex;align-items:center;justify-content:center;gap:1rem;height:100%;">
        <img
            src="{{ asset('logokemenag.png') }}"
            alt="Logo Kementerian Agama"
            style="height:3rem;width:auto;object-fit:contain;"
        >
        <img
            src="{{ asset('logoiainbone.png') }}"
            alt="Logo IAIN Bone"
            style="height:3rem;width:auto;object-fit:contain;"
        >
    </div>
@endauth
