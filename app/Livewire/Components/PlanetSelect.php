<?php

namespace App\Livewire\Components;

use App\Models\Planet;
use App\Support\TravellerMap;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class PlanetSelect extends Component
{
    #[Modelable]
    public ?int $value = null;

    public string $selected = '';

    public bool $modalOpen = false;

    public string $newSector = '';

    public string $newHex = '';

    #[Computed]
    public function planets(): array
    {
        return Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id')->all();
    }

    #[Computed]
    public function hexOptions(): array
    {
        if (blank($this->newSector)) {
            return [];
        }

        return TravellerMap::worldOptions($this->newSector);
    }

    public function updatedNewSector(): void
    {
        $this->newHex = '';
        unset($this->hexOptions);
    }

    public function mount(): void
    {
        $this->selected = $this->value ? (string) $this->value : '';
    }

    public function updatedSelected(string $val): void
    {
        if ($val === 'new') {
            $this->selected = $this->value ? (string) $this->value : '';
            $this->newSector = '';
            $this->newHex = '';
            $this->modalOpen = true;
        } else {
            $this->value = $val !== '' ? (int) $val : null;
        }
    }

    public function createPlanet(): void
    {
        $this->validate([
            'newSector' => 'required|string',
            'newHex' => 'required|string',
        ], [
            'newSector.required' => 'Please select a sector.',
            'newHex.required' => 'Please select a world.',
        ]);

        $planet = Planet::create([
            'sector' => $this->newSector,
            'hex' => $this->newHex,
        ]);

        unset($this->planets);
        $this->value = $planet->id;
        $this->selected = (string) $planet->id;
        $this->modalOpen = false;
        Flux::toast('Planet added.');
    }

    public function render(): View
    {
        return view('livewire.components.planet-select', [
            'sectors' => TravellerMap::sectors(),
        ]);
    }
}
