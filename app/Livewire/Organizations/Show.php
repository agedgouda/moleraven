<?php

namespace App\Livewire\Organizations;

use App\Models\Character;
use App\Models\CharacterOrganization;
use App\Models\Npc;
use App\Models\NpcOrganization;
use App\Models\Organization;
use App\Models\OrganizationOrganization;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Organization')]
class Show extends Component
{
    public Organization $organization;

    /** @return Collection<int, array{model: Character|Npc|Organization, name: string, label: string, relation: string, notes: string|null, route: string|null}> */
    #[Computed]
    public function connections(): Collection
    {
        $characters = $this->organization->characterMemberships()->with('character')->get()->map(fn (CharacterOrganization $c) => [
            'model' => $c->character,
            'name' => $c->character->name,
            'label' => 'PC',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('pcs.show', $c->character),
        ]);

        $npcs = $this->organization->npcMemberships()->with('npc')->get()->map(fn (NpcOrganization $c) => [
            'model' => $c->npc,
            'name' => $c->npc->name,
            'label' => 'NPC',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('npcs.show', $c->npc),
        ]);

        $outgoing = $this->organization->orgLinks()->with('relatedOrganization')->get()->map(fn (OrganizationOrganization $c) => [
            'model' => $c->relatedOrganization,
            'name' => $c->relatedOrganization->name,
            'label' => 'Org',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('organizations.show', $c->relatedOrganization),
        ]);

        $incoming = $this->organization->orgLinkedBy()->with('organization')->get()->map(fn (OrganizationOrganization $c) => [
            'model' => $c->organization,
            'name' => $c->organization->name,
            'label' => 'Org',
            'relation' => $c->relationship_type->label(),
            'notes' => $c->notes,
            'route' => route('organizations.show', $c->organization),
        ]);

        return $characters->concat($npcs)->concat($outgoing)->concat($incoming)->sortBy('name')->values();
    }

    public function render(): View
    {
        return view('livewire.organizations.show');
    }
}
