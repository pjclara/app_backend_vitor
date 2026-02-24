<?php

namespace App\Filament\Resources\Sentences;

use App\Filament\Resources\Sentences\Pages\CreateSentence;
use App\Filament\Resources\Sentences\Pages\EditSentence;
use App\Filament\Resources\Sentences\Pages\ListSentences;
use App\Filament\Resources\Sentences\Pages\ViewSentence;
use App\Filament\Resources\Sentences\Schemas\SentenceForm;
use App\Filament\Resources\Sentences\Schemas\SentenceInfolist;
use App\Filament\Resources\Sentences\Tables\SentencesTable;
use App\Models\Sentence;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SentenceResource extends Resource
{
    protected static ?string $model = Sentence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Frases';

    public static function form(Schema $schema): Schema
    {
        return SentenceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SentenceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SentencesTable::configure($table);
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
            'index' => ListSentences::route('/'),
            'create' => CreateSentence::route('/create'),
            'view' => ViewSentence::route('/{record}'),
            'edit' => EditSentence::route('/{record}/edit'),
        ];
    }
}
