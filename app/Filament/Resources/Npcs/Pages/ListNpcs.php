<?php

namespace App\Filament\Resources\Npcs\Pages;

use App\Filament\Resources\Npcs\NpcResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNpcs extends ListRecords
{
    protected static string $resource = NpcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
