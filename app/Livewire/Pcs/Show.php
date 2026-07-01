<?php

namespace App\Livewire\Pcs;

use App\Models\CareerTerm;
use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\CharacterOrganization;
use App\Models\InventoryItem;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\Skill;
use App\Support\Mgt2;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('PC')]
class Show extends Component
{
    public Character $character;

    /** @return EloquentCollection<int, Skill> */
    #[Computed]
    public function skills(): EloquentCollection
    {
        return $this->character->skills()->orderBy('name')->get();
    }

    /** @return EloquentCollection<int, CareerTerm> */
    #[Computed]
    public function careerTerms(): EloquentCollection
    {
        return $this->character->careerTerms()->orderBy('term')->get();
    }

    /** @return EloquentCollection<int, InventoryItem> */
    #[Computed]
    public function inventoryItems(): EloquentCollection
    {
        return $this->character->inventoryItems()->orderBy('name')->get();
    }

    /** @return Collection<int, array{model: Npc|Organization, name: string, label: string, relation: string, notes: string|null, route: string}> */
    #[Computed]
    public function connections(): Collection
    {
        $npcs = $this->character->characterNpcs()->with('npc')->get()->map(fn (CharacterNpc $c) => [
            'model' => $c->npc,
            'name' => $c->npc->name,
            'label' => 'NPC',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('npcs.show', $c->npc),
        ]);

        $orgs = $this->character->organizations()->with('organization')->get()->map(fn (CharacterOrganization $c) => [
            'model' => $c->organization,
            'name' => $c->organization->name,
            'label' => 'Org',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('organizations.show', $c->organization),
        ]);

        return $npcs->concat($orgs)->sortBy('name')->values();
    }

    /** @return array<int, string> */
    #[Computed]
    public function statOptions(): array
    {
        return Mgt2::statOptions();
    }

    public function render(): View
    {
        return view('livewire.pcs.show');
    }
}
