<?php

namespace App\Filament\Resources\Characters\Pages;

use App\Filament\Resources\Characters\CharacterResource;
use App\Models\Character;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCharacter extends EditRecord
{
    protected static string $resource = CharacterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle_current')
                ->label(fn () => $this->record->is_current ? 'Current Character' : 'Set as Current')
                ->icon(fn () => $this->record->is_current ? 'heroicon-s-star' : 'heroicon-o-star')
                ->color(fn () => $this->record->is_current ? 'warning' : 'gray')
                ->action(function () {
                    /** @var Character $character */
                    $character = $this->record;
                    $character->update(['is_current' => ! $character->is_current]);
                }),

            DeleteAction::make(),
        ];
    }
}
