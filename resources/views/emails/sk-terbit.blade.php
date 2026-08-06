<x-mail::message>
# Penerbitan SK Pembimbing Telah Selesai

Assalamu’alaikum, **{{ $mahasiswa->nama_lengkap }}**.

Permohonan **SK Pembimbing Proposal Skripsi** Anda telah **berhasil diterbitkan**.

**Detail ringkas**
- NIM: {{ $mahasiswa->nim }}
- Program Studi: {{ $mahasiswa->program_studi?->value }}
- Nomor SK: {{ $permohonan->nomor_sk }}
- Tanggal SK: {{ $permohonan->tanggal_sk?->translatedFormat('d F Y') }}
- Pembimbing 1: {{ $permohonan->pembimbing_1 }}
- Pembimbing 2: {{ $permohonan->pembimbing_2 }}

Anda dapat memantau status dan mengunduh file PDF SK melalui halaman tracking.

<x-mail::button :url="$trackingUrl">
Buka Halaman Tracking
</x-mail::button>

Hormat kami,<br>
{{ config('app.name') }} — FSHI IAIN Bone
</x-mail::message>
