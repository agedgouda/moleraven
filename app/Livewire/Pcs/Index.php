<?php

namespace App\Livewire\Pcs;

use App\Enums\CharacterStatus;
use App\Models\Character;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'PCs'])]
#[Title('PCs')]
class Index extends Component
{
    public string $search = '';

    public function deleteCharacter(int $id): void
    {
        Character::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $characters = Character::query()
            ->with(['user', 'homeworld'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        $statuses = CharacterStatus::cases();

        return view('livewire.pcs.index', compact('characters', 'statuses'));
    }
}
