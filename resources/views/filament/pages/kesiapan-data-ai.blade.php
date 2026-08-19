<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Dosen siap rekomendasi</p>
            <p class="mt-1 text-2xl font-semibold">{{ $dosenSiap }} / {{ $dosenAktif }}</p>
            <p class="mt-1 text-xs text-gray-500">Aktif dan punya minimal 2 keahlian. {{ $dosenBelumSiap }} belum siap.</p>
        </div>
        <div class="rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Korpus judul SK terbit</p>
            <p class="mt-1 text-2xl font-semibold">{{ $korpus }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $korpusDitandaiMirip }} ditandai mirip. Target nyaman: 100+.</p>
        </div>
        <div class="rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Kamus istilah prodi</p>
            <p class="mt-1 text-2xl font-semibold">{{ $istilahAktif }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $keahlianAktif }} keahlian aktif di master.</p>
        </div>
    </div>

    <div class="mt-4 rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-sm font-semibold">Istilah per program studi</h3>
        <p class="mt-1 text-xs text-gray-500">Target awal ~40–80 istilah per prodi agar gerbang judul vs prodi bisa jalan.</p>
        <ul class="mt-3 grid gap-2 md:grid-cols-3">
            @foreach ($istilahPerProdi as $prodi => $jumlah)
                <li class="rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800">
                    <span class="font-medium">{{ $prodi }}</span>
                    <span class="float-right">{{ $jumlah }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mt-4 rounded-xl bg-white p-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-sm font-semibold">Jejak SK untuk histori dosen</h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            SK Pembimbing terbit: <strong>{{ $skPembimbingTerbit }}</strong>
            · SK Penguji terbit: <strong>{{ $skPengujiTerbit }}</strong>
            · Nama pembimbing terhubung ke master dosen:
            <strong>{{ $pembimbingTerhubung }} / {{ $pembimbingTotal }}</strong>
        </p>
        <p class="mt-2 text-xs text-gray-500">
            Setelah migrasi, jalankan <code>php artisan simons:siapkan-data-ai</code> untuk menautkan nama dosen
            pada SK lama dan mengisi korpus judul.
        </p>
    </div>
</x-filament-panels::page>
