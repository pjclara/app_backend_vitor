<?php

namespace App\Filament\Resources\Syllables\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SyllableForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('syllable')
                    ->required(),
                TextInput::make('audio_url'),
            ]);
    }
}
