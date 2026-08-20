<?php

namespace App\Services;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanPembimbing;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SkPembimbingGenerator
{
    public function generate(PermohonanPembimbing $permohonan): string
    {
        if (blank($permohonan->sk_token)) {
            $permohonan->forceFill([
                'sk_token' => Str::random(48),
            ])->save();
        }

        $pdf = $this->makePdf($permohonan->fresh(['mahasiswa']), preview: false);

        $path = 'sk-pembimbing/SK-'.$permohonan->id.'-'.now()->format('YmdHis').'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Simpan perubahan isian SK lalu generate ulang PDF.
     * Nomor SK dan tanggal penetapan tidak diubah.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbaruiDanGenerateUlang(PermohonanPembimbing $permohonan, array $data): string
    {
        $nomorSk = $permohonan->nomor_sk;
        $tanggalSk = $permohonan->tanggal_sk;

        DB::transaction(function () use ($permohonan, $data, $nomorSk, $tanggalSk): void {
            $mahasiswa = $permohonan->mahasiswa;
            $mahasiswaData = $data['mahasiswa'] ?? [];

            if ($mahasiswa && is_array($mahasiswaData) && $mahasiswaData !== []) {
                $nimBaru = trim((string) ($mahasiswaData['nim'] ?? $mahasiswa->nim));
                $mahasiswa->fill(Arr::except($mahasiswaData, ['nim']));

                if ($nimBaru !== '' && $nimBaru !== $mahasiswa->nim) {
                    $mahasiswa->nim = $nimBaru;
                }

                $mahasiswa->save();
                $permohonan->mahasiswa_nim = $mahasiswa->nim;
            }

            $permohonan->fill(Arr::only($data, [
                'semester',
                'judul_skripsi',
                'pembimbing_1',
                'pembimbing_2',
                'file_usul_pembimbing',
            ]));

            $permohonan->nomor_sk = $nomorSk;
            $permohonan->tanggal_sk = $tanggalSk;
            $permohonan->save();
        });

        $permohonan = $permohonan->fresh(['mahasiswa']) ?? $permohonan;
        $lama = $permohonan->file_sk;
        $path = $this->generate($permohonan);
        $permohonan->forceFill(['file_sk' => $path])->saveQuietly();

        if (filled($lama) && $lama !== $path) {
            Storage::disk('public')->delete($lama);
        }

        return $path;
    }

    /**
     * Browser HTML preview (ringan) — layout sama dengan template DomPDF.
     * Digunakan semua role via tombol Preview SK.
     */
    public function previewHtml(PermohonanPembimbing $permohonan): View
    {
        return view('sk.pembimbing', $this->viewData(
            $this->draftForPreview($permohonan),
            preview: true,
            browserPreview: true,
        ));
    }

    /**
     * Format: 001/SK-PEMBIMBING/08/2026
     * Nomor urut 3 digit per tahun berjalan.
     */
    public function nextNomorSk(): string
    {
        return DB::transaction(function (): string {
            $year = now()->format('Y');
            $month = now()->format('m');

            $last = PermohonanPembimbing::query()
                ->where('status', StatusPermohonan::SkTerbit)
                ->whereYear('tanggal_sk', $year)
                ->whereNotNull('nomor_sk')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('nomor_sk');

            $next = 1;
            if (is_string($last) && preg_match('/^(\d{3})\/SK-PEMBIMBING\//', $last, $matches)) {
                $next = ((int) $matches[1]) + 1;
            } else {
                $count = PermohonanPembimbing::query()
                    ->where('status', StatusPermohonan::SkTerbit)
                    ->whereYear('tanggal_sk', $year)
                    ->count();
                $next = $count + 1;
            }

            return sprintf('%03d/SK-PEMBIMBING/%s/%s', $next, $month, $year);
        });
    }

    public function peekNextNomor(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $last = PermohonanPembimbing::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->whereYear('tanggal_sk', $year)
            ->whereNotNull('nomor_sk')
            ->orderByDesc('id')
            ->value('nomor_sk');

        $next = 1;
        if (is_string($last) && preg_match('/^(\d{3})\/SK-PEMBIMBING\//', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        } else {
            $count = PermohonanPembimbing::query()
                ->where('status', StatusPermohonan::SkTerbit)
                ->whereYear('tanggal_sk', $year)
                ->count();
            $next = $count + 1;
        }

        return sprintf('%03d/SK-PEMBIMBING/%s/%s', $next, $month, $year);
    }

    protected function draftForPreview(PermohonanPembimbing $permohonan): PermohonanPembimbing
    {
        $permohonan->loadMissing(['mahasiswa.judulSkripsiAktif']);

        $attrs = $permohonan->getAttributes();

        if (blank($attrs['nomor_sk'] ?? null)) {
            $attrs['nomor_sk'] = $this->peekNextNomor().' (PREVIEW)';
        }

        if (blank($attrs['tanggal_sk'] ?? null)) {
            $attrs['tanggal_sk'] = now()->toDateString();
        }

        if (blank($attrs['sk_token'] ?? null)) {
            $attrs['sk_token'] = 'preview-'.Str::random(16);
        }

        $draft = new PermohonanPembimbing;
        $draft->forceFill($attrs);
        $draft->setRelation('mahasiswa', $permohonan->mahasiswa);

        return $draft;
    }

    protected function makePdf(PermohonanPembimbing $permohonan, bool $preview = false): DomPdf
    {
        return Pdf::loadView('sk.pembimbing', $this->viewData($permohonan, $preview, false))
            ->setPaper(config('sk.paper'), 'portrait');
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(PermohonanPembimbing $permohonan, bool $preview, bool $browserPreview): array
    {
        $permohonan->loadMissing(['mahasiswa.judulSkripsiAktif']);
        $mahasiswa = $permohonan->mahasiswa;
        $nim = $mahasiswa?->nim ?? $permohonan->mahasiswa_nim;

        $trackingUrl = route('permohonan.tracking', ['nim' => $nim]);
        $token = (string) $permohonan->sk_token;
        $ttdUrl = filled($token) && ! str_starts_with($token, 'preview-')
            ? route('sk.verify', ['token' => $token])
            : url('/sk/verifikasi/preview');

        $logoPath = public_path('logoiainbone.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $prodiValue = $mahasiswa?->program_studi?->value;

        return [
            'permohonan' => $permohonan,
            'mahasiswa' => $mahasiswa,
            'judulSkripsi' => $permohonan->judul_skripsi,
            'logoData' => $logoData,
            'qrTtd' => $this->qrDataUri($ttdUrl),
            'qrTracking' => $this->qrDataUri($trackingUrl),
            'config' => config('sk'),
            'prodiLengkap' => config('sk.prodi_lengkap')[$prodiValue] ?? $prodiValue,
            'isPreview' => $preview,
            'isBrowserPreview' => $browserPreview,
        ];
    }

    protected function qrDataUri(string $data): string
    {
        // v5: Builder::create(); v6+: constructor (create() dihapus).
        if (method_exists(Builder::class, 'create')) {
            return Builder::create()
                ->writer(new PngWriter)
                ->data($data)
                ->size(180)
                ->margin(2)
                ->build()
                ->getDataUri();
        }

        $builder = new Builder(
            writer: new PngWriter,
            data: $data,
            size: 180,
            margin: 2,
        );

        return $builder->build()->getDataUri();
    }
}
