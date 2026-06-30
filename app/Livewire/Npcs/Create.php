<?php

namespace App\Livewire\Npcs;

use App\Models\Npc;
use App\Support\Mgt2;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New NPC'])]
#[Title('New NPC')]
class Create extends Component
{
    public string $name = '';

    public ?int $homeworldPlanetId = null;

    public ?int $lastKnownPlanetId = null;

    public ?int $age = null;

    public int $strength = 7;

    public int $dexterity = 7;

    public int $endurance = 7;

    public int $intelligence = 7;

    public int $education = 7;

    public int $socialStanding = 7;

    public ?string $notes = '';

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'strength' => 'required|integer|min:0|max:15',
            'dexterity' => 'required|integer|min:0|max:15',
            'endurance' => 'required|integer|min:0|max:15',
            'intelligence' => 'required|integer|min:0|max:15',
            'education' => 'required|integer|min:0|max:15',
            'socialStanding' => 'required|integer|min:0|max:15',
        ]);

        $npc = Npc::create([
            'name' => $this->name,
            'homeworld_planet_id' => $this->homeworldPlanetId,
            'last_known_planet_id' => $this->lastKnownPlanetId,
            'age' => $this->age,
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'endurance' => $this->endurance,
            'intelligence' => $this->intelligence,
            'education' => $this->education,
            'social_standing' => $this->socialStanding,
            'notes' => $this->notes ?: null,
        ]);

        $this->redirect(route('npcs.edit', $npc), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.npcs.create', [
            'statOptions' => Mgt2::statOptions(),
        ]);
    }
}
