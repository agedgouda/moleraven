<?php

namespace App\Livewire\Animals;

use App\Models\Animal;
use App\Models\AnimalAttack;
use App\Models\AnimalSkill;
use App\Models\AnimalTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Animal')]
class Show extends Component
{
    public Animal $animal;

    /** @return Collection<int, AnimalSkill> */
    #[Computed]
    public function skills(): Collection
    {
        return $this->animal->skills()->orderBy('name')->get();
    }

    /** @return Collection<int, AnimalAttack> */
    #[Computed]
    public function attacks(): Collection
    {
        return $this->animal->attacks()->orderBy('name')->get();
    }

    /** @return Collection<int, AnimalTrait> */
    #[Computed]
    public function animalTraits(): Collection
    {
        return $this->animal->traits()->orderBy('name')->get();
    }

    /** @return Collection<int, Animal> */
    #[Computed]
    public function variants(): Collection
    {
        return $this->animal->variants()->orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.animals.show');
    }
}
