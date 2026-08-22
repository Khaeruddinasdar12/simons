<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStudi;
use App\Enums\StatusPermohonan;
use App\Filament\Resources\PermohonanPengujiResource\Pages;
use App\Filament\Support\HapusPermohonanUi;
use App\Jobs\FinalisasiSkPengujiJob;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PermohonanPenguji;
use App\Services\HapusPermohonanService;
use App\Services\SkPengujiGenerator;
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

class PermohonanPengujiResource extends Resource
{
    protected static ?string $model = PermohonanPenguji::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Permohonan Penguji';

    protected static ?string $modelLabel = 'Permohonan Penguji';

    protected static ?string $pluralModelLabel = 'Permohonan Penguji';

    protected static ?string $navigationGroup = 'Skripsi';

    protected static ?int $navigationSort = 2;

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

                Forms\Components\Section::make('Skripsi & Penguji')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Placeholder::make('sk_pembimbing_info')
                            ->label('SK Pembimbing')
                            ->content(fn (?PermohonanPenguji $record): string => $record?->permohonanPembimbing
                                ? trim(($record->permohonanPembimbing->nomor_sk ?: 'SK terbit').' — '.$record->permohonanPembimbing->pembimbing_1.' & '.$record->permohonanPembimbing->pembimbing_2)
                                : '-')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('judul_skripsi')
                            ->label('Judul Skripsi')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->rows(2)
                            ->helperText('Judul mengikuti judul skripsi terkini mahasiswa. Perubahan judul dilakukan dari halaman SK Pembimbing agar setiap versi tercatat beserta waktunya.'),
                        Forms\Components\Select::make('penguji_1')
                            ->label('Penguji 1')
                            ->options(fn (?PermohonanPenguji $record): array => Dosen::optionsForSelect(
                                $record?->penguji_1,
                                $record?->penguji_2,
                            ))
                            ->searchable()
                            ->required()
                            ->different('penguji_2')
                            ->rules([
                                fn (?PermohonanPenguji $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                    $pembimbing = array_filter([
                                        $record?->permohonanPembimbing?->pembimbing_1,
                                        $record?->permohonanPembimbing?->pembimbing_2,
                                    ]);
                                    if (is_string($value) && in_array($value, $pembimbing, true)) {
                                        $fail('Penguji 1 tidak boleh sama dengan dosen pembimbing pada SK Pembimbing.');
                                    }
                                },
                            ])
                            ->validationMessages([
                                'required' => 'Penguji 1 wajib dipilih.',
                                'different' => 'Penguji 1 dan Penguji 2 tidak boleh sama.',
                            ]),
                        Forms\Components\Select::make('penguji_2')
                            ->label('Penguji 2')
                            ->options(fn (?PermohonanPenguji $record): array => Dosen::optionsForSelect(
                                $record?->penguji_1,
                                $record?->penguji_2,
                            ))
                            ->searchable()
                            ->required()
                            ->different('penguji_1')
                            ->rules([
                                fn (?PermohonanPenguji $record) => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                    $pembimbing = array_filter([
                                        $record?->permohonanPembimbing?->pembimbing_1,
                                        $record?->permohonanPembimbing?->pembimbing_2,
                                    ]);
                                    if (is_string($value) && in_array($value, $pembimbing, true)) {
                                        $fail('Penguji 2 tidak boleh sama dengan dosen pembimbing pada SK Pembimbing.');
                                    }
                                },
                            ])
                            ->validationMessages([
                                'required' => 'Penguji 2 wajib dipilih.',
                                'different' => 'Penguji 1 dan Penguji 2 tidak boleh sama.',
                            ]),
                        Forms\Components\FileUpload::make('file_usul_penguji')
                            ->label('File Usulan Penguji dari Kaprodi')
                            ->helperText('Wajib diunggah. Format PDF / JPG / PNG.')
                            ->disk('public')
                            ->directory('usul-penguji')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->minFiles(1)
                            ->validationMessages([
                                'required' => 'File usulan penguji dari Kaprodi wajib diunggah.',
                                'min' => 'File usulan penguji dari Kaprodi wajib diunggah.',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Catatan Perizinan')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Placeholder::make('status_label')
                            ->label('Status')
                            ->content(fn (?PermohonanPenguji $record): string => $record?->status?->label() ?? '-'),
                        Forms\Components\Placeholder::make('akademik_catatan_view')
                            ->label('Catatan Akademik')
                            ->content(fn (?PermohonanPenguji $record): string => $record?->akademik_catatan ?: 'Belum ada catatan'),
                        Forms\Components\Placeholder::make('kabag_catatan_view')
                            ->label('Catatan Kabag')
                            ->content(fn (?PermohonanPenguji $record): string => trim(($record?->formatRoleStatus($record?->kabag_status) ?? 'Menunggu').' — '.($record?->kabag_catatan ?: 'Belum ada catatan'))),
                        Forms\Components\Placeholder::make('wadek1_catatan_view')
                            ->label('Catatan Wadek 1')
                            ->content(fn (?PermohonanPenguji $record): string => trim(($record?->formatRoleStatus($record?->wadek1_status) ?? 'Menunggu').' — '.($record?->wadek1_catatan ?: 'Belum ada catatan'))),
                        Forms\Components\Placeholder::make('dekan_catatan_view')
                            ->label('Catatan Dekan')
                            ->content(fn (?PermohonanPenguji $record): string => trim(($record?->formatRoleStatus($record?->dekan_status) ?? 'Menunggu').' — '.($record?->dekan_catatan ?: 'Belum ada catatan'))),
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
                    ->tooltip(fn (PermohonanPenguji $record): string => $record->judul_skripsi)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('penguji_1')
                    ->label('Penguji 1')
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('penguji_2')
                    ->label('Penguji 2')
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (StatusPermohonan $state): string => $state->label())
                    ->color(fn (StatusPermohonan $state): string => $state->color()),
                Tables\Columns\TextColumn::make('kabag_status')
                    ->label('Kabag')
                    ->badge()
                    ->formatStateUsing(function (string $state, PermohonanPenguji $record): string {
                        if ($record->status !== StatusPermohonan::DikirimPimpinan && $record->status !== StatusPermohonan::SkTerbit && $record->status !== StatusPermohonan::DikembalikanAkademik) {
                            return '-';
                        }

                        return match ($state) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Dikembalikan',
                            default => 'Menunggu',
                        };
                    })
                    ->color(fn (string $state, PermohonanPenguji $record): string => match (true) {
                        $record->status === StatusPermohonan::Diajukan => 'gray',
                        $state === 'disetujui' => 'success',
                        $state === 'ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('wadek1_status')
                    ->label('Wadek1')
                    ->badge()
                    ->formatStateUsing(function (string $state, PermohonanPenguji $record): string {
                        if ($record->status !== StatusPermohonan::DikirimPimpinan && $record->status !== StatusPermohonan::SkTerbit && $record->status !== StatusPermohonan::DikembalikanAkademik) {
                            return '-';
                        }

                        return match ($state) {
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Dikembalikan',
                            default => 'Menunggu',
                        };
                    })
                    ->color(fn (string $state, PermohonanPenguji $record): string => match (true) {
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
                    ->label(fn (PermohonanPenguji $record): string => filled($record->file_sk) ? 'Lihat File SK' : 'Preview SK')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->tooltip(fn (PermohonanPenguji $record): string => filled($record->file_sk)
                        ? 'Buka File SK terbit di tab baru'
                        : 'Buka preview di browser (tanpa generate PDF di server)')
                    ->url(fn (PermohonanPenguji $record): string => filled($record->file_sk)
                        ? route('sk.penguji.lihat', $record)
                        : route('sk.penguji.preview', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (PermohonanPenguji $record): bool => auth()->user()?->can('previewSk', $record) ?? false),
                Tables\Actions\Action::make('generateUlangSk')
                    ->label('Generate Ulang SK')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->visible(fn (PermohonanPenguji $record): bool => auth()->user()?->can('generateUlangSk', $record) ?? false)
                    ->fillForm(fn (PermohonanPenguji $record): array => self::generateUlangSkFillForm($record))
                    ->form(self::generateUlangSkFormSchema())
                    ->modalHeading('Generate Ulang SK Penguji')
                    ->modalDescription('Semua isian dapat diubah. Nomor SK dan tanggal penetapan tidak berubah. PDF SK akan dibuat ulang dari data ini.')
                    ->modalWidth('7xl')
                    ->modalSubmitActionLabel('Simpan & Generate Ulang SK')
                    ->action(function (PermohonanPenguji $record, array $data): void {
                        self::prosesGenerateUlangSk($record, $data);
                    }),
                Tables\Actions\EditAction::make()
                    ->visible(fn (PermohonanPenguji $record): bool => auth()->user()?->can('update', $record) ?? false),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus permohonan')
                    ->modalHeading('Hapus permohonan SK Penguji')
                    ->modalDescription(HapusPermohonanUi::deskripsiHapusPenguji())
                    ->successNotificationTitle('Permohonan SK Penguji dihapus')
                    ->using(function (PermohonanPenguji $record): void {
                        app(HapusPermohonanService::class)->hapusPenguji($record);
                    }),
                Tables\Actions\Action::make('hapusDataNim')
                    ->label('Hapus seluruh data NIM')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (PermohonanPenguji $record): bool => auth()->user()?->can('hapusDataNim', $record) ?? false)
                    ->requiresConfirmation()
                    ->modalHeading('Hapus seluruh data mahasiswa')
                    ->modalDescription(fn (PermohonanPenguji $record): string => HapusPermohonanUi::deskripsiHapusNim(
                        (string) $record->mahasiswa_nim,
                        $record->mahasiswa?->nama_lengkap,
                    ))
                    ->form(fn (PermohonanPenguji $record): array => [
                        HapusPermohonanUi::fieldKonfirmasiNim((string) $record->mahasiswa_nim),
                    ])
                    ->action(function (PermohonanPenguji $record): void {
                        HapusPermohonanUi::hapusDataNim((string) $record->mahasiswa_nim);
                    }),
                Tables\Actions\Action::make('setujuiKabag')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->modalHidden(true)
                    ->visible(fn (PermohonanPenguji $record): bool => (Auth::user()?->isKabag() ?? false)
                        && $record->status === StatusPermohonan::DikirimPimpinan
                        && $record->kabag_status === 'pending')
                    ->action(function (PermohonanPenguji $record, Tables\Actions\Action $action): void {
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
                    ->visible(fn (PermohonanPenguji $record): bool => (Auth::user()?->isWadek1() ?? false)
                        && $record->status === StatusPermohonan::DikirimPimpinan
                        && $record->wadek1_status === 'pending')
                    ->action(function (PermohonanPenguji $record, Tables\Actions\Action $action): void {
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
                    ->modalDescription('Permohonan SK Penguji yang dipilih akan dihapus permanen, termasuk berkas usul dan file SK.')
                    ->successNotificationTitle('Permohonan terpilih dihapus')
                    ->action(function (Collection $records): void {
                        $service = app(HapusPermohonanService::class);

                        foreach ($records as $record) {
                            if ($record instanceof PermohonanPenguji) {
                                $service->hapusPenguji($record);
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
                            if (! $record instanceof PermohonanPenguji) {
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
                            if (! $record instanceof PermohonanPenguji) {
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
            'index' => Pages\ListPermohonanPengujis::route('/'),
            'view' => Pages\ViewPermohonanPenguji::route('/{record}'),
            'edit' => Pages\EditPermohonanPenguji::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'mahasiswa.judulSkripsiAktif',
            'permohonanPembimbing',
            'akademikVerifier',
            'kabagVerifier',
            'wadek1Verifier',
            'dekanVerifier',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function generateUlangSkFillForm(PermohonanPenguji $record): array
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
            'penguji_1' => $record->penguji_1,
            'penguji_2' => $record->penguji_2,
            'file_usul_penguji' => $record->file_usul_penguji,
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
                        ->rule(function (Get $get, ?PermohonanPenguji $record): Unique {
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
                        ->maxLength(255),
                    Forms\Components\TextInput::make('mahasiswa.tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('mahasiswa.tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    Forms\Components\Textarea::make('mahasiswa.alamat_lengkap')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->columnSpanFull()
                        ->rows(3),
                    Forms\Components\TextInput::make('mahasiswa.no_hp')
                        ->label('No. HP')
                        ->required()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('mahasiswa.email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('mahasiswa.program_studi')
                        ->label('Program Studi')
                        ->options(ProgramStudi::options())
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('semester')
                        ->label('Semester')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(14),
                ]),
            Forms\Components\Section::make('Skripsi & Penguji')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('judul_skripsi')
                        ->label('Judul Skripsi')
                        ->required()
                        ->columnSpanFull()
                        ->rows(2),
                    Forms\Components\Select::make('penguji_1')
                        ->label('Penguji 1')
                        ->options(fn (Get $get, ?PermohonanPenguji $record): array => Dosen::optionsForSelect(
                            $get('penguji_1') ?? $record?->penguji_1,
                            $get('penguji_2') ?? $record?->penguji_2,
                        ))
                        ->searchable()
                        ->required()
                        ->different('penguji_2'),
                    Forms\Components\Select::make('penguji_2')
                        ->label('Penguji 2')
                        ->options(fn (Get $get, ?PermohonanPenguji $record): array => Dosen::optionsForSelect(
                            $get('penguji_1') ?? $record?->penguji_1,
                            $get('penguji_2') ?? $record?->penguji_2,
                        ))
                        ->searchable()
                        ->required()
                        ->different('penguji_1'),
                    Forms\Components\FileUpload::make('file_usul_penguji')
                        ->label('File Usulan Penguji dari Kaprodi')
                        ->disk('public')
                        ->directory('usul-penguji')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->downloadable()
                        ->openable()
                        ->required()
                        ->minFiles(1)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function prosesGenerateUlangSk(PermohonanPenguji $record, array $data): void
    {
        abort_unless(auth()->user()?->can('generateUlangSk', $record), 403);

        app(SkPengujiGenerator::class)->perbaruiData($record, $data);
        FinalisasiSkPengujiJob::dispatch($record->id, kirimEmail: false, forcePdf: true)->afterResponse();

        Notification::make()
            ->title('Data SK disimpan')
            ->body('PDF sedang dibuat ulang di latar belakang. Refresh halaman jika File SK belum berubah.')
            ->success()
            ->send();
    }
}
