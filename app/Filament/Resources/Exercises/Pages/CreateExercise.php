<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use App\Services\ExerciseProcessorService;
use Filament\Resources\Pages\CreateRecord;

class CreateExercise extends CreateRecord
{
    protected static string $resource = ExerciseResource::class;

    /**
     * Após criar o exercício, divide em palavras e sílabas automaticamente.
     */
    protected function afterCreate(): void
    {
        $processor = new ExerciseProcessorService();
        $processor->process($this->record);
    }
}
