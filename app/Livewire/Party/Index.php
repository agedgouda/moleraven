<?php

namespace App\Livewire\Party;

use App\Models\Character;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Party'])]
#[Title('Party')]
class Index extends Component
{
    public function render(): View
    {
        $characters = Character::with(['user', 'lastKnownPlanet'])
            ->where('is_current', true)
            ->orderBy('name')
            ->get();

        return view('livewire.party.index', compact('characters'));
    }
}
