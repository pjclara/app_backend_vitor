<?php

namespace App\Filament\Resources\Exercises\Schemas;

use App\Enums\DictationDifficulty;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class ExerciseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sentence')
                    ->label('Exercício')
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->label('Conteúdo')
                    ->columnSpanFull()
                    ->visible(fn ($state) => filled($state)),
                TextEntry::make('number')
                    ->label('Número'),
                TextEntry::make('difficulty')
                    ->label('Dificuldade')
                    ->formatStateUsing(fn ($state): string => $state instanceof DictationDifficulty ? $state->label() : (string) $state)
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        DictationDifficulty::EASY => 'success',
                        DictationDifficulty::MEDIUM => 'warning',
                        DictationDifficulty::HARD => 'danger',
                        default => 'gray',
                    }),
                RepeatableEntry::make('exerciseWords')
                    ->label('Palavras e Sílabas')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('word_order')
                            ->label('Ordem'),
                        TextEntry::make('word.word')
                            ->label('Palavra'),
                        TextEntry::make('word.syllables')
                            ->label('Sílabas'),
                    ])
                    ->columns(3),
                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }
}
