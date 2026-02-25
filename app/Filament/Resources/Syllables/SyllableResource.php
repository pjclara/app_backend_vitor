<?php

namespace App\Filament\Resources\Syllables;

use App\Filament\Resources\Syllables\Pages\CreateSyllable;
use App\Filament\Resources\Syllables\Pages\EditSyllable;
use App\Filament\Resources\Syllables\Pages\ListSyllables;
use App\Filament\Resources\Syllables\Pages\ViewSyllable;
use App\Filament\Resources\Syllables\Schemas\SyllableForm;
use App\Filament\Resources\Syllables\Schemas\SyllableInfolist;
use App\Filament\Resources\Syllables\Tables\SyllablesTable;
use App\Models\Syllable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SyllableResource extends Resource
{
    protected static ?string $model = Syllable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Silaba';

    protected static ?string $navigationLabel = 'Sílabas';

    protected static ?string $modelLabel = 'Sílabas';

    protected static ?string $pluralModelLabel = 'Sílabas';

    protected static UnitEnum|string|null $navigationGroup = 'Gestão de Conteúdos';

    public static function form(Schema $schema): Schema
    {
        return SyllableForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SyllableInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SyllablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSyllables::route('/'),
            'create' => CreateSyllable::route('/create'),
            'view' => ViewSyllable::route('/{record}'),
            'edit' => EditSyllable::route('/{record}/edit'),
        ];
    }
}
