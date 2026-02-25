<?php

namespace App\Filament\Resources\Exercises\Schemas;

use App\Enums\DictationDifficulty;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('sentence')
                    ->label('Exercício')
                    ->required()
                    ->rows(3)
                    ->helperText('Escreva o exercício. As palavras e sílabas serão geradas automaticamente.')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label('Conteúdo')
                    ->rows(4)
                    ->nullable()
                    ->columnSpanFull(),
                TextInput::make('number')
                    ->label('Número')
                    ->numeric()
                    ->nullable(),
                Select::make('difficulty')
                    ->label('Dificuldade')
                    ->options(collect(DictationDifficulty::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray())
                    ->default(DictationDifficulty::EASY->value)
                    ->required(),
            ]);
    }
}
