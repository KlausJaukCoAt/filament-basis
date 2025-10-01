<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                  Group::make([
                TextEntry::make('description')
                    ->label('Aktion'),

                TextEntry::make('event')
                    ->label('Typ')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('log_name')
                    ->label('Log-Kategorie'),

                TextEntry::make('causer.name')
                    ->label('Verursacher'),

                TextEntry::make('subject_type')
                    ->label('Zieltyp'),

                TextEntry::make('subject_id')
                    ->label('Ziel-ID'),

                TextEntry::make('created_at')
                    ->label('Zeitpunkt')
                    ->dateTime(),
            ])->columns(2),

            Group::make([
            KeyValueEntry::make('properties.attributes')
                ->label('Attributes')
                ->hidden(fn($record) => empty($record->properties['attributes'] ?? []))
                ->state(fn($record) => collect($record->properties['attributes'] ?? [])
                ->forget(['password', 'remember_token'])
                ->toArray()),
            KeyValueEntry::make('properties.old')
                ->label('Old Value')
                ->hidden(fn($record) => empty($record->properties['old'] ?? []))
                ->state(fn($record) => collect($record->properties['old'] ?? [])
                ->forget(['password', 'remember_token'])
                ->toArray()),
            KeyValueEntry::make('properties.new')
                ->label('New Value')
                ->hidden(fn($record) => empty($record->properties['new'] ?? []))
                ->state(fn($record) => collect($record->properties['new'] ?? [])
                ->forget(['password', 'remember_token'])
                ->toArray())
            ])->columns(2)->columnSpanFull(),
            ]);
    }
}
