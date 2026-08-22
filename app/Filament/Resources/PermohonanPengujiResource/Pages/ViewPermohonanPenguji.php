<?php

namespace App\Filament\Resources\PermohonanPengujiResource\Pages;

use App\Enums\StatusPermohonan;
use App\Filament\Resources\PermohonanPengujiResource;
use App\Filament\Support\HapusPermohonanUi;
use App\Models\PermohonanPenguji;
use App\Services\HapusPermohonanService;
use App\Services\SkPengujiMailService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;

class ViewPermohonanPenguji extends ViewRecord
{
    protected static string $resource = PermohonanPengujiResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Mahasiswa')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('mahasiswa.nim')->label('NIM'),
                        Infolists\Components\TextEntry::make('mahasiswa.nama_lengkap')->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('mahasiswa.tempat_tanggal_lahir')->label('Tempat / Tanggal Lahir'),
                        Infolists\Components\TextEntry::make('mahasiswa.no_hp')->label('No. HP'),
                        Infolists\Components\TextEntry::make('mahasiswa.email')->label('Email')->placeholder('-'),
                        Infolists\Components\TextEntry::make('mahasiswa.alamat_lengkap')->label('Alamat Lengkap')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('mahasiswa.program_studi')->label('Program Studi')->badge(),
                        Infolists\Components\TextEntry::make('semester')->label('Semester'),
                    ]),

                Infolists\Components\Section::make('Skripsi & Penguji')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('judul_skripsi')
                            ->label('Judul Skripsi')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('judul_terkini')
                            ->label('Judul untuk permohonan berikutnya')
                            ->state(fn (PermohonanPenguji $record): string => $record->judulTerkini())
                            ->visible(fn (PermohonanPenguji $record): bool => $record->judul_skripsi !== $record->judulTerkini())
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('penguji_1')->label('Penguji 1'),
                        Infolists\Components\TextEntry::make('penguji_2')->label('Penguji 2'),
                        Infolists\Components\TextEntry::make('permohonanPembimbing.nomor_sk')
                            ->label('SK Pembimbing')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('file_usul_penguji')
                            ->label('File Usulan Kaprodi')
                            ->formatStateUsing(fn (?string $state): string => $state ? 'Lihat berkas' : '-')
                            ->url(fn (PermohonanPenguji $record): ?string => $record->file_usul_url, true)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Status Permohonan')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (StatusPermohonan $state): string => $state->label())
                            ->color(fn (StatusPermohonan $state): string => $state->color()),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Diajukan')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('nomor_sk')->label('Nomor SK')->placeholder('-'),
                        Infolists\Components\TextEntry::make('tanggal_sk')->label('Tanggal SK')->date('d/m/Y')->placeholder('-'),
                        Infolists\Components\TextEntry::make('file_sk')
                            ->label('File SK')
                            ->formatStateUsing(fn (?string $state): string => $state ? 'Unduh PDF SK' : '-')
                            ->url(fn (PermohonanPenguji $record): ?string => $record->file_sk
                                ? route('sk.penguji.download', $record)
                                : null, true)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Catatan Perizinan per Role')
                    ->description('Ringkasan status dan catatan dari setiap pejabat')
                    ->schema([
                        Infolists\Components\Section::make('Akademik')
                            ->icon('heroicon-o-academic-cap')
                            ->compact()
                            ->columns(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('akademik_proses')
                                    ->label('Status')
                                    ->badge()
                                    ->state(fn (PermohonanPenguji $record): string => filled($record->akademik_dikirim_at)
                                        ? 'Sudah dikirim ke pimpinan'
                                        : (filled($record->akademik_catatan) ? 'Ada catatan' : 'Belum diproses'))
                                    ->color(fn (PermohonanPenguji $record): string => filled($record->akademik_dikirim_at) ? 'success' : 'gray'),
                                Infolists\Components\TextEntry::make('akademik_dikirim_at')
                                    ->label('Waktu dikirim')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('akademikVerifier.name')
                                    ->label('Diproses oleh')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('akademik_catatan')
                                    ->label('Catatan')
                                    ->placeholder('Belum ada catatan')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),

                        Infolists\Components\Section::make('Kabag')
                            ->icon('heroicon-o-briefcase')
                            ->compact()
                            ->columns(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('kabag_status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state, PermohonanPenguji $record): string => $record->formatRoleStatus($state))
                                    ->color(fn (?string $state): string => match ($state) {
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    }),
                                Infolists\Components\TextEntry::make('kabag_verified_at')
                                    ->label('Waktu')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('kabagVerifier.name')
                                    ->label('Oleh')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('kabag_catatan')
                                    ->label('Catatan')
                                    ->placeholder('Belum ada catatan')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),

                        Infolists\Components\Section::make('Wakil Dekan 1')
                            ->icon('heroicon-o-user-group')
                            ->compact()
                            ->columns(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('wadek1_status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state, PermohonanPenguji $record): string => $record->formatRoleStatus($state))
                                    ->color(fn (?string $state): string => match ($state) {
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    }),
                                Infolists\Components\TextEntry::make('wadek1_verified_at')
                                    ->label('Waktu')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('wadek1Verifier.name')
                                    ->label('Oleh')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('wadek1_catatan')
                                    ->label('Catatan')
                                    ->placeholder('Belum ada catatan')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),

                        Infolists\Components\Section::make('Dekan')
                            ->icon('heroicon-o-building-library')
                            ->compact()
                            ->columns(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('dekan_status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state, PermohonanPenguji $record): string => $record->formatRoleStatus($state))
                                    ->color(fn (?string $state): string => match ($state) {
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'warning',
                                    }),
                                Infolists\Components\TextEntry::make('dekan_verified_at')
                                    ->label('Waktu')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('dekanVerifier.name')
                                    ->label('Oleh')
                                    ->placeholder('-'),
                                Infolists\Components\TextEntry::make('dekan_catatan')
                                    ->label('Catatan')
                                    ->placeholder('Belum ada catatan')
                                    ->columnSpanFull()
                                    ->prose(),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        return [
            Actions\Action::make('previewSk')
                ->label('Preview SK')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->tooltip('Buka preview di browser (tanpa generate PDF di server)')
                ->url(fn (): string => route('sk.penguji.preview', $this->getRecord()))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $user->can('previewSk', $this->getRecord())),

            Actions\Action::make('generateUlangSk')
                ->label('Generate Ulang SK')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn (): bool => $user->can('generateUlangSk', $this->getRecord()))
                ->fillForm(fn (): array => PermohonanPengujiResource::generateUlangSkFillForm($this->getRecord()))
                ->form(PermohonanPengujiResource::generateUlangSkFormSchema())
                ->modalHeading('Generate Ulang SK Penguji')
                ->modalDescription('Semua isian dapat diubah. Nomor SK dan tanggal penetapan tidak berubah. PDF SK akan dibuat ulang dari data ini.')
                ->modalWidth('7xl')
                ->modalSubmitActionLabel('Simpan & Generate Ulang SK')
                ->action(function (array $data): void {
                    PermohonanPengujiResource::prosesGenerateUlangSk($this->getRecord(), $data);
                    $this->refreshRecord();
                }),

            Actions\EditAction::make()
                ->visible(fn (): bool => $user->can('update', $this->getRecord())),

            Actions\Action::make('kirimPimpinan')
                ->label('Kirim ke Kabag, Wadek 1 & Dekan')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn (): bool => $user->can('sendToPimpinan', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('akademik_catatan')
                        ->label('Catatan akademik')
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Kirim ke Pimpinan')
                ->modalDescription('Permohonan dikirim bersamaan ke Kabag, Wadek 1, dan Dekan. Dekan baru dapat menerbitkan SK setelah Kabag dan Wadek 1 menyetujui. Jika ada penolakan dari pimpinan, permohonan akan dikembalikan ke akademik.')
                ->action(function (array $data) use ($user): void {
                    /** @var PermohonanPenguji $record */
                    $record = $this->getRecord();

                    $record->update([
                        'status' => StatusPermohonan::DikirimPimpinan,
                        'akademik_verified_by' => $user->id,
                        'akademik_verified_at' => now(),
                        'akademik_dikirim_at' => now(),
                        'akademik_catatan' => $data['akademik_catatan'] ?? $record->akademik_catatan,
                        'kabag_status' => 'pending',
                        'kabag_verified_by' => null,
                        'kabag_verified_at' => null,
                        'wadek1_status' => 'pending',
                        'wadek1_verified_by' => null,
                        'wadek1_verified_at' => null,
                        'dekan_status' => 'pending',
                        'dekan_verified_by' => null,
                        'dekan_verified_at' => null,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Permohonan dikirim ke pimpinan')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('tolakAkademik')
                ->label('Tolak Permanen')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $user->can('rejectFinal', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('akademik_catatan')
                        ->label('Alasan penolakan total')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Tolak Permanen')
                ->modalDescription('Penolakan ini bersifat final. Hanya akademik yang dapat menolak permohonan secara total.')
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'status' => StatusPermohonan::Ditolak,
                        'akademik_verified_by' => $user->id,
                        'akademik_verified_at' => now(),
                        'akademik_catatan' => $data['akademik_catatan'],
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Permohonan ditolak secara permanen')
                        ->danger()
                        ->send();
                }),

            Actions\Action::make('verifikasiKabag')
                ->label('Setujui (Kabag)')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (): bool => $user->can('verifyKabag', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('kabag_catatan')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'kabag_status' => 'disetujui',
                        'kabag_verified_by' => $user->id,
                        'kabag_verified_at' => now(),
                        'kabag_catatan' => $data['kabag_catatan'] ?? null,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Disetujui oleh Kabag')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('tolakKabag')
                ->label('Kembalikan ke Akademik')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (): bool => $user->can('verifyKabag', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('kabag_catatan')
                        ->label('Catatan pengembalian')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Kembalikan ke Akademik')
                ->modalDescription('Permohonan akan dikembalikan ke akademik untuk ditinjau ulang. Penolakan final hanya dilakukan oleh akademik.')
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'kabag_status' => 'ditolak',
                        'kabag_verified_by' => $user->id,
                        'kabag_verified_at' => now(),
                        'kabag_catatan' => $data['kabag_catatan'],
                        'status' => StatusPermohonan::DikembalikanAkademik,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Dikembalikan ke akademik oleh Kabag')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('verifikasiWadek1')
                ->label('Setujui (Wadek 1)')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (): bool => $user->can('verifyWadek1', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('wadek1_catatan')
                        ->label('Catatan')
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'wadek1_status' => 'disetujui',
                        'wadek1_verified_by' => $user->id,
                        'wadek1_verified_at' => now(),
                        'wadek1_catatan' => $data['wadek1_catatan'] ?? null,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Disetujui oleh Wadek 1')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('tolakWadek1')
                ->label('Kembalikan ke Akademik')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (): bool => $user->can('verifyWadek1', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('wadek1_catatan')
                        ->label('Catatan pengembalian')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Kembalikan ke Akademik')
                ->modalDescription('Permohonan akan dikembalikan ke akademik untuk ditinjau ulang. Penolakan final hanya dilakukan oleh akademik.')
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'wadek1_status' => 'ditolak',
                        'wadek1_verified_by' => $user->id,
                        'wadek1_verified_at' => now(),
                        'wadek1_catatan' => $data['wadek1_catatan'],
                        'status' => StatusPermohonan::DikembalikanAkademik,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Dikembalikan ke akademik oleh Wadek 1')
                        ->warning()
                        ->send();
                }),

            Actions\Action::make('terbitkanSk')
                ->label('Verifikasi & Terbitkan SK')
                ->icon('heroicon-o-document-check')
                ->color('success')
                ->visible(fn (): bool => $user->can('issueSk', $this->getRecord()))
                ->requiresConfirmation()
                ->modalHeading('Verifikasi & Terbitkan SK')
                ->modalDescription(fn (): string => 'Nomor dan tanggal SK akan digenerate otomatis (contoh: '
                    .app(\App\Services\SkPengujiGenerator::class)->peekNextNomor()
                    .'). Sistem juga membuat PDF SK serta mengirim notifikasi email ke mahasiswa.')
                ->modalSubmitActionLabel('Ya, Terbitkan SK')
                ->action(function () use ($user): void {
                    /** @var PermohonanPenguji $record */
                    $record = $this->getRecord();
                    $generator = app(\App\Services\SkPengujiGenerator::class);

                    try {
                        $nomorSk = $generator->allocateNomorSk(function (string $nomorSk) use ($record, $user): void {
                            $record->update([
                                'dekan_status' => 'disetujui',
                                'dekan_verified_by' => $user->id,
                                'dekan_verified_at' => now(),
                                'nomor_sk' => $nomorSk,
                                'tanggal_sk' => now()->toDateString(),
                                'status' => StatusPermohonan::SkTerbit,
                            ]);
                        });
                    } catch (UniqueConstraintViolationException $e) {
                        Notification::make()
                            ->title('Gagal menerbitkan SK')
                            ->body('Nomor SK bentrok dengan data yang sudah ada. Silakan coba lagi.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $path = $generator->generate($record->fresh());

                    $record->update([
                        'file_sk' => $path,
                    ]);

                    $record = $record->fresh();
                    $emailTerkirim = false;
                    $emailError = null;
                    $emailPenerima = null;

                    try {
                        $emailPenerima = app(SkPengujiMailService::class)->sendTerbitNotification($record);
                        $emailTerkirim = true;
                    } catch (\InvalidArgumentException $e) {
                        $emailError = $e->getMessage();
                    } catch (\Throwable $e) {
                        report($e);
                        $emailError = $e->getMessage();
                    }

                    $this->refreshRecord();

                    if ($emailTerkirim) {
                        Notification::make()
                            ->title('SK berhasil diterbitkan')
                            ->body('Nomor: '.$nomorSk.' - Email dikirim ke '.$emailPenerima)
                            ->success()
                            ->send();
                    } elseif ($emailError) {
                        Notification::make()
                            ->title('SK terbit, tetapi email gagal')
                            ->body('Nomor: '.$nomorSk.' - '.$emailError)
                            ->warning()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('SK berhasil diterbitkan')
                            ->body('Nomor: '.$nomorSk.' - Mahasiswa belum mengisi email')
                            ->success()
                            ->send();
                    }
                }),

            Actions\Action::make('kirimUlangEmailSk')
                ->label('Kirim Ulang Email SK')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->visible(fn (): bool => $user->can('previewSk', $this->getRecord())
                    && $this->getRecord()->status === StatusPermohonan::SkTerbit
                    && filled($this->getRecord()->mahasiswa?->email)
                    && filled($this->getRecord()->file_sk))
                ->requiresConfirmation()
                ->modalHeading('Kirim ulang email SK')
                ->modalDescription(fn (): string => 'Email akan dikirim ke '.$this->getRecord()->mahasiswa?->email)
                ->action(function (): void {
                    $record = $this->getRecord()->fresh();

                    try {
                        $emailPenerima = app(SkPengujiMailService::class)->sendTerbitNotification($record);

                        Notification::make()
                            ->title('Email SK berhasil dikirim ulang')
                            ->body('Dikirim ke '.$emailPenerima.'. Cek Inbox dan folder Spam/Promosi. Jika kosong, buka Brevo → Transactional → Logs.')
                            ->success()
                            ->persistent()
                            ->send();
                    } catch (\Throwable $e) {
                        report($e);

                        Notification::make()
                            ->title('Gagal mengirim email')
                            ->body($e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),

            Actions\Action::make('tolakDekan')
                ->label('Kembalikan ke Akademik')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger')
                ->visible(fn (): bool => $user->can('issueSk', $this->getRecord()))
                ->form([
                    Forms\Components\Textarea::make('dekan_catatan')
                        ->label('Catatan pengembalian')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->modalHeading('Kembalikan ke Akademik')
                ->modalDescription('Permohonan akan dikembalikan ke akademik. Penolakan final hanya dilakukan oleh akademik.')
                ->action(function (array $data) use ($user): void {
                    $this->getRecord()->update([
                        'dekan_status' => 'ditolak',
                        'dekan_verified_by' => $user->id,
                        'dekan_verified_at' => now(),
                        'dekan_catatan' => $data['dekan_catatan'],
                        'status' => StatusPermohonan::DikembalikanAkademik,
                    ]);
                    $this->refreshRecord();

                    Notification::make()
                        ->title('Dikembalikan ke akademik oleh Dekan')
                        ->warning()
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->label('Hapus permohonan')
                ->modalHeading('Hapus permohonan SK Penguji')
                ->modalDescription(HapusPermohonanUi::deskripsiHapusPenguji())
                ->successNotificationTitle('Permohonan SK Penguji dihapus')
                ->successRedirectUrl(PermohonanPengujiResource::getUrl('index'))
                ->using(function (PermohonanPenguji $record): void {
                    app(HapusPermohonanService::class)->hapusPenguji($record);
                }),

            Actions\Action::make('hapusDataNim')
                ->label('Hapus seluruh data NIM')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->visible(fn (): bool => $user->can('hapusDataNim', $this->getRecord()))
                ->requiresConfirmation()
                ->modalHeading('Hapus seluruh data mahasiswa')
                ->modalDescription(fn (): string => HapusPermohonanUi::deskripsiHapusNim(
                    (string) $this->getRecord()->mahasiswa_nim,
                    $this->getRecord()->mahasiswa?->nama_lengkap,
                ))
                ->form(fn (): array => [
                    HapusPermohonanUi::fieldKonfirmasiNim((string) $this->getRecord()->mahasiswa_nim),
                ])
                ->action(function (): void {
                    $nim = (string) $this->getRecord()->mahasiswa_nim;
                    HapusPermohonanUi::hapusDataNim($nim);
                    $this->redirect(PermohonanPengujiResource::getUrl('index'));
                }),
        ];
    }

    protected function refreshRecord(): void
    {
        $this->record = $this->getRecord()->fresh([
            'mahasiswa',
            'permohonanPembimbing',
            'akademikVerifier',
            'kabagVerifier',
            'wadek1Verifier',
            'dekanVerifier',
        ]);
    }
}
