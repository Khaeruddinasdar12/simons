<?php

namespace App\Services;

use App\Enums\StatusPermohonan;
use App\Models\PermohonanPenguji;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SkPengujiGenerator
{
    public function generate(PermohonanPenguji $permohonan): string
    {
        if (blank($permohonan->sk_token)) {
            $permohonan->forceFill([
                'sk_token' => Str::random(48),
            ])->save();
        }

        $pdf = $this->makePdf($permohonan->fresh(['mahasiswa']), preview: false);

        $path = 'sk-penguji/SK-'.$permohonan->id.'-'.now()->format('YmdHis').'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function previewHtml(PermohonanPenguji $permohonan): View
    {
        return view('sk.penguji', $this->viewData(
            $this->draftForPreview($permohonan),
            preview: true,
            browserPreview: true,
        ));
    }

    /**
     * Format: 001/SK-PENGUJI/08/2026
     */
    public function nextNomorSk(): string
    {
        return DB::transaction(function (): string {
            return $this->buildNomor(lock: true);
        });
    }

    public function peekNextNomor(): string
    {
        return $this->buildNomor(lock: false);
    }

    protected function buildNomor(bool $lock): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $query = PermohonanPenguji::query()
            ->where('status', StatusPermohonan::SkTerbit)
            ->whereYear('tanggal_sk', $year)
            ->whereNotNull('nomor_sk')
            ->orderByDesc('id');

        if ($lock) {
            $query->lockForUpdate();
        }

        $last = $query->value('nomor_sk');

        $next = 1;
        if (is_string($last) && preg_match('/^(\d{3})\/SK-PENGUJI\//', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        } else {
            $count = PermohonanPenguji::query()
                ->where('status', StatusPermohonan::SkTerbit)
                ->whereYear('tanggal_sk', $year)
                ->count();
            $next = $count + 1;
        }

        return sprintf('%03d/SK-PENGUJI/%s/%s', $next, $month, $year);
    }

    protected function draftForPreview(PermohonanPenguji $permohonan): PermohonanPenguji
    {
        $permohonan->loadMissing('mahasiswa');

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

        $draft = new PermohonanPenguji;
        $draft->forceFill($attrs);
        $draft->setRelation('mahasiswa', $permohonan->mahasiswa);

        return $draft;
    }

    protected function makePdf(PermohonanPenguji $permohonan, bool $preview = false): DomPdf
    {
        return Pdf::loadView('sk.penguji', $this->viewData($permohonan, $preview, false))
            ->setPaper(config('sk.paper'), 'portrait');
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(PermohonanPenguji $permohonan, bool $preview, bool $browserPreview): array
    {
        $permohonan->loadMissing('mahasiswa');
        $mahasiswa = $permohonan->mahasiswa;
        $nim = $mahasiswa?->nim ?? $permohonan->mahasiswa_nim;

        $trackingUrl = route('permohonan.tracking', ['nim' => $nim]);
        $token = (string) $permohonan->sk_token;
        $ttdUrl = filled($token) && ! str_starts_with($token, 'preview-')
            ? route('sk.penguji.verify', ['token' => $token])
            : url('/sk-penguji/verifikasi/preview');

        $logoPath = public_path('logoiainbone.png');
        $logoData = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode((string) file_get_contents($logoPath))
            : null;

        $prodiValue = $mahasiswa?->program_studi?->value;

        return [
            'permohonan' => $permohonan,
            'mahasiswa' => $mahasiswa,
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
