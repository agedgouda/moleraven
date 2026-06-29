<?php

namespace App\Livewire\Party;

use App\Models\Character;
use App\Models\Party;
use App\Models\Planet;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Party'])]
#[Title('Party')]
class Index extends Component
{
    public Party $party;

    public ?int $currentPlanetId = null;

    public ?string $notes = null;

    public function mount(): void
    {
        $this->party = Party::instance();
        $this->currentPlanetId = $this->party->current_planet_id;
        $this->notes = $this->party->notes;
    }

    public function saveNotes(): void
    {
        $this->party->update(['notes' => $this->notes ?: null]);
        Flux::toast('Notes saved.');
    }

    public function updatedCurrentPlanetId(): void
    {
        $this->party->update(['current_planet_id' => $this->currentPlanetId]);

        Character::where('status', 'active')
            ->update(['last_known_planet_id' => $this->currentPlanetId]);

        Flux::toast('Party saved.');
    }

    public function render(): View
    {
        $characters = Character::with(['user'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $planets = Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id');

        return view('livewire.party.index', compact('characters', 'planets'));
    }
}
