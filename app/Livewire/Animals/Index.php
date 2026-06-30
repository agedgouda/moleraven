<?php

namespace App\Livewire\Animals;

use App\Models\Animal;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Animals'])]
#[Title('Animals')]
class Index extends Component
{
    public string $search = '';

    public function deleteAnimal(int $id): void
    {
        Animal::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $animals = Animal::query()
            ->with('nativePlanet')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.animals.index', compact('animals'));
    }
}
