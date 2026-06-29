<?php

namespace App\Filament\Resources\Npcs\RelationManagers;

use App\Enums\OrganizationRelationshipType;
use App\Models\Organization;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NpcOrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizations';

    protected static ?string $title = 'Organizations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('organization_id')
                ->label('Organization')
                ->options(Organization::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Select::make('relationship_type')
                ->options(collect(OrganizationRelationshipType::cases())->mapWithKeys(
                    fn (OrganizationRelationshipType $t) => [$t->value => $t->label()]
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
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('organization.type')
                    ->label('Type'),

                TextColumn::make('relationship_type')
                    ->label('Relationship')
                    ->badge()
                    ->color(fn (OrganizationRelationshipType $state) => $state->color()),

                TextColumn::make('notes')->limit(50),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
