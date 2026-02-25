<?php

namespace App\Filament\Resources\Syllables\Pages;

use App\Filament\Resources\Syllables\SyllableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSyllables extends ListRecords
{
    protected static string $resource = SyllableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
