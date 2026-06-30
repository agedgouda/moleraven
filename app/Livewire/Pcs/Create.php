<?php

namespace App\Livewire\Pcs;

use App\Enums\CharacterStatus;
use App\Models\Character;
use App\Support\Mgt2;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New PC'])]
#[Title('New PC')]
class Create extends Component
{
    public string $name = '';

    public string $status = 'active';

    public bool $isCurrent = false;

    public ?int $age = 18;

    public int $credits = 0;

    public ?int $homeworldPlanetId = null;

    public ?int $lastKnownPlanetId = null;

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
            'status' => 'required|string',
            'age' => 'nullable|integer|min:1|max:150',
            'credits' => 'integer|min:0',
            'strength' => 'required|integer|min:0|max:15',
            'dexterity' => 'required|integer|min:0|max:15',
            'endurance' => 'required|integer|min:0|max:15',
            'intelligence' => 'required|integer|min:0|max:15',
            'education' => 'required|integer|min:0|max:15',
            'socialStanding' => 'required|integer|min:0|max:15',
        ]);

        $character = Character::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'status' => $this->status,
            'is_current' => $this->isCurrent,
            'age' => $this->age,
            'credits' => $this->credits,
            'homeworld_planet_id' => $this->homeworldPlanetId,
            'last_known_planet_id' => $this->lastKnownPlanetId,
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'endurance' => $this->endurance,
            'intelligence' => $this->intelligence,
            'education' => $this->education,
            'social_standing' => $this->socialStanding,
            'notes' => $this->notes ?: null,
        ]);

        $this->redirect(route('pcs.edit', $character), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.pcs.create', [
            'statOptions' => Mgt2::statOptions(),
            'statusOptions' => CharacterStatus::cases(),
        ]);
    }
}
