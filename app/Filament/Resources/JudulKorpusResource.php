<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStudi;
use App\Filament\Resources\JudulKorpusResource\Pages;
use App\Models\JudulKorpus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JudulKorpusResource extends Resource
{
    protected static ?string $model = JudulKorpus::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Korpus Judul';

    protected static ?string $modelLabel = 'Judul Korpus';

    protected static ?string $pluralModelLabel = 'Korpus Judul';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 13;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('judul_skripsi')
                    ->label('Judul')
                    ->content(fn (?JudulKorpus $record): string => $record?->judul_skripsi ?? '-'),
                Forms\Components\Placeholder::make('judul_normalized')
                    ->label('Judul ternormalisasi')
                    ->content(fn (?JudulKorpus $record): string => $record?->judul_normalized ?? '-'),
                Forms\Components\Placeholder::make('mahasiswa_nim')
                    ->label('NIM')
                    ->content(fn (?JudulKorpus $record): string => $record?->mahasiswa_nim ?? '-'),
                Forms\Components\Placeholder::make('program_studi')
                    ->label('Prodi')
                    ->content(fn (?JudulKorpus $record): string => $record?->program_studi?->value ?? '-'),
                Forms\Components\Toggle::make('ditandai_mirip')
                    ->label('Ditandai mirip / perlu tinjau')
                    ->helperText('Tandai manual jika judul ini terlalu dekat dengan judul lain. Dipakai nanti sebagai label latih.'),
                Forms\Components\Textarea::make('catatan_kurasi')
                    ->label('Catatan kurasi')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_sk')
                    ->label('Tanggal SK')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Asal')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'penguji' ? 'SK Penguji' : 'SK Pembimbing'),
                Tables\Columns\TextColumn::make('mahasiswa_nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('program_studi')
                    ->label('Prodi')
                    ->formatStateUsing(fn (mixed $state): string => $state instanceof ProgramStudi ? $state->value : (string) ($state ?? '-'))
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('judul_skripsi')
                    ->label('Judul')
                    ->searchable()
                    ->wrap()
                    ->limit(70)
                    ->tooltip(fn (JudulKorpus $record): string => $record->judul_skripsi),
                Tables\Columns\IconColumn::make('ditandai_mirip')
                    ->label('Mirip')
                    ->boolean(),
            ])
            ->defaultSort('tanggal_sk', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'pembimbing' => 'SK Pembimbing',
                        'penguji' => 'SK Penguji',
                    ]),
                Tables\Filters\SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(ProgramStudi::options()),
                Tables\Filters\TernaryFilter::make('ditandai_mirip')
                    ->label('Ditandai mirip')
                    ->boolean()
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak')
                    ->placeholder('Semua'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Kurasi'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('mahasiswa');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJudulKorpus::route('/'),
            'edit' => Pages\EditJudulKorpus::route('/{record}/edit'),
        ];
    }
}
