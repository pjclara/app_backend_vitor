<?php

namespace App\Filament\Resources\Exercises\Tables;

use App\Enums\DictationDifficulty;
use App\Services\AudioService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

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
                    ->formatStateUsing(fn($state): string => $state instanceof DictationDifficulty ? $state->label() : (string) $state)
                    ->badge()
                    ->color(fn($state): string => match ($state) {
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
                \Filament\Actions\Action::make('listen')
                    ->label('Ouvir')
                    ->icon('heroicon-o-speaker-wave')
                    ->color('info')
                    ->action(function ($record, $livewire) {
                        $audioUrl = $record->audio_url_1;
                        
                        // Se não há áudio, tenta gerar
                        if (empty($audioUrl)) {
                            try {
                                $audioUrl = AudioService::generateAndSave(
                                    $record->sentence,
                                    'pt-PT',
                                    'sentences',
                                    'exercise-' . $record->id
                                );
                                
                                if ($audioUrl) {
                                    $record->update(['audio_url_1' => $audioUrl]);
                                }
                                
                                // Gerar áudio para as palavras também
                                $words = $record->words()->get();
                                foreach ($words as $word) {
                                    if (empty($word->audio_url)) {
                                        try {
                                            $wordAudioUrl = AudioService::generateAndSave(
                                                $word->word,
                                                'pt-PT',
                                                'words'
                                            );
                                            
                                            if ($wordAudioUrl) {
                                                $word->update(['audio_url' => $wordAudioUrl]);
                                            }
                                        } catch (\Exception $e) {
                                            Log::warning('Erro ao gerar áudio da palavra ' . $word->word . ': ' . $e->getMessage());
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                Log::error('Erro ao gerar áudio: ' . $e->getMessage());
                            }
                        }
                        
                        // Reproduz o áudio se houver
                        if ($audioUrl) {
                            $audioPath = ltrim($audioUrl, '/');
                            $url = asset('storage/' . $audioPath);
                            $url = str_replace("'", "\\'", $url);
                            $sentence = str_replace("'", "\\'", $record->sentence);
                            $livewire->js("
                                const audio = new Audio('{$url}');
                                audio.onerror = function() {
                                    console.warn('Áudio não encontrado, usando síntese de voz');
                                    window.speechSynthesis.cancel();
                                    const utterance = new SpeechSynthesisUtterance('{$sentence}');
                                    utterance.lang = 'pt-PT';
                                    utterance.rate = 0.85;
                                    window.speechSynthesis.speak(utterance);
                                };
                                audio.play().catch(error => {
                                    console.warn('Erro ao reproduzir áudio, usando síntese de voz');
                                    window.speechSynthesis.cancel();
                                    const utterance = new SpeechSynthesisUtterance('{$sentence}');
                                    utterance.lang = 'pt-PT';
                                    utterance.rate = 0.85;
                                    window.speechSynthesis.speak(utterance);
                                });
                            ");
                        } else {
                            // Fallback para síntese de voz
                            $sentence = str_replace("'", "\\'", $record->sentence);
                            $livewire->js("
                                window.speechSynthesis.cancel();
                                const utterance = new SpeechSynthesisUtterance('{$sentence}');
                                utterance.lang = 'pt-PT';
                                utterance.rate = 0.85;
                                window.speechSynthesis.speak(utterance);
                            ");
                        }
                    }),
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
