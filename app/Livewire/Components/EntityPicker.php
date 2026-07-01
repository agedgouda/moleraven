<?php

namespace App\Livewire\Components;

use App\Models\Animal;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\Planet;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class EntityPicker extends Component
{
    #[Modelable]
    public array $value = [];

    public string $type = '';

    public string $label = '';

    public string $pendingId = '';

    public ?int $defaultId = null;

    public function mount(): void
    {
        if ($this->defaultId !== null && $this->pendingId === '') {
            $this->pendingId = (string) $this->defaultId;
        }
    }

    public function addEntity(): void
    {
        if ($this->pendingId === '' || in_array((int) $this->pendingId, $this->value, true)) {
            return;
        }

        $this->value[] = (int) $this->pendingId;
        $this->pendingId = '';
    }

    public function removeEntity(int $id): void
    {
        $this->value = array_values(array_filter($this->value, fn ($v) => $v !== $id));
    }

    private function allOptions(): Collection
    {
        return match ($this->type) {
            'npc' => Npc::orderBy('name')->get(['id', 'name']),
            'organization' => Organization::orderBy('name')->get(['id', 'name']),
            'animal' => Animal::orderBy('name')->get(['id', 'name']),
            'planet' => Planet::orderBy('sector')->orderBy('hex')->get(['id', 'sector', 'hex']),
            default => collect(),
        };
    }

    public function render(): View
    {
        $all = $this->allOptions();

        return view('livewire.components.entity-picker', [
            'availableOptions' => $all->whereNotIn('id', $this->value)->values(),
            'selectedEntities' => $all->whereIn('id', $this->value)->values(),
        ]);
    }
}
