<?php

namespace App\Filament\Resources\Sentences\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SentenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('sentence')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('words_json')
                    ->columnSpanFull(),
                TextInput::make('difficulty')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
