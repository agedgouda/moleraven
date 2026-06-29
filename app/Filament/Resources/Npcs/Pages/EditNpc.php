<?php

namespace App\Filament\Resources\Npcs\Pages;

use App\Filament\Resources\Npcs\NpcResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNpc extends EditRecord
{
    protected static string $resource = NpcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
