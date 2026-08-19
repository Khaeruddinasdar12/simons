<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStudi;
use App\Filament\Resources\KeahlianResource\Pages;
use App\Models\Keahlian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KeahlianResource extends Resource
{
    protected static ?string $model = Keahlian::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Keahlian Dosen';

    protected static ?string $modelLabel = 'Keahlian';

    protected static ?string $pluralModelLabel = 'Keahlian Dosen';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama keahlian')
                    ->helperText('Contoh: Perbankan Syariah, Siyasah Dusturiyah, Hukum Keluarga.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('program_studi')
                    ->label('Program studi utama')
                    ->options(ProgramStudi::options())
                    ->placeholder('Lintas prodi')
                    ->native(false)
                    ->nullable()
                    ->afterStateHydrated(function (Forms\Components\Select $component, mixed $state): void {
                        if ($state instanceof ProgramStudi) {
                            $component->state($state->value);
                        }
                    })
                    ->dehydrateStateUsing(function (mixed $state): ?string {
                        if ($state instanceof ProgramStudi) {
                            return $state->value;
                        }

                        return filled($state) ? (string) $state : null;
                    }),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Keahlian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_studi')
                    ->label('Prodi')
                    ->placeholder('Lintas prodi')
                    ->formatStateUsing(fn (mixed $state): string => $state instanceof ProgramStudi ? $state->value : (string) ($state ?? ''))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dosens_count')
                    ->label('Dosen')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('nama')
            ->filters([
                Tables\Filters\SelectFilter::make('program_studi')
                    ->label('Program Studi')
                    ->options(ProgramStudi::options()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status aktif')
                    ->boolean()
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
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
        return parent::getEloquentQuery()->withCount('dosens');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeahlians::route('/'),
            'create' => Pages\CreateKeahlian::route('/create'),
            'edit' => Pages\EditKeahlian::route('/{record}/edit'),
        ];
    }
}
