<?php

namespace App\Filament\Resources\Syllables\Pages;

use App\Filament\Resources\Syllables\SyllableResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSyllable extends EditRecord
{
    protected static string $resource = SyllableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
