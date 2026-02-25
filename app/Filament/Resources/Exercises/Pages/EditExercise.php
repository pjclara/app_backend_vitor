<?php

namespace App\Filament\Resources\Exercises\Pages;

use App\Filament\Resources\Exercises\ExerciseResource;
use App\Services\ExerciseProcessorService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExercise extends EditRecord
{
    protected static string $resource = ExerciseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Após guardar o exercício editado, reprocessa palavras e sílabas.
     */
    protected function afterSave(): void
    {
        $processor = new ExerciseProcessorService();
        $processor->process($this->record);
    }
}
