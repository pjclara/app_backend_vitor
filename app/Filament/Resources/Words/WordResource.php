<?php

namespace App\Filament\Resources\Words;

use App\Filament\Resources\Words\Pages\CreateWord;
use App\Filament\Resources\Words\Pages\EditWord;
use App\Filament\Resources\Words\Pages\ListWords;
use App\Filament\Resources\Words\Pages\ViewWord;
use App\Filament\Resources\Words\Schemas\WordForm;
use App\Filament\Resources\Words\Schemas\WordInfolist;
use App\Filament\Resources\Words\Tables\WordsTable;
use App\Models\Word;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WordResource extends Resource
{
    protected static ?string $model = Word::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Palavras';

    protected static ?string $navigationLabel = 'Palavras';

    protected static ?string $modelLabel = 'Palavra';

    protected static ?string $pluralModelLabel = 'Palavras';

    protected static UnitEnum|string|null $navigationGroup = 'Gestão de Conteúdos';


    public static function form(Schema $schema): Schema
    {
        return WordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WordsTable::configure($table);
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
            'index' => ListWords::route('/'),
            'create' => CreateWord::route('/create'),
            'view' => ViewWord::route('/{record}'),
            'edit' => EditWord::route('/{record}/edit'),
        ];
    }
}
