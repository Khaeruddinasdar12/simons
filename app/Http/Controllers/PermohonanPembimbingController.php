<?php

namespace App\Http\Controllers;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Http\Requests\StorePermohonanPembimbingRequest;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PermohonanPembimbing;
use App\Services\SkPembimbingGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermohonanPembimbingController extends Controller
{
    public function create(): View
    {
        return view('permohonan.create', [
            'programStudi' => ProgramStudi::options(),
            'dosens' => Dosen::optionsForSelect(),
        ]);
    }

    public function store(StorePermohonanPembimbingRequest $request): RedirectResponse
    {
        $path = $request->file('file_usul_pembimbing')
            ->store('usul-pembimbing', 'public');

        $nim = trim((string) $request->input('nim'));

        DB::transaction(function () use ($request, $path, $nim): void {
            Mahasiswa::query()->updateOrCreate(
                ['nim' => $nim],
                $request->mahasiswaAttributes()
            );

            PermohonanPembimbing::create([
                ...$request->permohonanAttributes(),
                'mahasiswa_nim' => $nim,
                'file_usul_pembimbing' => $path,
                'status' => StatusPermohonan::Diajukan,
            ]);
        });

        return redirect()
            ->route('permohonan.tracking', ['nim' => $nim])
            ->with('success', 'Permohonan usul pembimbing berhasil dikirim. Anda dapat memantau statusnya di halaman tracking.');
    }

    public function tracking(Request $request): View
    {
        $nim = trim((string) $request->query('nim', ''));
        $mahasiswa = null;
        $permohonans = collect();

        if ($nim !== '') {
            $request->validate([
                'nim' => ['required', 'string', 'max:30'],
            ]);

            $mahasiswa = Mahasiswa::query()
                ->with([
                    'permohonanPembimbing' => fn ($q) => $q->latest(),
                    'permohonanPenguji' => fn ($q) => $q->latest(),
                    'undanganMunaqasyah' => fn ($q) => $q->latest(),
                    'pembimbingTerbitTerakhir',
                    'pengujiTerbitTerakhir',
                    'judulSkripsiAktif',
                ])
                ->find($nim);

            $permohonans = $mahasiswa?->permohonanPembimbing ?? collect();
        }

        return view('permohonan.tracking', [
            'nim' => $nim,
            'mahasiswa' => $mahasiswa,
            'permohonans' => $permohonans,
            'searched' => $nim !== '',
        ]);
    }

    public function verifySk(string $token): View
    {
        $permohonan = PermohonanPembimbing::query()
            ->with('mahasiswa')
            ->where('sk_token', $token)
            ->where('status', StatusPermohonan::SkTerbit)
            ->firstOrFail();

        return view('sk.verify', [
            'permohonan' => $permohonan,
            'config' => config('sk'),
        ]);
    }

    public function downloadSk(PermohonanPembimbing $permohonan): StreamedResponse
    {
        abort_unless(
            $permohonan->status === StatusPermohonan::SkTerbit && filled($permohonan->file_sk),
            404
        );

        abort_unless(Storage::disk('public')->exists($permohonan->file_sk), 404);

        return Storage::disk('public')->download(
            $permohonan->file_sk,
            'SK-Pembimbing-'.$permohonan->mahasiswa_nim.'.pdf'
        );
    }

    public function previewSk(PermohonanPembimbing $permohonan): View
    {
        $this->authorize('previewSk', $permohonan);

        $permohonan->loadMissing('mahasiswa');

        return app(SkPembimbingGenerator::class)->previewHtml($permohonan);
    }
}
