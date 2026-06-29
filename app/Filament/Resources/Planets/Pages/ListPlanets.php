<?php

namespace App\Filament\Resources\Planets\Pages;

use App\Filament\Resources\Planets\PlanetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlanets extends ListRecords
{
    protected static string $resource = PlanetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
