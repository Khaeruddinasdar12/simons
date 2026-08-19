<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DosenResource\Pages;
use App\Models\Dosen;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DosenResource extends Resource
{
    protected static ?string $model = Dosen::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Data Dosen';

    protected static ?string $modelLabel = 'Dosen';

    protected static ?string $pluralModelLabel = 'Dosen';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap (dengan gelar)')
                    ->helperText('Contoh: Dr. Ahmad Fauzi, M.H. — nama ini tampil di SK.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nip')
                    ->label('NIP')
                    ->nullable()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->helperText('Dosen nonaktif tidak muncul di pilihan pembimbing dan penguji.')
                    ->default(true)
                    ->inline(false),
                Forms\Components\TextInput::make('kuota_pembimbing')
                    ->label('Kuota pembimbing')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(30)
                    ->placeholder('Tidak dibatasi')
                    ->helperText('Opsional. Dipakai nanti untuk menyeimbangkan beban rekomendasi.'),
                Forms\Components\TextInput::make('kuota_penguji')
                    ->label('Kuota penguji')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(30)
                    ->placeholder('Tidak dibatasi'),
                Forms\Components\Select::make('keahlians')
                    ->label('Keahlian / minat')
                    ->relationship('keahlians', 'nama')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->maxItems(5)
                    ->helperText('Isi 2–5 keahlian. Dosen dengan kurang dari 2 keahlian belum siap untuk rekomendasi AI.')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('catatan_minat')
                    ->label('Catatan minat (bebas)')
                    ->rows(2)
                    ->helperText('Opsional. Contoh topik yang sering dibimbing, untuk konteks AI nanti.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keahlians.nama')
                    ->label('Keahlian')
                    ->badge()
                    ->separator(',')
                    ->placeholder('Belum diisi')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('keahlians_count')
                    ->label('Jml keahlian')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kuota_pembimbing')
                    ->label('Kuota pemb.')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
                Tables\Filters\Filter::make('belum_siap_ai')
                    ->label('Belum siap rekomendasi AI')
                    ->query(fn (Builder $query): Builder => $query->has('keahlians', '<', 2)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('keahlians');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDosens::route('/'),
            'create' => Pages\CreateDosen::route('/create'),
            'edit' => Pages\EditDosen::route('/{record}/edit'),
        ];
    }
}
