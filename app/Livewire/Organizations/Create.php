<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New Organization'])]
#[Title('New Organization')]
class Create extends Component
{
    public string $name = '';

    public ?string $type = '';

    public ?int $baseOfOperationsPlanetId = null;

    public ?string $notes = '';

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $org = Organization::create([
            'name' => $this->name,
            'type' => $this->type ?: null,
            'base_of_operations_planet_id' => $this->baseOfOperationsPlanetId,
            'notes' => $this->notes ?: null,
        ]);

        $this->redirect(route('organizations.edit', $org), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.organizations.create');
    }
}
