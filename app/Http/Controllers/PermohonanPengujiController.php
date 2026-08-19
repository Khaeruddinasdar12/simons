<?php

namespace App\Http\Controllers;

use App\Enums\StatusPermohonan;
use App\Http\Requests\StorePermohonanPengujiRequest;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PermohonanPenguji;
use App\Services\SkPengujiGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermohonanPengujiController extends Controller
{
    public function create(): View
    {
        return view('permohonan.penguji', [
            'dosens' => Dosen::optionsForSelect(),
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $nim = trim((string) $request->query('nim', ''));

        if ($nim === '' || strlen($nim) > 30) {
            return response()->json([
                'eligible' => false,
                'message' => 'NIM wajib diisi.',
            ], 422);
        }

        $mahasiswa = Mahasiswa::query()
            ->with('pembimbingTerbitTerakhir')
            ->find($nim);

        if (! $mahasiswa) {
            return response()->json([
                'eligible' => false,
                'message' => 'NIM tidak ditemukan. SK Penguji hanya untuk mahasiswa yang SK Pembimbingnya sudah terbit di sistem ini.',
            ]);
        }

        $skPembimbing = $mahasiswa->pembimbingTerbitTerakhir;

        if (! $skPembimbing) {
            return response()->json([
                'eligible' => false,
                'message' => 'SK Pembimbing untuk NIM ini belum terbit. Pengajuan SK Penguji belum dapat dilanjutkan.',
            ]);
        }

        if (! $mahasiswa->bisaAjukanPenguji()) {
            return response()->json([
                'eligible' => false,
                'message' => 'Masih ada permohonan SK Penguji yang aktif untuk NIM ini. Pantau statusnya di halaman tracking.',
            ]);
        }

        return response()->json([
            'eligible' => true,
            'mahasiswa' => [
                'nim' => $mahasiswa->nim,
                'nama_lengkap' => $mahasiswa->nama_lengkap,
                'tempat_lahir' => $mahasiswa->tempat_lahir,
                'tanggal_lahir' => $mahasiswa->tanggal_lahir?->translatedFormat('d F Y'),
                'alamat_lengkap' => $mahasiswa->alamat_lengkap,
                'no_hp' => $mahasiswa->no_hp,
                'email' => $mahasiswa->email,
                'program_studi' => $mahasiswa->program_studi?->value,
            ],
            'judul_skripsi' => $skPembimbing->judul_skripsi,
            'semester' => $skPembimbing->semester,
            'pembimbing_1' => $skPembimbing->pembimbing_1,
            'pembimbing_2' => $skPembimbing->pembimbing_2,
            'nomor_sk_pembimbing' => $skPembimbing->nomor_sk,
        ]);
    }

    public function store(StorePermohonanPengujiRequest $request): RedirectResponse
    {
        $path = $request->file('file_usul_penguji')
            ->store('usul-penguji', 'public');

        $nim = trim((string) $request->input('nim'));

        DB::transaction(function () use ($request, $path, $nim): void {
            $mahasiswa = Mahasiswa::query()->findOrFail($nim);

            $skPembimbing = $mahasiswa->pembimbingTerbitTerakhir()
                ->lockForUpdate()
                ->firstOrFail();

            PermohonanPenguji::create([
                ...$request->permohonanAttributes(),
                'mahasiswa_nim' => $nim,
                'permohonan_pembimbing_id' => $skPembimbing->id,
                'judul_skripsi' => $skPembimbing->judul_skripsi,
                'semester' => $skPembimbing->semester,
                'file_usul_penguji' => $path,
                'status' => StatusPermohonan::Diajukan,
            ]);
        });

        return redirect()
            ->route('permohonan.tracking', ['nim' => $nim])
            ->with('success', 'Permohonan SK Penguji berhasil dikirim. Anda dapat memantau statusnya di halaman tracking.');
    }

    public function verifySk(string $token): View
    {
        $permohonan = PermohonanPenguji::query()
            ->with('mahasiswa')
            ->where('sk_token', $token)
            ->where('status', StatusPermohonan::SkTerbit)
            ->firstOrFail();

        return view('sk.verify-penguji', [
            'permohonan' => $permohonan,
            'config' => config('sk'),
        ]);
    }

    public function downloadSk(PermohonanPenguji $permohonanPenguji): StreamedResponse
    {
        abort_unless(
            $permohonanPenguji->status === StatusPermohonan::SkTerbit && filled($permohonanPenguji->file_sk),
            404
        );

        abort_unless(Storage::disk('public')->exists($permohonanPenguji->file_sk), 404);

        return Storage::disk('public')->download(
            $permohonanPenguji->file_sk,
            'SK-Penguji-'.$permohonanPenguji->mahasiswa_nim.'.pdf'
        );
    }

    public function previewSk(PermohonanPenguji $permohonanPenguji): View
    {
        $this->authorize('previewSk', $permohonanPenguji);

        $permohonanPenguji->loadMissing('mahasiswa');

        return app(SkPengujiGenerator::class)->previewHtml($permohonanPenguji);
    }
}
