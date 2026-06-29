<?php

namespace App\Livewire\Planets;

use App\Models\Planet;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Planets'])]
#[Title('Planets')]
class Index extends Component
{
    public string $search = '';

    public function deletePlanet(int $id): void
    {
        Planet::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $planets = Planet::query()
            ->when($this->search, fn ($q) => $q->where('sector', 'like', "%{$this->search}%")
                ->orWhere('hex', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%"))
            ->orderBy('sector')
            ->orderBy('hex')
            ->get();

        return view('livewire.planets.index', compact('planets'));
    }
}
