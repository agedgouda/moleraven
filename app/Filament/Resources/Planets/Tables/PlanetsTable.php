<?php

namespace App\Filament\Resources\Planets\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlanetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sector')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hex')
                    ->label('Hex')
                    ->fontFamily('mono'),

                TextColumn::make('notes')
                    ->limit(60),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
