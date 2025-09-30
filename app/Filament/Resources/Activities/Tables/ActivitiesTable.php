<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([

        TextColumn::make('subject_type')
            ->label('Zieltyp')
            ->sortable(),
        TextColumn::make('subject_id')
            ->label('ZielID')
            ->searchable()
            ->sortable(),
        TextColumn::make('causer.name')
            ->label('Verursacher')
            ->searchable()
            ->sortable(),
        TextColumn::make('event')
            ->label('Typ')
            ->badge()
            ->colors([
                'created' => 'success',
                'updated' => 'warning',
                'deleted' => 'danger',
            ]),
        TextColumn::make('description')
            ->label('Aktion')
            ->searchable()
            ->limit(50),







        TextColumn::make('created_at')
            ->label('Zeitpunkt')
            ->dateTime()
            ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),                
            ])
            ->toolbarActions([
              //
            ]);
    }
}
