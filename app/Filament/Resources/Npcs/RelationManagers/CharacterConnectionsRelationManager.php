<?php

namespace App\Filament\Resources\Npcs\RelationManagers;

use App\Enums\NpcRelationshipType;
use App\Models\Character;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CharacterConnectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'characterConnections';

    protected static ?string $title = 'Connected Characters';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('character_id')
                ->label('Character')
                ->options(
                    Character::with('user')
                        ->get()
                        ->mapWithKeys(fn (Character $c) => [$c->id => "{$c->name} ({$c->user->name})"])
                )
                ->searchable()
                ->required(),

            Select::make('relationship_type')
                ->options(collect(NpcRelationshipType::cases())->mapWithKeys(
                    fn (NpcRelationshipType $t) => [$t->value => $t->label()]
                ))
                ->required(),

            Textarea::make('notes')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('character.name')
                    ->label('Character')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('character.user.name')
                    ->label('Player'),

                TextColumn::make('relationship_type')
                    ->label('Relationship')
                    ->badge()
                    ->color(fn (NpcRelationshipType $state) => $state->color()),

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
