<?php

namespace App\Filament\Resources\Exercises\Tables;

use App\Enums\DictationDifficulty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExercisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sentence')
                    ->label('Exercício')
                    ->limit(60)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('words_count')
                    ->label('Palavras')
                    ->counts('words')
                    ->sortable(),
                TextColumn::make('difficulty')
                    ->label('Dificuldade')
                    ->formatStateUsing(fn ($state): string => $state instanceof DictationDifficulty ? $state->label() : (string) $state)
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        DictationDifficulty::EASY => 'success',
                        DictationDifficulty::MEDIUM => 'warning',
                        DictationDifficulty::HARD => 'danger',
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
                    ->options(collect(DictationDifficulty::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray()),
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
