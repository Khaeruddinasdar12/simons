<x-mail::message>
# Penerbitan SK Penguji Telah Selesai

Assalamu’alaikum, **{{ $mahasiswa->nama_lengkap }}**.

Permohonan **SK Penguji Skripsi** Anda telah **berhasil diterbitkan**.

**Detail ringkas**
- NIM: {{ $mahasiswa->nim }}
- Program Studi: {{ $mahasiswa->program_studi?->getLabel() }}
- Nomor SK: {{ $permohonan->nomor_sk }}
- Tanggal SK: {{ $permohonan->tanggal_sk?->translatedFormat('d F Y') }}
- Penguji 1: {{ $permohonan->penguji_1 }}
- Penguji 2: {{ $permohonan->penguji_2 }}

Anda dapat memantau status dan mengunduh file PDF SK melalui halaman tracking.

<x-mail::button :url="$trackingUrl">
Buka Halaman Tracking
</x-mail::button>

Hormat kami,<br>
{{ config('app.name') }} — FSHI IAIN Bone
</x-mail::message>
