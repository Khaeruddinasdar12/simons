@php
    $warnaStatus = match ($item->status) {
        \App\Enums\StatusPermohonan::SkTerbit => 'background:#e8f5ee;color:#14532d;',
        \App\Enums\StatusPermohonan::Ditolak => 'background:#fde8e8;color:#9b1c1c;',
        \App\Enums\StatusPermohonan::DikembalikanAkademik => 'background:#e8f0fe;color:#1e40af;',
        \App\Enums\StatusPermohonan::DikirimPimpinan => 'background:#fff7e6;color:#92400e;',
        default => 'background:#f3f4f6;color:#374151;',
    };
    $catatan = $item->catatanPublik();
@endphp

<article class="panel">
    <div class="panel-head">
        <h3>Data pengajuan</h3>
        <span class="badge" style="{{ $warnaStatus }}">{{ $item->status->labelPublik() }}</span>
    </div>

    <ol class="progress" aria-label="Progres permohonan">
        @foreach ($item->progresPublik() as $step)
            <li class="progress-step is-{{ $step['state'] }}">
                <span class="dot" aria-hidden="true"></span>
                <span class="progress-label">{{ $step['label'] }}</span>
            </li>
        @endforeach
    </ol>
    <p class="ket">{{ $item->status->keteranganPublik() }}</p>

    @if ($catatan)
        <p class="note {{ $item->status === \App\Enums\StatusPermohonan::DikembalikanAkademik ? 'warn' : '' }}">
            {{ $catatan }}
        </p>
    @endif

    <dl class="facts">
        <div class="facts-full">
            <dt>Judul skripsi</dt>
            <dd>{{ $item->judul_skripsi }}</dd>
        </div>
        <div>
            <dt>{{ $label1 }}</dt>
            <dd>{{ $nama1 }}</dd>
        </div>
        <div>
            <dt>{{ $label2 }}</dt>
            <dd>{{ $nama2 ?: '—' }}</dd>
        </div>
        <div>
            <dt>Semester</dt>
            <dd>{{ $item->semester }}</dd>
        </div>
        <div>
            <dt>Diajukan</dt>
            <dd>{{ $item->created_at?->translatedFormat('d F Y') }}</dd>
        </div>
        @if ($item->nomor_sk)
            <div class="facts-full">
                <dt>Nomor SK</dt>
                <dd>{{ $item->nomor_sk }}@if ($item->tanggal_sk) · {{ $item->tanggal_sk->translatedFormat('d F Y') }}@endif</dd>
            </div>
        @endif
    </dl>

    @if (! empty($unduhUrl))
        <div class="actions">
            <a class="btn" href="{{ $unduhUrl }}">{{ $unduhLabel }}</a>
        </div>
    @endif
</article>
