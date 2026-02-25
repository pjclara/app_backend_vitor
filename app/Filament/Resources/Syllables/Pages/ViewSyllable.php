<?php

namespace App\Filament\Resources\Syllables\Pages;

use App\Filament\Resources\Syllables\SyllableResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSyllable extends ViewRecord
{
    protected static string $resource = SyllableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
