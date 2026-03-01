<?php

namespace App\Filament\Resources\Words\Tables;

use App\Services\AudioService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class WordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('word')
                    ->searchable(),
                ViewColumn::make('audio_url')
                    ->label('Áudio')
                    ->view('filament.columns.audio-player'),
                TextColumn::make('difficulty')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('listen')
                    ->label('Ouvir')
                    ->icon('heroicon-o-speaker-wave')
                    ->color('info')
                    ->action(function ($record, $livewire) {
                        $audioUrl = $record->audio_url;
                        
                        // Se não há áudio, tenta gerar
                        if (empty($audioUrl)) {
                            try {
                                $audioUrl = AudioService::generateAndSave(
                                    $record->word,
                                    'pt-PT',
                                    'words'
                                );
                                
                                if ($audioUrl) {
                                    $record->update(['audio_url' => $audioUrl]);
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
                            $word = str_replace("'", "\\'", $record->word);
                            $livewire->js("
                                const audio = new Audio('{$url}');
                                audio.onerror = function() {
                                    console.warn('Áudio não encontrado, usando síntese de voz');
                                    window.speechSynthesis.cancel();
                                    const utterance = new SpeechSynthesisUtterance('{$word}');
                                    utterance.lang = 'pt-PT';
                                    utterance.rate = 0.85;
                                    window.speechSynthesis.speak(utterance);
                                };
                                audio.play().catch(error => {
                                    console.warn('Erro ao reproduzir áudio, usando síntese de voz');
                                    window.speechSynthesis.cancel();
                                    const utterance = new SpeechSynthesisUtterance('{$word}');
                                    utterance.lang = 'pt-PT';
                                    utterance.rate = 0.85;
                                    window.speechSynthesis.speak(utterance);
                                });
                            ");
                        } else {
                            // Fallback para síntese de voz
                            $word = str_replace("'", "\\'", $record->word);
                            $livewire->js("
                                window.speechSynthesis.cancel();
                                const utterance = new SpeechSynthesisUtterance('{$word}');
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
            ]);
    }
}
