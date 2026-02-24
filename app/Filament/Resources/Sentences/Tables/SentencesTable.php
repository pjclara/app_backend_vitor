<?php

namespace App\Filament\Resources\Sentences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SentencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sentence')
                    ->label('Frase')
                    ->limit(60)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('words_count')
                    ->label('Palavras')
                    ->counts('words')
                    ->sortable(),
                TextColumn::make('difficulty')
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
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('difficulty')
                    ->label('Dificuldade')
                    ->options([
                        1 => 'Fácil',
                        2 => 'Médio',
                        3 => 'Difícil',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
