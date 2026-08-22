<?php

namespace App\Services;

use App\Models\PermohonanPenguji;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SkPengujiGenerator
{
    public function generate(PermohonanPenguji $permohonan): string
    {
        @set_time_limit(120);

        if (blank($permohonan->sk_token)) {
            $permohonan->forceFill([
                'sk_token' => Str::random(48),
            ])->save();
        }

        $pdf = $this->makePdf($permohonan->fresh(['mahasiswa.judulSkripsiAktif']) ?? $permohonan, preview: false);

        $directory = 'sk-penguji';
        Storage::disk('public')->makeDirectory($directory);

        $path = $directory.'/SK-'.$permohonan->id.'-'.now()->format('YmdHis').'.pdf';
        $written = Storage::disk('public')->put($path, $pdf->output());

        if ($written === false) {
            throw new \RuntimeException('Gagal menyimpan PDF SK ke storage. Periksa permission folder storage/app/public.');
        }

        return $path;
    }

    /**
     * Simpan perubahan isian SK lalu generate ulang PDF.
     * Nomor SK dan tanggal penetapan tidak diubah.
     *
     * @param  array<string, mixed>  $data
     */
    public function perbaruiDanGenerateUlang(PermohonanPenguji $permohonan, array $data): string
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
                'penguji_1',
                'penguji_2',
                'file_usul_penguji',
            ]));

            $permohonan->nomor_sk = $nomorSk;
            $permohonan->tanggal_sk = $tanggalSk;
            $permohonan->save();
        });

        $permohonan = $permohonan->fresh(['mahasiswa.judulSkripsiAktif']) ?? $permohonan;
        $lama = $permohonan->file_sk;
        $path = $this->generate($permohonan);
        $permohonan->forceFill(['file_sk' => $path])->saveQuietly();

        if (filled($lama) && $lama !== $path) {
            Storage::disk('public')->delete($lama);
        }

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
     * Nomor urut 3 digit per tahun berjalan, dari nomor terbesar yang sudah terpakai.
     */
    public function nextNomorSk(): string
    {
        return DB::transaction(fn (): string => $this->buildNomor(lock: true));
    }

    public function peekNextNomor(): string
    {
        return $this->buildNomor(lock: false);
    }

    /**
     * Kunci baris nomor SK, alokasikan nomor berikutnya, lalu simpan dalam transaksi yang sama.
     *
     * @param  callable(string $nomorSk): void  $persist
     */
    public function allocateNomorSk(callable $persist): string
    {
        return retry(3, function () use ($persist): string {
            return DB::transaction(function () use ($persist): string {
                $nomorSk = $this->buildNomor(lock: true);
                $persist($nomorSk);

                return $nomorSk;
            });
        }, 50, fn (\Throwable $e): bool => $e instanceof UniqueConstraintViolationException);
    }

    protected function buildNomor(bool $lock): string
    {
        $year = now()->format('Y');

        $query = PermohonanPenguji::query()
            ->whereNotNull('nomor_sk')
            ->where('nomor_sk', 'like', '%/SK-PENGUJI/%/'.$year);

        if ($lock) {
            $query->lockForUpdate();
        }

        return SkNomorAllocator::next('SK-PENGUJI', $query->pluck('nomor_sk'));
    }

    protected function draftForPreview(PermohonanPenguji $permohonan): PermohonanPenguji
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
        $permohonan->loadMissing(['mahasiswa.judulSkripsiAktif']);
        $mahasiswa = $permohonan->mahasiswa;
        $nim = $mahasiswa?->nim ?? $permohonan->mahasiswa_nim;

        $trackingUrl = route('permohonan.tracking', ['nim' => $nim]);
        $token = (string) $permohonan->sk_token;
        $ttdUrl = filled($token) && ! str_starts_with($token, 'preview-')
            ? route('sk.penguji.verify', ['token' => $token])
            : url('/sk-penguji/verifikasi/preview');

        $logoData = app(SkDocumentAssets::class)->logoDataUri();

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
        try {
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
        } catch (\Throwable $e) {
            report($e);

            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
        }
    }
}
