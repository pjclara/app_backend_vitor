<?php

namespace App\Filament\Resources\Sentences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class SentenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sentence')
                    ->label('Frase')
                    ->columnSpanFull(),
                TextEntry::make('difficulty')
                    ->label('Dificuldade')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Fácil',
                        2 => 'Médio',
                        3 => 'Difícil',
                        default => (string) $state,
                    })
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1 => 'success',
                        2 => 'warning',
                        3 => 'danger',
                        default => 'gray',
                    }),
                RepeatableEntry::make('sentenceWords')
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
