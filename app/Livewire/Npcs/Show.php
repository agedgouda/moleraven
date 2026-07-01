<?php

namespace App\Livewire\Npcs;

use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\Npc;
use App\Models\NpcOrganization;
use App\Models\NpcSkill;
use App\Models\Organization;
use App\Support\Mgt2;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('NPC')]
class Show extends Component
{
    public Npc $npc;

    /** @return EloquentCollection<int, NpcSkill> */
    #[Computed]
    public function skills(): EloquentCollection
    {
        return $this->npc->skills()->orderBy('name')->get();
    }

    /** @return Collection<int, array{model: Character|Organization, name: string, label: string, relation: string, notes: string|null, route: string|null}> */
    #[Computed]
    public function connections(): Collection
    {
        $characters = $this->npc->characterConnections()->with('character')->get()->map(fn (CharacterNpc $c) => [
            'model' => $c->character,
            'name' => $c->character->name,
            'label' => 'PC',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('pcs.show', $c->character),
        ]);

        $orgs = $this->npc->organizations()->with('organization')->get()->map(fn (NpcOrganization $c) => [
            'model' => $c->organization,
            'name' => $c->organization->name,
            'label' => 'Org',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('organizations.show', $c->organization),
        ]);

        return $characters->concat($orgs)->sortBy('name')->values();
    }

    /** @return array<int, string> */
    #[Computed]
    public function statOptions(): array
    {
        return Mgt2::statOptions();
    }

    public function render(): View
    {
        return view('livewire.npcs.show');
    }
}
