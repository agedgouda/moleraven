<?php

namespace App\Filament\Resources\Characters\Tables;

use App\Enums\CharacterStatus;
use App\Models\Character;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CharactersTable
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
                    ->state(fn (Character $record) => $record->uppString())
                    ->fontFamily('mono'),

                TextColumn::make('age')
                    ->sortable(),

                TextColumn::make('homeworld.display_label')
                    ->label('Homeworld'),

                TextColumn::make('credits')
                    ->prefix('Cr ')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CharacterStatus $state) => $state->color()),

                IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('')
                    ->trueColor('warning'),
            ])
            ->filters([])
            ->recordActions([
                Action::make('set_current')
                    ->label('Set as Current')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->hidden(fn (Character $record) => $record->is_current)
                    ->action(fn (Character $record) => $record->update(['is_current' => true])),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
