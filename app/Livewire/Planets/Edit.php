<?php

namespace App\Livewire\Planets;

use App\Models\Planet;
use App\Support\TravellerMap;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Edit Planet'])]
#[Title('Edit Planet')]
class Edit extends Component
{
    public Planet $planet;

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

    #[Computed]
    public function worldData(): ?array
    {
        return TravellerMap::getWorldData($this->sector, $this->hex);
    }

    public function mount(Planet $planet): void
    {
        $this->planet = $planet;
        $this->sector = $planet->sector;
        $this->hex = $planet->hex;
        $this->notes = $planet->notes;
    }

    public function updatedSector(): void
    {
        $this->hex = '';
        unset($this->hexOptions);
        unset($this->worldData);
    }

    public function updatedHex(): void
    {
        unset($this->worldData);
    }

    public function save(): void
    {
        $this->validate([
            'sector' => 'required|string',
            'hex' => 'required|string',
        ]);

        $this->planet->update([
            'sector' => $this->sector,
            'hex' => $this->hex,
            'notes' => $this->notes,
        ]);

        $this->dispatch('flux-toast', message: 'Planet saved.');
    }

    public function render(): View
    {
        return view('livewire.planets.edit', [
            'sectors' => TravellerMap::sectors(),
        ]);
    }
}
