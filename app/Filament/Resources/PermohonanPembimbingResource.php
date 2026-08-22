<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Filament\Resources\PermohonanPembimbingResource\Pages;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PermohonanPembimbing;
use App\Models\User;
use App\Filament\Support\HapusPermohonanUi;
use App\Services\HapusPermohonanService;
use App\Services\JudulSkripsiService;
use App\Services\SkPembimbingGenerator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class PermohonanPembimbingResource extends Resource
{
    protected static ?string $model = PermohonanPembimbing::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Permohonan Pembimbing';

    protected static ?string $modelLabel = 'Permohonan Pembimbing';

    protected static ?string $pluralModelLabel = 'Permohonan Pembimbing';

    protected static ?string $navigationGroup = 'Skripsi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Mahasiswa')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Group::make()
                            ->relationship('mahasiswa')
                            ->columns(2)
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\TextInput::make('nim')
                                    ->label('NIM')
                                    ->required()
                                    ->maxLength(30)
                                    ->rule(function (?Mahasiswa $record): Unique {
                                        return (new Unique('mahasiswas', 'nim'))
                                            ->ignore($record?->getKey(), 'nim');
                                    })
                                    ->validationMessages([
                                        'required' => 'NIM wajib diisi.',
                                    ])
                                    ->helperText('Akademik dapat memperbaiki NIM. Perubahan berlaku untuk seluruh data mahasiswa ini.'),
                                Forms\Components\TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Nama lengkap wajib diisi.',
                                    ]),
                                Forms\Components\TextInput::make('tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Tempat lahir wajib diisi.',
                                    ]),
                                Forms\Components\DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->validationMessages([
                                        'required' => 'Tanggal lahir wajib diisi.',
                                    ]),
                                Forms\Components\Textarea::make('alamat_lengkap')
                                    ->label('Alamat Lengkap')
                                    ->required()
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->validationMessages([
                                        'required' => 'Alamat lengkap wajib diisi.',
                                    ]),
                                Forms\Components\TextInput::make('no_hp')
                                    ->label('No. HP')
                                    ->required()
                                    ->maxLength(20)
                                    ->validationMessages([
                                        'required' => 'Nomor HP wajib diisi.',
                                    ]),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Email wajib diisi.',
                                    ]),
                                Forms\Components\Select::make('program_studi')
                                    ->label('Program Studi')
                                    ->options(ProgramStudi::options())
                                    ->required()
                                    ->native(false)
                                    ->validationMessages([
                                        'required' => 'Program studi wajib dipilih.',
                                    ]),
                            ]),
                        Forms\Components\TextInput::make('semester')
                            ->label('Semester')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(14)
                            ->validationMessages([
                                'required' => 'Semester wajib diisi.',
                            ]),
                    ]),

                Forms\Components\Section::make('Skripsi & Pembimbing')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('judul_skripsi')
                            ->label('Judul Skripsi (pada SK Pembimbing)')
                            ->required()
                            ->columnSpanFull()
                            ->rows(2)
                            ->helperText('Judul yang tercantum di SK Pembimbing. Untuk judul permohonan berikutnya tanpa mengubah SK, gunakan tombol Ubah Judul.')
                            ->validationMessages([
                                'required' => 'Judul skripsi wajib diisi.',
                            ]),
                        Forms\Components\Select::make('pembimbing_1')
                            ->label('Pembimbing 1')
                            ->options(fn (?PermohonanPembimbing $record): array => Dosen::optionsForSelect(
                                $record?->pembimbing_1,
                                $record?->pembimbing_2,
                            ))
                            ->searchable()
                            ->required()
                            ->different('pembimbing_2')
                            ->validationMessages([
                                'required' => 'Pembimbing 1 wajib dipilih.',
                                'different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
                            ]),
                        Forms\Components\Select::make('pembimbing_2')
                            ->label('Pembimbing 2')
                            ->options(fn (?PermohonanPembimbing $record): array => Dosen::optionsForSelect(
                                $record?->pembimbing_1,
                                $record?->pembimbing_2,
                            ))
                            ->searchable()
                            ->required()
                            ->different('pembimbing_1')
                            ->validationMessages([
                                'required' => 'Pembimbing 2 wajib dipilih.',
                                'different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
                            ]),
                        Forms\Components\FileUpload::make('file_usul_pembimbing')
                            ->label('File Usul Pembimbing dari Prodi')
                            ->helperText('Wajib diunggah. Format PDF / JPG / PNG.')
                            ->disk('public')
                            ->directory('usul-pembimbing')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->minFiles(1)
                            ->validationMessages([
                                'required' => 'File usul pembimbing dari Prodi wajib diunggah.',
                                'min' => 'File usul pembimbing dari Prodi wajib diunggah.',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Catatan Perizinan')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Placeholder::make('status_label')
                            ->label('Status')
                            ->content(fn (?PermohonanPembimbing $record): string => $record?->status?->label() ?? '-'),
                        Forms\Components\Placeholder::make('akademik_catatan_view')
                            ->label('Catatan Akademik')
                            ->content(fn (?PermohonanPembimbing $record): string => $record?->akademik_catatan ?: 'Belum ada catatan'),
                        Forms\Components\Placeholder::make('kabag_catatan_view')
                            ->label('Catatan Kabag')
                            ->content(fn (?PermohonanPembimbing $record): string => trim(($record?->formatRoleStatus($record?->kabag_status) ?? 'Menunggu').' — '.($record?->kabag_catatan ?: 'Belum ada catatan'))),
                        Forms\Components\Placeholder::make('wadek1_catatan_view')
                            ->label('Catatan Wadek 1')
                            ->content(fn (?PermohonanPembimbing $record): string => trim(($record?->formatRoleStatus($record?->wadek1_status) ?? 'Menunggu').' — '.($record?->wadek1_catatan ?: 'Belum ada catatan'))),
                        Forms\Components\Placeholder::make('dekan_catatan_view')
                            ->label('Catatan Dekan')
                            ->content(fn (?PermohonanPembimbing $record): string => trim(($record?->formatRoleStatus($record?->dekan_status) ?? 'Menunggu').' — '.($record?->dekan_catatan ?: 'Belum ada catatan'))),
                    ])
                    ->visibleOn('edit')
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mahasiswa_nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mahasiswa.nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('mahasiswa.program_studi')
                    ->label('Prodi')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('judul_skripsi')
                    ->label('Judul')
                    ->limit(40)
                    ->state(fn (PermohonanPembimbing $record): string => $record->judulTerkini())
                    ->tooltip(fn (PermohonanPembimbing $record): string => $record->judulTerkini())
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusPermohonan $state): string => $state->label())
                    ->color(fn (StatusPermohonan $state): string => $state->color()),
                Tables\Columns\TextColumn::make('kabag_status')
                    ->label('Kabag')
                    ->badge()
                    ->formatStateUsing(function (string $state, PermohonanPembimbing $record): string {
                        if ($record->status !== StatusPermohonan::DikirimPimpinan && $record->status !== StatusPermohonan::SkTerbit && $record->status !== StatusPermohonan::DikembalikanAkademik) {
                            return '-';
                        }

                        return match ($state) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Dikembalikan',
                            default => 'Menunggu',
                        };
                    })
                    ->color(fn (string $state, PermohonanPembimbing $record): string => match (true) {
                        $record->status === StatusPermohonan::Diajukan => 'gray',
                        $state === 'disetujui' => 'success',
                        $state === 'ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('wadek1_status')
                    ->label('Wadek1')
                    ->badge()
                    ->formatStateUsing(function (string $state, PermohonanPembimbing $record): string {
                        if ($record->status !== StatusPermohonan::DikirimPimpinan && $record->status !== StatusPermohonan::SkTerbit && $record->status !== StatusPermohonan::DikembalikanAkademik) {
                            return '-';
                        }

                        return match ($state) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Dikembalikan',
                            default => 'Menunggu',
                        };
                    })
                    ->color(fn (string $state, PermohonanPembimbing $record): string => match (true) {
                        $record->status === StatusPermohonan::Diajukan => 'gray',
                        $state === 'disetujui' => 'success',
                        $state === 'ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(StatusPermohonan::options()),
                Tables\Filters\SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(ProgramStudi::options())
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            filled($data['value'] ?? null),
                            fn (Builder $q): Builder => $q->whereHas(
                                'mahasiswa',
                                fn (Builder $mq): Builder => $mq->where('program_studi', $data['value'])
                            )
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('previewSk')
                    ->label(fn (PermohonanPembimbing $record): string => filled($record->file_sk) ? 'Lihat File SK' : 'Preview SK')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->tooltip(fn (PermohonanPembimbing $record): string => filled($record->file_sk)
                        ? 'Buka File SK terbit di tab baru'
                        : 'Buka preview di browser (tanpa generate PDF di server)')
                    ->url(fn (PermohonanPembimbing $record): string => filled($record->file_sk)
                        ? route('sk.lihat', $record)
                        : route('sk.preview', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (PermohonanPembimbing $record): bool => auth()->user()?->can('previewSk', $record) ?? false),
                Tables\Actions\Action::make('ubahJudul')
                    ->label('Ubah Judul')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn (PermohonanPembimbing $record): bool => auth()->user()?->can('ubahJudul', $record) ?? false)
                    ->fillForm(fn (PermohonanPembimbing $record): array => [
                        'judul' => $record->judulTerkini(),
                    ])
                    ->form(self::ubahJudulFormSchema())
                    ->modalHeading('Ubah Judul Skripsi')
                    ->modalDescription('Judul lama tetap tersimpan sebagai riwayat. SK Pembimbing yang sudah terbit tidak digenerate ulang. Judul baru dipakai untuk permohonan SK Penguji atau undangan munaqasyah berikutnya.')
                    ->modalSubmitActionLabel('Simpan Judul')
                    ->action(function (PermohonanPembimbing $record, array $data): void {
                        $user = Auth::user();
                        if (! $user instanceof User) {
                            return;
                        }

                        self::simpanUbahJudul($record, $data, $user);
                    }),
                Tables\Actions\Action::make('generateUlangSk')
                    ->label('Generate Ulang SK')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn (PermohonanPembimbing $record): bool => auth()->user()?->can('generateUlangSk', $record) ?? false)
                    ->fillForm(fn (PermohonanPembimbing $record): array => self::generateUlangSkFillForm($record))
                    ->form(self::generateUlangSkFormSchema())
                    ->modalHeading('Generate Ulang SK Pembimbing')
                    ->modalDescription('Semua isian dapat diubah. Nomor SK dan tanggal penetapan tidak berubah. PDF SK akan dibuat ulang dari data ini.')
                    ->modalWidth('7xl')
                    ->modalSubmitActionLabel('Simpan & Generate Ulang SK')
                    ->action(function (PermohonanPembimbing $record, array $data): void {
                        self::prosesGenerateUlangSk($record, $data);
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn (PermohonanPembimbing $record): bool => auth()->user()?->can('update', $record) ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus permohonan')
                    ->modalHeading('Hapus permohonan SK Pembimbing')
                    ->modalDescription(fn (PermohonanPembimbing $record): string => HapusPermohonanUi::deskripsiHapusPembimbing($record))
                    ->successNotificationTitle('Permohonan SK Pembimbing dihapus')
                    ->using(function (PermohonanPembimbing $record): void {
                        app(HapusPermohonanService::class)->hapusPembimbing($record);
                    }),
                Tables\Actions\Action::make('hapusDataNim')
                    ->label('Hapus seluruh data NIM')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (PermohonanPembimbing $record): bool => auth()->user()?->can('hapusDataNim', $record) ?? false)
                    ->requiresConfirmation()
                    ->modalHeading('Hapus seluruh data mahasiswa')
                    ->modalDescription(fn (PermohonanPembimbing $record): string => HapusPermohonanUi::deskripsiHapusNim(
                        (string) $record->mahasiswa_nim,
                        $record->mahasiswa?->nama_lengkap,
                    ))
                    ->form(fn (PermohonanPembimbing $record): array => [
                        HapusPermohonanUi::fieldKonfirmasiNim((string) $record->mahasiswa_nim),
                    ])
                    ->action(function (PermohonanPembimbing $record): void {
                        HapusPermohonanUi::hapusDataNim((string) $record->mahasiswa_nim);
                    }),
                Tables\Actions\Action::make('setujuiKabag')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->modalHidden(true)
                    ->visible(fn (PermohonanPembimbing $record): bool => (Auth::user()?->isKabag() ?? false)
                        && $record->status === StatusPermohonan::DikirimPimpinan
                        && $record->kabag_status === 'pending')
                    ->action(function (PermohonanPembimbing $record, Tables\Actions\Action $action): void {
                        $user = Auth::user();
                        $record->update([
                            'kabag_status' => 'disetujui',
                            'kabag_verified_by' => $user->id,
                            'kabag_verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Disetujui oleh Kabag')
                            ->success()
                            ->send();

                        $action->redirect(static::getUrl('index'));
                    }),
                Tables\Actions\Action::make('setujuiWadek1')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->modalHidden(true)
                    ->visible(fn (PermohonanPembimbing $record): bool => (Auth::user()?->isWadek1() ?? false)
                        && $record->status === StatusPermohonan::DikirimPimpinan
                        && $record->wadek1_status === 'pending')
                    ->action(function (PermohonanPembimbing $record, Tables\Actions\Action $action): void {
                        $user = Auth::user();
                        $record->update([
                            'wadek1_status' => 'disetujui',
                            'wadek1_verified_by' => $user->id,
                            'wadek1_verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Disetujui oleh Wadek 1')
                            ->success()
                            ->send();

                        $action->redirect(static::getUrl('index'));
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus permohonan terpilih')
                    ->modalHeading('Hapus permohonan terpilih')
                    ->modalDescription('Permohonan SK Pembimbing yang dipilih akan dihapus permanen, termasuk berkas dan permohonan SK Penguji yang terhubung.')
                    ->successNotificationTitle('Permohonan terpilih dihapus')
                    ->action(function (Collection $records): void {
                        $service = app(HapusPermohonanService::class);

                        foreach ($records as $record) {
                            if ($record instanceof PermohonanPembimbing) {
                                $service->hapusPembimbing($record);
                            }
                        }
                    }),
                Tables\Actions\BulkAction::make('setujuiKabagMassal')
                    ->label('Setujui Kabag')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (): bool => Auth::user()?->isKabag() ?? false)
                    ->modalHidden(true)
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records, Tables\Actions\BulkAction $action): void {
                        $user = Auth::user();
                        $count = 0;

                        foreach ($records as $record) {
                            if (! $record instanceof PermohonanPembimbing) {
                                continue;
                            }

                            $record->refresh();

                            if (
                                $record->status !== StatusPermohonan::DikirimPimpinan
                                || $record->kabag_status !== 'pending'
                                || ! ($user?->isKabag() ?? false)
                            ) {
                                continue;
                            }

                            $record->update([
                                'kabag_status' => 'disetujui',
                                'kabag_verified_by' => $user->id,
                                'kabag_verified_at' => now(),
                            ]);
                            $count++;
                        }

                        if ($count > 0) {
                            Notification::make()
                                ->title("{$count} permohonan disetujui Kabag")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Tidak ada permohonan yang bisa disetujui')
                                ->body('Pastikan status utama "Dikirim ke Pimpinan" dan Kabag masih menunggu.')
                                ->warning()
                                ->send();
                        }

                        $action->redirect(static::getUrl('index'));
                    }),

                Tables\Actions\BulkAction::make('setujuiWadek1Massal')
                    ->label('Setujui Wadek 1')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (): bool => Auth::user()?->isWadek1() ?? false)
                    ->modalHidden(true)
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records, Tables\Actions\BulkAction $action): void {
                        $user = Auth::user();
                        $count = 0;

                        foreach ($records as $record) {
                            if (! $record instanceof PermohonanPembimbing) {
                                continue;
                            }

                            $record->refresh();

                            if (
                                $record->status !== StatusPermohonan::DikirimPimpinan
                                || $record->wadek1_status !== 'pending'
                                || ! ($user?->isWadek1() ?? false)
                            ) {
                                continue;
                            }

                            $record->update([
                                'wadek1_status' => 'disetujui',
                                'wadek1_verified_by' => $user->id,
                                'wadek1_verified_at' => now(),
                            ]);
                            $count++;
                        }

                        if ($count > 0) {
                            Notification::make()
                                ->title("{$count} permohonan disetujui Wadek 1")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Tidak ada permohonan yang bisa disetujui')
                                ->body('Pastikan status utama "Dikirim ke Pimpinan" dan Wadek 1 masih menunggu.')
                                ->warning()
                                ->send();
                        }

                        $action->redirect(static::getUrl('index'));
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanPembimbings::route('/'),
            'view' => Pages\ViewPermohonanPembimbing::route('/{record}'),
            'edit' => Pages\EditPermohonanPembimbing::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'mahasiswa.judulSkripsiAktif',
            'akademikVerifier',
            'kabagVerifier',
            'wadek1Verifier',
            'dekanVerifier',
        ]);
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function ubahJudulFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('judul')
                ->label('Judul skripsi untuk permohonan berikutnya')
                ->required()
                ->maxLength(500)
                ->rows(3)
                ->helperText('Tidak mengubah PDF SK Pembimbing yang sudah terbit. Dipakai saat pengajuan SK Penguji atau undangan munaqasyah.')
                ->validationMessages([
                    'required' => 'Judul skripsi wajib diisi.',
                    'max' => 'Judul skripsi maksimal 500 karakter.',
                ]),
            Forms\Components\Textarea::make('catatan')
                ->label('Catatan perubahan')
                ->helperText('Opsional. Contoh: perbaikan ejaan, perubahan fokus penelitian, atau sesuai usulan pembimbing.')
                ->rows(2)
                ->maxLength(1000),
        ];
    }

    /**
     * @param  array{judul: string, catatan?: string|null}  $data
     */
    public static function simpanUbahJudul(PermohonanPembimbing $record, array $data, User $user): void
    {
        app(JudulSkripsiService::class)->ubah(
            $record,
            (string) $data['judul'],
            $user,
            $data['catatan'] ?? null,
        );

        Notification::make()
            ->title('Judul skripsi diperbarui')
            ->body('Perubahan tercatat di riwayat. SK Pembimbing tidak digenerate ulang. Permohonan berikutnya memakai judul terbaru.')
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    public static function generateUlangSkFillForm(PermohonanPembimbing $record): array
    {
        $record->loadMissing('mahasiswa');
        $mahasiswa = $record->mahasiswa;

        return [
            'nomor_sk' => $record->nomor_sk,
            'tanggal_sk' => $record->tanggal_sk,
            'mahasiswa' => [
                'nim' => $mahasiswa?->nim,
                'nama_lengkap' => $mahasiswa?->nama_lengkap,
                'tempat_lahir' => $mahasiswa?->tempat_lahir,
                'tanggal_lahir' => $mahasiswa?->tanggal_lahir,
                'alamat_lengkap' => $mahasiswa?->alamat_lengkap,
                'no_hp' => $mahasiswa?->no_hp,
                'email' => $mahasiswa?->email,
                'program_studi' => $mahasiswa?->program_studi?->value,
            ],
            'semester' => $record->semester,
            'judul_skripsi' => $record->judul_skripsi,
            'pembimbing_1' => $record->pembimbing_1,
            'pembimbing_2' => $record->pembimbing_2,
            'file_usul_pembimbing' => $record->file_usul_pembimbing,
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function generateUlangSkFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Identitas SK')
                ->description('Nomor SK dan tanggal penetapan tidak diubah.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nomor_sk')
                        ->label('Nomor SK')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\DatePicker::make('tanggal_sk')
                        ->label('Tanggal Penetapan')
                        ->disabled()
                        ->dehydrated(false)
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                ]),
            Forms\Components\Section::make('Data Mahasiswa')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('mahasiswa.nim')
                        ->label('NIM')
                        ->required()
                        ->maxLength(30)
                        ->rule(function (Get $get, ?PermohonanPembimbing $record): Unique {
                            $nim = $record?->mahasiswa_nim ?? $get('mahasiswa.nim');

                            return (new Unique('mahasiswas', 'nim'))
                                ->ignore($nim, 'nim');
                        })
                        ->validationMessages([
                            'required' => 'NIM wajib diisi.',
                        ]),
                    Forms\Components\TextInput::make('mahasiswa.nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Nama lengkap wajib diisi.',
                        ]),
                    Forms\Components\TextInput::make('mahasiswa.tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Tempat lahir wajib diisi.',
                        ]),
                    Forms\Components\DatePicker::make('mahasiswa.tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->validationMessages([
                            'required' => 'Tanggal lahir wajib diisi.',
                        ]),
                    Forms\Components\Textarea::make('mahasiswa.alamat_lengkap')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->columnSpanFull()
                        ->rows(3)
                        ->validationMessages([
                            'required' => 'Alamat lengkap wajib diisi.',
                        ]),
                    Forms\Components\TextInput::make('mahasiswa.no_hp')
                        ->label('No. HP')
                        ->required()
                        ->maxLength(20)
                        ->validationMessages([
                            'required' => 'Nomor HP wajib diisi.',
                        ]),
                    Forms\Components\TextInput::make('mahasiswa.email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Email wajib diisi.',
                        ]),
                    Forms\Components\Select::make('mahasiswa.program_studi')
                        ->label('Program Studi')
                        ->options(ProgramStudi::options())
                        ->required()
                        ->native(false)
                        ->validationMessages([
                            'required' => 'Program studi wajib dipilih.',
                        ]),
                    Forms\Components\TextInput::make('semester')
                        ->label('Semester')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(14)
                        ->validationMessages([
                            'required' => 'Semester wajib diisi.',
                        ]),
                ]),
            Forms\Components\Section::make('Skripsi & Pembimbing')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('judul_skripsi')
                        ->label('Judul Skripsi')
                        ->required()
                        ->columnSpanFull()
                        ->rows(2)
                        ->helperText('Judul yang tercantum di SK Pembimbing. Untuk judul permohonan berikutnya tanpa mengubah SK, gunakan tombol Ubah Judul.')
                        ->validationMessages([
                            'required' => 'Judul skripsi wajib diisi.',
                        ]),
                    Forms\Components\Select::make('pembimbing_1')
                        ->label('Pembimbing 1')
                        ->options(fn (Get $get, ?PermohonanPembimbing $record): array => Dosen::optionsForSelect(
                            $get('pembimbing_1') ?? $record?->pembimbing_1,
                            $get('pembimbing_2') ?? $record?->pembimbing_2,
                        ))
                        ->searchable()
                        ->required()
                        ->different('pembimbing_2')
                        ->validationMessages([
                            'required' => 'Pembimbing 1 wajib dipilih.',
                            'different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
                        ]),
                    Forms\Components\Select::make('pembimbing_2')
                        ->label('Pembimbing 2')
                        ->options(fn (Get $get, ?PermohonanPembimbing $record): array => Dosen::optionsForSelect(
                            $get('pembimbing_1') ?? $record?->pembimbing_1,
                            $get('pembimbing_2') ?? $record?->pembimbing_2,
                        ))
                        ->searchable()
                        ->required()
                        ->different('pembimbing_1')
                        ->validationMessages([
                            'required' => 'Pembimbing 2 wajib dipilih.',
                            'different' => 'Pembimbing 1 dan Pembimbing 2 tidak boleh sama.',
                        ]),
                    Forms\Components\FileUpload::make('file_usul_pembimbing')
                        ->label('File Usul Pembimbing dari Prodi')
                        ->helperText('Wajib diunggah. Format PDF / JPG / PNG.')
                        ->disk('public')
                        ->directory('usul-pembimbing')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->downloadable()
                        ->openable()
                        ->required()
                        ->minFiles(1)
                        ->validationMessages([
                            'required' => 'File usul pembimbing dari Prodi wajib diunggah.',
                            'min' => 'File usul pembimbing dari Prodi wajib diunggah.',
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function prosesGenerateUlangSk(PermohonanPembimbing $record, array $data): void
    {
        abort_unless(auth()->user()?->can('generateUlangSk', $record), 403);

        app(SkPembimbingGenerator::class)->perbaruiDanGenerateUlang($record, $data);

        Notification::make()
            ->title('SK Pembimbing digenerate ulang')
            ->body('Data permohonan disimpan. Nomor SK dan tanggal penetapan tidak berubah. PDF SK telah dibuat ulang.')
            ->success()
            ->send();
    }
}
