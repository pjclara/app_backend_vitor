<?php

namespace App\Filament\Resources\Sentences\Pages;

use App\Filament\Resources\Sentences\SentenceResource;
use App\Services\SentenceProcessorService;
use Filament\Resources\Pages\CreateRecord;

class CreateSentence extends CreateRecord
{
    protected static string $resource = SentenceResource::class;

    /**
     * Após criar a frase, divide em palavras e sílabas automaticamente.
     */
    protected function afterCreate(): void
    {
        $processor = new SentenceProcessorService();
        $processor->process($this->record);
    }
}
