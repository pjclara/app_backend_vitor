<?php

namespace App\Filament\Resources\Words\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('word'),
                TextEntry::make('audio_url'),
                TextEntry::make('difficulty')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
