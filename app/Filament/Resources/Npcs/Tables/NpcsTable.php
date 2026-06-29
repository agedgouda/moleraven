<?php

namespace App\Filament\Resources\Npcs\Tables;

use App\Models\Npc;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NpcsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('upp')
                    ->label('UPP')
                    ->state(fn (Npc $record) => $record->uppString())
                    ->fontFamily('mono'),

                TextColumn::make('homeworld.display_label')
                    ->label('Homeworld'),

                TextColumn::make('age')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
