<?php

namespace App\Filament\Resources\Sentences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SentenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('difficulty')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
