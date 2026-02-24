<?php

namespace App\Filament\Resources\Sentences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SentenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('sentence')
                    ->label('Frase')
                    ->required()
                    ->rows(3)
                    ->helperText('Escreva a frase. As palavras e sílabas serão geradas automaticamente.')
                    ->columnSpanFull(),
                Select::make('difficulty')
                    ->label('Dificuldade')
                    ->options([
                        1 => 'Fácil',
                        2 => 'Médio',
                        3 => 'Difícil',
                    ])
                    ->default(1)
                    ->required(),
            ]);
    }
}
