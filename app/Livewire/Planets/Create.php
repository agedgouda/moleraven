<?php

namespace App\Livewire\Planets;

use App\Models\Planet;
use App\Support\TravellerMap;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New Planet'])]
#[Title('New Planet')]
class Create extends Component
{
    public string $sector = '';

    public string $hex = '';

    public ?string $notes = '';

    #[Computed]
    public function hexOptions(): array
    {
        if (blank($this->sector)) {
            return [];
        }

        return TravellerMap::worldOptions($this->sector);
    }

    public function updatedSector(): void
    {
        $this->hex = '';
        unset($this->hexOptions);
    }

    public function save(): void
    {
        $this->validate([
            'sector' => 'required|string',
            'hex' => 'required|string',
        ]);

        $planet = Planet::create([
            'sector' => $this->sector,
            'hex' => $this->hex,
            'notes' => $this->notes,
        ]);

        $this->redirect(route('planets.edit', $planet), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.planets.create', [
            'sectors' => TravellerMap::sectors(),
        ]);
    }
}
