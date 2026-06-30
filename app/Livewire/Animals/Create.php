<?php

namespace App\Livewire\Animals;

use App\Models\Animal;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New Animal'])]
#[Title('New Animal')]
class Create extends Component
{
    public string $name = '';

    #[Url]
    public ?int $parent = null;

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:255']);

        $animal = Animal::create([
            'name' => $this->name,
            'parent_animal_id' => $this->parent,
        ]);

        $this->redirect(route('animals.edit', $animal), navigate: true);
    }

    public function render(): View
    {
        $parentAnimal = $this->parent ? Animal::find($this->parent) : null;

        return view('livewire.animals.create', compact('parentAnimal'));
    }
}
