<?php

namespace App\Livewire\Pcs;

use App\Enums\CharacterStatus;
use App\Enums\NpcRelationshipType;
use App\Enums\OrganizationRelationshipType;
use App\Livewire\Concerns\HasConnectionFilters;
use App\Livewire\Concerns\HasSkillModal;
use App\Models\CareerTerm;
use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\CharacterOrganization;
use App\Models\InventoryItem;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\Planet;
use App\Support\Mgt2;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Edit PC'])]
#[Title('Edit PC')]
class Edit extends Component
{
    use HasConnectionFilters, HasSkillModal, WithFileUploads;

    public Character $character;

    public $imageUpload;

    // Core fields
    public string $name = '';

    public string $status = 'active';

    public bool $isCurrent = false;

    public ?int $age = null;

    public int $credits = 0;

    public ?int $homeworldPlanetId = null;

    public ?int $lastKnownPlanetId = null;

    public ?string $notes = '';

    // UPP stats
    public int $strength = 7;

    public int $dexterity = 7;

    public int $endurance = 7;

    public int $intelligence = 7;

    public int $education = 7;

    public int $socialStanding = 7;

    // --- Career term modal ---
    public ?int $editingCareerTermId = null;

    public string $careerModalCareer = '';

    public string $careerModalAssignment = '';

    public int $careerModalTerm = 1;

    public int $careerModalRank = 0;

    public string $careerModalRankTitle = '';

    public string $careerModalNotes = '';

    // --- Inventory item modal ---
    public ?int $editingInventoryId = null;

    public string $inventoryModalName = '';

    public int $inventoryModalQuantity = 1;

    public string $inventoryModalDescription = '';

    // --- Connection modal (NPC or Organization) ---
    public string $connectionModalType = 'npc';

    public ?int $editingConnectionId = null;

    public ?int $connectionModalNpcId = null;

    public string $connectionModalNpcRelType = 'contact';

    public ?int $connectionModalOrgId = null;

    public string $connectionModalOrgRelType = 'member';

    public string $connectionModalNotes = '';

    #[Computed]
    public function skills(): Collection
    {
        return $this->character->skills()->orderBy('name')->get();
    }

    #[Computed]
    public function careerTerms(): Collection
    {
        return $this->character->careerTerms()->orderBy('term')->get();
    }

    #[Computed]
    public function inventoryItems(): Collection
    {
        return $this->character->inventoryItems()->orderBy('name')->get();
    }

    #[Computed]
    public function allConnections(): Collection
    {
        $npc = $this->character->characterNpcs()->with('npc')->get()->map(fn ($c) => [
            'id' => $c->id, 'type' => 'npc', 'model' => $c->npc,
            'name' => $c->npc->name, 'label' => 'NPC',
            'route' => route('npcs.edit', $c->npc),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        $org = $this->character->organizations()->with('organization')->get()->map(fn ($c) => [
            'id' => $c->id, 'type' => 'org', 'model' => $c->organization,
            'name' => $c->organization->name, 'label' => 'Org',
            'route' => route('organizations.edit', $c->organization),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        return $this->applyConnectionFilters($npc->concat($org));
    }

    public function mount(Character $character): void
    {
        $this->character = $character;
        $this->name = $character->name;
        $this->status = $character->status->value;
        $this->isCurrent = $character->is_current;
        $this->age = $character->age;
        $this->credits = $character->credits ?? 0;
        $this->homeworldPlanetId = $character->homeworld_planet_id;
        $this->lastKnownPlanetId = $character->last_known_planet_id;
        $this->strength = $character->strength;
        $this->dexterity = $character->dexterity;
        $this->endurance = $character->endurance;
        $this->intelligence = $character->intelligence;
        $this->education = $character->education;
        $this->socialStanding = $character->social_standing;
        $this->notes = $character->notes ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'age' => 'nullable|integer|min:1|max:150',
            'credits' => 'integer|min:0',
            'strength' => 'required|integer|min:0|max:15',
            'dexterity' => 'required|integer|min:0|max:15',
            'endurance' => 'required|integer|min:0|max:15',
            'intelligence' => 'required|integer|min:0|max:15',
            'education' => 'required|integer|min:0|max:15',
            'socialStanding' => 'required|integer|min:0|max:15',
        ]);

        $this->character->update([
            'name' => $this->name,
            'status' => $this->status,
            'is_current' => $this->isCurrent,
            'age' => $this->age,
            'credits' => $this->credits,
            'homeworld_planet_id' => $this->homeworldPlanetId,
            'last_known_planet_id' => $this->lastKnownPlanetId,
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'endurance' => $this->endurance,
            'intelligence' => $this->intelligence,
            'education' => $this->education,
            'social_standing' => $this->socialStanding,
            'notes' => $this->notes ?: null,
        ]);

        Flux::toast('PC saved.');
    }

    protected function skillable(): Character
    {
        return $this->character;
    }

    // ---- Career Terms ----

    public function openCareerModal(?int $termId = null): void
    {
        if ($termId) {
            $term = CareerTerm::find($termId);
            $this->editingCareerTermId = $termId;
            $this->careerModalCareer = $term->career;
            $this->careerModalAssignment = $term->assignment ?? '';
            $this->careerModalTerm = $term->term ?? 1;
            $this->careerModalRank = $term->rank ?? 0;
            $this->careerModalRankTitle = $term->rank_title ?? '';
            $this->careerModalNotes = $term->notes ?? '';
        } else {
            $this->editingCareerTermId = null;
            $this->careerModalCareer = '';
            $this->careerModalAssignment = '';
            $this->careerModalTerm = ($this->careerTerms->count() + 1);
            $this->careerModalRank = 0;
            $this->careerModalRankTitle = '';
            $this->careerModalNotes = '';
        }

        $this->modal('career-modal')->show();
    }

    public function saveCareerTerm(): void
    {
        $this->validate([
            'careerModalCareer' => 'required|string|max:255',
            'careerModalTerm' => 'required|integer|min:1',
            'careerModalRank' => 'required|integer|min:0|max:6',
        ]);

        $data = [
            'career' => $this->careerModalCareer,
            'assignment' => $this->careerModalAssignment ?: null,
            'term' => $this->careerModalTerm,
            'rank' => $this->careerModalRank,
            'rank_title' => $this->careerModalRankTitle ?: null,
            'notes' => $this->careerModalNotes ?: null,
        ];

        if ($this->editingCareerTermId) {
            CareerTerm::find($this->editingCareerTermId)->update($data);
        } else {
            $this->character->careerTerms()->create($data);
        }

        unset($this->careerTerms);
        $this->modal('career-modal')->close();
        Flux::toast('Career term saved.');
    }

    public function deleteCareerTerm(int $termId): void
    {
        CareerTerm::findOrFail($termId)->delete();
        unset($this->careerTerms);
    }

    // ---- Inventory ----

    public function openInventoryModal(?int $itemId = null): void
    {
        if ($itemId) {
            $item = InventoryItem::find($itemId);
            $this->editingInventoryId = $itemId;
            $this->inventoryModalName = $item->name;
            $this->inventoryModalQuantity = $item->quantity ?? 1;
            $this->inventoryModalDescription = $item->description ?? '';
        } else {
            $this->editingInventoryId = null;
            $this->inventoryModalName = '';
            $this->inventoryModalQuantity = 1;
            $this->inventoryModalDescription = '';
        }

        $this->modal('inventory-modal')->show();
    }

    public function saveInventoryItem(): void
    {
        $this->validate([
            'inventoryModalName' => 'required|string|max:255',
            'inventoryModalQuantity' => 'required|integer|min:1',
        ]);

        $data = [
            'name' => $this->inventoryModalName,
            'quantity' => $this->inventoryModalQuantity,
            'description' => $this->inventoryModalDescription ?: null,
        ];

        if ($this->editingInventoryId) {
            InventoryItem::find($this->editingInventoryId)->update($data);
        } else {
            $this->character->inventoryItems()->create($data);
        }

        unset($this->inventoryItems);
        $this->modal('inventory-modal')->close();
        Flux::toast('Item saved.');
    }

    public function deleteInventoryItem(int $itemId): void
    {
        InventoryItem::findOrFail($itemId)->delete();
        unset($this->inventoryItems);
    }

    // ---- Connections ----

    public function openConnectionModal(?int $id = null, string $type = 'npc'): void
    {
        $this->connectionModalType = $type;
        $this->editingConnectionId = $id;
        $this->connectionModalNotes = '';

        if ($id && $type === 'npc') {
            $conn = CharacterNpc::find($id);
            $this->connectionModalNpcId = $conn->npc_id;
            $this->connectionModalNpcRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } elseif ($id && $type === 'org') {
            $conn = CharacterOrganization::find($id);
            $this->connectionModalOrgId = $conn->organization_id;
            $this->connectionModalOrgRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } else {
            $this->connectionModalNpcId = null;
            $this->connectionModalNpcRelType = 'contact';
            $this->connectionModalOrgId = null;
            $this->connectionModalOrgRelType = 'member';
        }

        $this->modal('connection-modal')->show();
    }

    public function saveConnection(): void
    {
        if ($this->connectionModalType === 'npc') {
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
                CharacterNpc::find($this->editingConnectionId)->update($data);
            } else {
                $this->character->characterNpcs()->create($data);
            }

            unset($this->allConnections);
        } else {
            $this->validate(
                ['connectionModalOrgId' => 'required|integer|exists:organizations,id'],
                ['connectionModalOrgId.required' => 'You must select an organization.'],
            );

            $data = [
                'organization_id' => $this->connectionModalOrgId,
                'relationship_type' => $this->connectionModalOrgRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                CharacterOrganization::find($this->editingConnectionId)->update($data);
            } else {
                $this->character->organizations()->create($data);
            }

            unset($this->allConnections);
        }

        $this->modal('connection-modal')->close();
        Flux::toast('Connection saved.');
    }

    public function deleteConnection(int $id, string $type): void
    {
        if ($type === 'npc') {
            CharacterNpc::findOrFail($id)->delete();
        } else {
            CharacterOrganization::findOrFail($id)->delete();
        }

        unset($this->allConnections);
    }

    // ---- Image ----

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);

        if ($this->character->image_path) {
            Storage::disk('public')->delete($this->character->image_path);
        }

        $path = $this->imageUpload->store('characters', 'public');
        $this->character->update(['image_path' => $path]);
        $this->imageUpload = null;
        Flux::toast('Image uploaded.');
    }

    public function deleteImage(): void
    {
        if ($this->character->image_path) {
            Storage::disk('public')->delete($this->character->image_path);
            $this->character->update(['image_path' => null]);
        }
    }

    private function availableSkills(): array
    {
        $used = $this->skills->pluck('name');

        if ($this->editingSkillId) {
            $current = $this->skills->firstWhere('id', $this->editingSkillId)?->name;
            $used = $used->reject(fn ($n) => $n === $current);
        }

        return array_values(array_diff(Mgt2::SKILLS, $used->all()));
    }

    public function render(): View
    {
        $planets = Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id');
        $allNpcs = Npc::orderBy('name')->get(['id', 'name']);
        $allOrgs = Organization::orderBy('name')->get(['id', 'name']);

        return view('livewire.pcs.edit', [
            'planets' => $planets,
            'statOptions' => Mgt2::statOptions(),
            'statusOptions' => CharacterStatus::cases(),
            'mgt2Skills' => $this->availableSkills(),
            'npcRelationshipTypes' => NpcRelationshipType::cases(),
            'orgRelationshipTypes' => OrganizationRelationshipType::cases(),
            'allNpcs' => $allNpcs,
            'allOrgs' => $allOrgs,
        ]);
    }
}
