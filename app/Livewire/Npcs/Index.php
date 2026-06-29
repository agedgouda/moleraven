<?php

namespace App\Livewire\Npcs;

use App\Models\Npc;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'NPCs'])]
#[Title('NPCs')]
class Index extends Component
{
    public string $search = '';

    public function deleteNpc(int $id): void
    {
        Npc::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $npcs = Npc::query()
            ->with('homeworld')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('notes', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.npcs.index', compact('npcs'));
    }
}
