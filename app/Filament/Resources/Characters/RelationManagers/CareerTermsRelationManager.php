<?php

namespace App\Filament\Resources\Characters\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareerTermsRelationManager extends RelationManager
{
    protected static string $relationship = 'careerTerms';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('career')
                ->options([
                    'Navy' => 'Navy',
                    'Marines' => 'Marines',
                    'Scout' => 'Scout',
                    'Army' => 'Army',
                    'Merchant' => 'Merchant',
                    'Agent' => 'Agent',
                    'Noble' => 'Noble',
                    'Rogue' => 'Rogue',
                    'Entertainer' => 'Entertainer',
                    'Scholar' => 'Scholar',
                    'Other' => 'Other',
                ])
                ->required()
                ->searchable(),

            TextInput::make('assignment')
                ->maxLength(255),

            TextInput::make('term')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->maxValue(10)
                ->default(1)
                ->required(),

            TextInput::make('rank')
                ->numeric()
                ->integer()
                ->minValue(0)
                ->maxValue(6)
                ->default(0)
                ->required(),

            TextInput::make('rank_title')
                ->maxLength(255),

            Textarea::make('notes')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('term')
                    ->label('Term')
                    ->sortable(),

                TextColumn::make('career'),

                TextColumn::make('assignment'),

                TextColumn::make('rank')
                    ->label('Rank'),

                TextColumn::make('rank_title')
                    ->label('Rank Title'),

                TextColumn::make('notes')
                    ->limit(50),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
