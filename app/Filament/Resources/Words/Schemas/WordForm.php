<?php

namespace App\Filament\Resources\Words\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('word')
                    ->required(),
                Textarea::make('syllables')
                    ->columnSpanFull(),
                TextInput::make('audio_url'),
                TextInput::make('difficulty')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
