<?php

namespace App\Filament\Resources;

use App\Enums\ProgramStudi;
use App\Filament\Resources\IstilahProdiResource\Pages;
use App\Models\IstilahProdi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IstilahProdiResource extends Resource
{
    protected static ?string $model = IstilahProdi::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Istilah Prodi';

    protected static ?string $modelLabel = 'Istilah Prodi';

    protected static ?string $pluralModelLabel = 'Istilah Prodi';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('istilah')
                    ->label('Istilah / kata kunci')
                    ->helperText('Dipakai nanti untuk mencocokkan judul skripsi ke prodi. Contoh: murabahah, siyasah, hadhanah.')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Forms\Components\Select::make('program_studi')
                    ->label('Program studi')
                    ->options(ProgramStudi::options())
                    ->required()
                    ->native(false)
                    ->afterStateHydrated(function (Forms\Components\Select $component, mixed $state): void {
                        if ($state instanceof ProgramStudi) {
                            $component->state($state->value);
                        }
                    })
                    ->dehydrateStateUsing(function (mixed $state): string {
                        if ($state instanceof ProgramStudi) {
                            return $state->value;
                        }

                        return (string) $state;
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
                Tables\Columns\TextColumn::make('istilah')
                    ->label('Istilah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_studi')
                    ->label('Prodi')
                    ->formatStateUsing(fn (mixed $state): string => $state instanceof ProgramStudi ? $state->value : (string) ($state ?? '-'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('istilah')
            ->defaultPaginationPageOption(50)
            ->paginated([10, 25, 50, 100])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIstilahProdis::route('/'),
            'create' => Pages\CreateIstilahProdi::route('/create'),
            'edit' => Pages\EditIstilahProdi::route('/{record}/edit'),
        ];
    }
}
