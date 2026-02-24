<?php

namespace App\Filament\Resources\Sentences\Pages;

use App\Filament\Resources\Sentences\SentenceResource;
use App\Services\SentenceProcessorService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSentence extends EditRecord
{
    protected static string $resource = SentenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Após guardar a frase editada, reprocessa palavras e sílabas.
     */
    protected function afterSave(): void
    {
        $processor = new SentenceProcessorService();
        $processor->process($this->record);
    }
}
