<?php

namespace App\Livewire\Npcs;

use App\Enums\NpcRelationshipType;
use App\Enums\OrganizationRelationshipType;
use App\Livewire\Concerns\HasConnectionFilters;
use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\Npc;
use App\Models\NpcOrganization;
use App\Models\NpcSkill;
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

#[Layout('layouts.app', ['title' => 'Edit NPC'])]
#[Title('Edit NPC')]
class Edit extends Component
{
    use HasConnectionFilters, WithFileUploads;

    public Npc $npc;

    public $imageUpload;

    public string $name = '';

    public ?int $homeworldPlanetId = null;

    public ?int $lastKnownPlanetId = null;

    public ?int $age = null;

    public int $strength = 7;

    public int $dexterity = 7;

    public int $endurance = 7;

    public int $intelligence = 7;

    public int $education = 7;

    public int $socialStanding = 7;

    public ?string $notes = '';

    // Skill modal state
    public ?int $editingSkillId = null;

    public string $skillModalName = '';

    public int $skillModalLevel = 0;

    // Connection modal state
    public string $connectionModalType = 'character';

    public ?int $editingConnectionId = null;

    public ?int $connectionModalCharacterId = null;

    public string $connectionModalCharacterRelType = 'contact';

    public ?int $connectionModalOrgId = null;

    public string $connectionModalOrgRelType = 'member';

    public string $connectionModalNotes = '';

    #[Computed]
    public function skills(): Collection
    {
        return $this->npc->skills()->orderBy('name')->get();
    }

    #[Computed]
    public function allConnections(): Collection
    {
        $character = $this->npc->characterConnections()->with('character')->get()->map(fn ($c) => [
            'id' => $c->id, 'type' => 'character', 'model' => $c->character,
            'name' => $c->character->name, 'label' => 'PC',
            'route' => route('pcs.edit', $c->character),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        $org = $this->npc->organizations()->with('organization')->get()->map(fn ($c) => [
            'id' => $c->id, 'type' => 'org', 'model' => $c->organization,
            'name' => $c->organization->name, 'label' => 'Org',
            'route' => route('organizations.edit', $c->organization),
            'relationshipType' => $c->relationship_type, 'notes' => $c->notes,
        ]);

        return $this->applyConnectionFilters($character->concat($org));
    }

    public function mount(Npc $npc): void
    {
        $this->npc = $npc;
        $this->name = $npc->name;
        $this->homeworldPlanetId = $npc->homeworld_planet_id;
        $this->lastKnownPlanetId = $npc->last_known_planet_id;
        $this->age = $npc->age;
        $this->strength = $npc->strength;
        $this->dexterity = $npc->dexterity;
        $this->endurance = $npc->endurance;
        $this->intelligence = $npc->intelligence;
        $this->education = $npc->education;
        $this->socialStanding = $npc->social_standing;
        $this->notes = $npc->notes ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'strength' => 'required|integer|min:0|max:15',
            'dexterity' => 'required|integer|min:0|max:15',
            'endurance' => 'required|integer|min:0|max:15',
            'intelligence' => 'required|integer|min:0|max:15',
            'education' => 'required|integer|min:0|max:15',
            'socialStanding' => 'required|integer|min:0|max:15',
        ]);

        $this->npc->update([
            'name' => $this->name,
            'homeworld_planet_id' => $this->homeworldPlanetId,
            'last_known_planet_id' => $this->lastKnownPlanetId,
            'age' => $this->age,
            'strength' => $this->strength,
            'dexterity' => $this->dexterity,
            'endurance' => $this->endurance,
            'intelligence' => $this->intelligence,
            'education' => $this->education,
            'social_standing' => $this->socialStanding,
            'notes' => $this->notes ?: null,
        ]);

        Flux::toast('NPC saved.');
    }

    public function openSkillModal(?int $skillId = null): void
    {
        if ($skillId) {
            $skill = NpcSkill::find($skillId);
            $this->editingSkillId = $skillId;
            $this->skillModalName = $skill->name;
            $this->skillModalLevel = $skill->level;
        } else {
            $this->editingSkillId = null;
            $this->skillModalName = '';
            $this->skillModalLevel = 0;
        }

        $this->modal('skill-modal')->show();
    }

    public function saveSkill(): void
    {
        $this->validate([
            'skillModalName' => 'required|string|max:255',
            'skillModalLevel' => 'required|integer|min:0|max:6',
        ]);

        if ($this->editingSkillId) {
            NpcSkill::find($this->editingSkillId)->update([
                'name' => $this->skillModalName,
                'level' => $this->skillModalLevel,
            ]);
        } else {
            $this->npc->skills()->create([
                'name' => $this->skillModalName,
                'level' => $this->skillModalLevel,
            ]);
        }

        unset($this->skills);
        $this->modal('skill-modal')->close();
        Flux::toast('Skill saved.');
    }

    public function deleteSkill(int $skillId): void
    {
        NpcSkill::findOrFail($skillId)->delete();
        unset($this->skills);
    }

    // ---- Connections ----

    public function openConnectionModal(?int $id = null, string $type = 'character'): void
    {
        $this->connectionModalType = $type;
        $this->editingConnectionId = $id;
        $this->connectionModalNotes = '';

        if ($id && $type === 'character') {
            $conn = CharacterNpc::find($id);
            $this->connectionModalCharacterId = $conn->character_id;
            $this->connectionModalCharacterRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } elseif ($id && $type === 'org') {
            $conn = NpcOrganization::find($id);
            $this->connectionModalOrgId = $conn->organization_id;
            $this->connectionModalOrgRelType = $conn->relationship_type->value;
            $this->connectionModalNotes = $conn->notes ?? '';
        } else {
            $this->connectionModalCharacterId = null;
            $this->connectionModalCharacterRelType = 'contact';
            $this->connectionModalOrgId = null;
            $this->connectionModalOrgRelType = 'member';
        }

        $this->modal('npc-connection-modal')->show();
    }

    public function saveConnection(): void
    {
        if ($this->connectionModalType === 'character') {
            $this->validate(['connectionModalCharacterId' => 'required|integer|exists:characters,id']);

            $data = [
                'character_id' => $this->connectionModalCharacterId,
                'relationship_type' => $this->connectionModalCharacterRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                CharacterNpc::find($this->editingConnectionId)->update($data);
            } else {
                $this->npc->characterConnections()->create($data);
            }

            unset($this->allConnections);
        } else {
            $this->validate(['connectionModalOrgId' => 'required|integer|exists:organizations,id']);

            $data = [
                'organization_id' => $this->connectionModalOrgId,
                'relationship_type' => $this->connectionModalOrgRelType,
                'notes' => $this->connectionModalNotes ?: null,
            ];

            if ($this->editingConnectionId) {
                NpcOrganization::find($this->editingConnectionId)->update($data);
            } else {
                $this->npc->organizations()->create($data);
            }

            unset($this->allConnections);
        }

        $this->modal('npc-connection-modal')->close();
        Flux::toast('Connection saved.');
    }

    public function deleteConnection(int $id, string $type): void
    {
        if ($type === 'character') {
            CharacterNpc::findOrFail($id)->delete();
            unset($this->allConnections);
        } else {
            NpcOrganization::findOrFail($id)->delete();
            unset($this->allConnections);
        }
    }

    // ---- Image ----

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);

        if ($this->npc->image_path) {
            Storage::disk('public')->delete($this->npc->image_path);
        }

        $path = $this->imageUpload->store('npcs', 'public');
        $this->npc->update(['image_path' => $path]);
        $this->imageUpload = null;
        Flux::toast('Image uploaded.');
    }

    public function deleteImage(): void
    {
        if ($this->npc->image_path) {
            Storage::disk('public')->delete($this->npc->image_path);
            $this->npc->update(['image_path' => null]);
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
        $allCharacters = Character::orderBy('name')->get(['id', 'name']);
        $allOrgs = Organization::orderBy('name')->get(['id', 'name']);

        return view('livewire.npcs.edit', [
            'planets' => $planets,
            'statOptions' => Mgt2::statOptions(),
            'mgt2Skills' => $this->availableSkills(),
            'npcRelationshipTypes' => NpcRelationshipType::cases(),
            'orgRelationshipTypes' => OrganizationRelationshipType::cases(),
            'allCharacters' => $allCharacters,
            'allOrgs' => $allOrgs,
        ]);
    }
}
