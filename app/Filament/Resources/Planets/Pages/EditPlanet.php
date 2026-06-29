<?php

namespace App\Filament\Resources\Planets\Pages;

use App\Filament\Resources\Planets\PlanetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanet extends EditRecord
{
    protected static string $resource = PlanetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
