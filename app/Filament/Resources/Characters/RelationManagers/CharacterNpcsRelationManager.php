<?php

namespace App\Filament\Resources\Characters\RelationManagers;

use App\Enums\NpcRelationshipType;
use App\Models\Npc;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CharacterNpcsRelationManager extends RelationManager
{
    protected static string $relationship = 'characterNpcs';

    protected static ?string $title = 'Contacts & Allies';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('npc_id')
                ->label('NPC')
                ->options(Npc::orderBy('name')->pluck('name', 'id'))
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
                TextColumn::make('npc.name')
                    ->label('NPC')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('npc.homeworld')
                    ->label('Homeworld'),

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
