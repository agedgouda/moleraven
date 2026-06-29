<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Edit Organization'])]
#[Title('Edit Organization')]
class Edit extends Component
{
    private const TYPES = [
        'Corporation', 'Government', 'Military', 'Criminal', 'Religious',
        'Scout Service', 'Mercenary', 'Trade Guild', 'Noble House', 'Other',
    ];

    public Organization $organization;

    public string $name = '';

    public ?string $type = '';

    public ?string $baseOfOperations = '';

    public ?string $notes = '';

    public function mount(Organization $organization): void
    {
        $this->organization = $organization;
        $this->name = $organization->name;
        $this->type = $organization->type ?? '';
        $this->baseOfOperations = $organization->base_of_operations ?? '';
        $this->notes = $organization->notes ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $this->organization->update([
            'name' => $this->name,
            'type' => $this->type ?: null,
            'base_of_operations' => $this->baseOfOperations ?: null,
            'notes' => $this->notes ?: null,
        ]);

        Flux::toast('Organization saved.');
    }

    public function render(): View
    {
        return view('livewire.organizations.edit', [
            'types' => self::TYPES,
        ]);
    }
}
