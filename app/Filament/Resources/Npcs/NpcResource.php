<?php

namespace App\Filament\Resources\Npcs;

use App\Filament\Resources\Npcs\Pages\CreateNpc;
use App\Filament\Resources\Npcs\Pages\EditNpc;
use App\Filament\Resources\Npcs\Pages\ListNpcs;
use App\Filament\Resources\Npcs\RelationManagers\CharacterConnectionsRelationManager;
use App\Filament\Resources\Npcs\RelationManagers\NpcOrganizationsRelationManager;
use App\Filament\Resources\Npcs\Schemas\NpcForm;
use App\Filament\Resources\Npcs\Tables\NpcsTable;
use App\Models\Npc;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NpcResource extends Resource
{
    protected static ?string $model = Npc::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'NPCs';

    public static function form(Schema $schema): Schema
    {
        return NpcForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NpcsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CharacterConnectionsRelationManager::class,
            NpcOrganizationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNpcs::route('/'),
            'create' => CreateNpc::route('/create'),
            'edit' => EditNpc::route('/{record}/edit'),
        ];
    }
}
