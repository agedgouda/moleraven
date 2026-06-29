<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class Party extends Page
{
    protected string $view = 'filament.pages.party';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'The Party';

    protected static ?string $title = 'The Party';

    public function getCurrentCharacters(): Collection
    {
        return User::with(['characters' => fn ($q) => $q->where('is_current', true)])
            ->get()
            ->map(fn (User $user) => $user->characters->first())
            ->filter()
            ->values();
    }
}
