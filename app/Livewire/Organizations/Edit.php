<?php

namespace App\Livewire\Organizations;

use App\Enums\NpcRelationshipType;
use App\Enums\OrganizationRelationshipType;
use App\Livewire\Concerns\HasConnectionFilters;
use App\Models\Character;
use App\Models\CharacterOrganization;
use App\Models\Npc;
use App\Models\NpcOrganization;
use App\Models\Organization;
use App\Models\OrganizationOrganization;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Edit Organization'])]
#[Title('Edit Organization')]
class Edit extends Component
{
    use HasConnectionFilters, WithFileUploads;

    private const TYPES = [
        'Corporation', 'Government', 'Military', 'Criminal', 'Religious',
        'Scout Service', 'Mercenary', 'Trade Guild', 'Noble House', 'Other',
    ];

    public Organization $organization;

    public string $name = '';

    public ?string $type = '';

    public ?int $baseOfOperationsPlanetId = null;

    public ?string $notes = '';

    // Connection modal state
    public string $connectionModalType = 'character';

    public ?int $editingConnectionId = null;

    public ?int $connectionModalCharacterId = null;

    public string $connectionModalCharacterRelType = 'member';

    public ?int $connectionModalNpcId = null;

    public string $connectionModalNpcRelType = 'member';

    public ?int $connectionModalOrgId = null;

    public string $connectionModalOrgRelType = 'ally';

    public string $connectionModalNotes = '';

    public $imageUpload;

    public function mount(Organization $organization): void
    {
        $this->organization = $organization;
        $this->name = $organization->name;
        $this->type = $organization->type ?? '';
        $this->baseOfOperationsPlanetId = $organization->base_of_operations_planet_id;
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
            'base_of_operations_planet_id' => $this->baseOfOperationsPlanetId,
            'notes' => $this->notes ?: null,
        ]);

        Flux::toast('Organization saved.');
    }

    // ---- Image ----

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);

        if ($this->organization->image_path) {
            Storage::disk('public')->delete($this->organization->image_path);
        }

        $path = $this->imageUpload->store('organizations', 'public');
        $this->organization->update(['image_path' => $path]);
        $this->imageUpload = null;
        Flux::toast('Image uploaded.');
    }

    public function deleteImage(): void
    {
        if ($this->organization->image_path) {
            Storage::disk('public')->delete($this->organization->image_path);
            $this->organization->update(['image_path' => null]);
        }
    }

    // ---- Connections ----

    #[Computed]
    public function characterConnections(): Collection
    {
        return $this->organization->characterMemberships()->with('character')->get();
    }

    #[Computed]
    public function npcConnections(): Collection
    {
        return $this->organization->npcMemberships()->with('npc')->get();
    }

    #[Computed]
    public function orgConnections(): Collection
    {
        $outgoing = $this->organization->orgLinks()->with('relatedOrganization')->get()
            ->map(fn ($link) => ['id' => $link->id, 'direction' => 'out', 'org' => $link->relatedOrganization, 'relationship_type' => $link->relationship_type, 'notes' => $link->notes]);

        $incoming = $this->organization->orgLinkedBy()->with('organization')->get()
            ->map(fn ($link) => ['id' => $link->id, 'direction' => 'in', 'org' => $link->organization, 'relationship_type' => $link->relationship_type, 'notes' => $link->notes]);

        return $outgoing->concat($incoming)->sortBy(fn ($item) => $item['org']->name)->values();
    }

    #[Computed]
    public function allConnections(): Collection
    {
        $characters = $this->characterConnections->map(fn ($c) => [
            'id' => $c->id, 'type' => 'character', 'model' => $c->character,
            'name' => $c->character->name, 'label' => 'PC',
            'route' => route('pcs.edit', $c->character),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        $npcs = $this->npcConnections->map(fn ($c) => [
            'id' => $c->id, 'type' => 'npc', 'model' => $c->npc,
            'name' => $c->npc->name, 'label' => 'NPC',
            'route' => route('npcs.edit', $c->npc),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        $orgs = $this->orgConnections->map(fn ($c) => [
            'id' => $c['id'], 'type' => 'org', 'model' => $c['org'],
            'name' => $c['org']->name, 'label' => 'Org',
            'route' => route('organizations.edit', $c['org']),
            'relationshipType' => $c['relationship_type'], 'notes' => $c['notes'],
        ]);

        return $this->applyConnectionFilters($characters->concat($npcs)->concat($orgs));
    }

    public function openConnectionModal(?int $id = null, string $type = 'character'): void
    {
        $this->connectionModalType = $type;
        $this->editingConnectionId = $id;
        $this->connectionModalNotes = '';

        if ($id && $type === 'character') {
            $conn = CharacterOrganization::find($id);
            $this->connectionModalCharacterId = $conn->character_id;
            $this->connectionModalCharacterRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } elseif ($id && $type === 'npc') {
            $conn = NpcOrganization::find($id);
            $this->connectionModalNpcId = $conn->npc_id;
            $this->connectionModalNpcRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } elseif ($id && $type === 'org') {
            $conn = OrganizationOrganization::find($id);
            $this->connectionModalOrgId = $conn->related_organization_id;
            $this->connectionModalOrgRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } else {
            $this->connectionModalCharacterId = null;
            $this->connectionModalCharacterRelType = 'member';
            $this->connectionModalNpcId = null;
            $this->connectionModalNpcRelType = 'member';
            $this->connectionModalOrgId = null;
            $this->connectionModalOrgRelType = 'ally';
        }

        $this->modal('org-connection-modal')->show();
    }

    public function saveConnection(): void
    {
        if ($this->connectionModalType === 'character') {
            $this->validate(
                ['connectionModalCharacterId' => 'required|integer|exists:characters,id'],
                ['connectionModalCharacterId.required' => 'You must select a character.'],
            );

            $data = [
                'character_id' => $this->connectionModalCharacterId,
                'relationship_type' => $this->connectionModalCharacterRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                CharacterOrganization::find($this->editingConnectionId)->update($data);
            } else {
                $this->organization->characterMemberships()->create($data);
            }

            unset($this->characterConnections, $this->allConnections);
        } elseif ($this->connectionModalType === 'npc') {
            $this->validate(
                ['connectionModalNpcId' => 'required|integer|exists:npcs,id'],
                ['connectionModalNpcId.required' => 'You must select an NPC.'],
            );

            $data = [
                'npc_id' => $this->connectionModalNpcId,
                'relationship_type' => $this->connectionModalNpcRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                NpcOrganization::find($this->editingConnectionId)->update($data);
            } else {
                $this->organization->npcMemberships()->create($data);
            }

            unset($this->npcConnections, $this->allConnections);
        } else {
            $this->validate(
                ['connectionModalOrgId' => 'required|integer|exists:organizations,id'],
                ['connectionModalOrgId.required' => 'You must select an organization.'],
            );

            $data = [
                'related_organization_id' => $this->connectionModalOrgId,
                'relationship_type' => $this->connectionModalOrgRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                OrganizationOrganization::find($this->editingConnectionId)->update($data);
            } else {
                $this->organization->orgLinks()->create($data);
            }

            unset($this->orgConnections, $this->allConnections);
        }

        $this->modal('org-connection-modal')->close();
        Flux::toast('Connection saved.');
    }

    public function deleteConnection(int $id, string $type): void
    {
        if ($type === 'character') {
            CharacterOrganization::findOrFail($id)->delete();
            unset($this->characterConnections, $this->allConnections);
        } elseif ($type === 'npc') {
            NpcOrganization::findOrFail($id)->delete();
            unset($this->npcConnections, $this->allConnections);
        } else {
            OrganizationOrganization::findOrFail($id)->delete();
            unset($this->orgConnections, $this->allConnections);
        }
    }

    public function render(): View
    {
        $allCharacters = Character::orderBy('name')->get(['id', 'name']);
        $allNpcs = Npc::orderBy('name')->get(['id', 'name']);
        $allOrgs = Organization::where('id', '!=', $this->organization->id)->orderBy('name')->get(['id', 'name']);

        return view('livewire.organizations.edit', [
            'types' => self::TYPES,
            'orgRelationshipTypes' => OrganizationRelationshipType::cases(),
            'npcRelationshipTypes' => NpcRelationshipType::cases(),
            'allCharacters' => $allCharacters,
            'allNpcs' => $allNpcs,
            'allOrgs' => $allOrgs,
        ]);
    }
}
