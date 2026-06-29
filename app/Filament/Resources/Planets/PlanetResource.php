<?php

namespace App\Filament\Resources\Planets;

use App\Filament\Resources\Planets\Pages\CreatePlanet;
use App\Filament\Resources\Planets\Pages\EditPlanet;
use App\Filament\Resources\Planets\Pages\ListPlanets;
use App\Filament\Resources\Planets\Schemas\PlanetForm;
use App\Filament\Resources\Planets\Tables\PlanetsTable;
use App\Models\Planet;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PlanetResource extends Resource
{
    protected static ?string $model = Planet::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Planets';

    public static function form(Schema $schema): Schema
    {
        return PlanetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlanetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlanets::route('/'),
            'create' => CreatePlanet::route('/create'),
            'edit' => EditPlanet::route('/{record}/edit'),
        ];
    }
}
