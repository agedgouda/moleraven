<?php

use App\Enums\OrganizationRelationshipType;
use App\Livewire\Npcs\Edit as EditNpc;
use App\Livewire\Organizations\Create as CreateOrganization;
use App\Livewire\Organizations\Edit as EditOrganization;
use App\Livewire\Organizations\Index as ListOrganizations;
use App\Livewire\Pcs\Edit as EditCharacter;
use App\Models\Character;
use App\Models\CharacterOrganization;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('organization list', function () {
    it('shows all organizations', function () {
        Organization::factory()->create(['name' => 'Zhodani Consulate']);

        Livewire::test(ListOrganizations::class)->assertSee('Zhodani Consulate');
    });
});

describe('organization creation', function () {
    it('can create an organization', function () {
        Livewire::test(CreateOrganization::class)
            ->set('name', 'Imperial Navy')
            ->set('type', 'Military')
            ->call('save')
            ->assertHasNoErrors();

        expect(Organization::where('name', 'Imperial Navy')->exists())->toBeTrue();
    });
});

describe('organization editing', function () {
    it('can edit an organization', function () {
        $org = Organization::factory()->create();

        Livewire::test(EditOrganization::class, ['organization' => $org])
            ->set('name', 'Updated Name')
            ->set('type', 'Criminal')
            ->call('save')
            ->assertHasNoErrors();

        expect($org->fresh()->name)->toBe('Updated Name');
    });
});

describe('character organizations (from character)', function () {
    it('can connect a character to an organization', function () {
        $character = Character::factory()->for($this->user)->create();
        $org = Organization::factory()->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('connectionModalType', 'org')
            ->set('connectionModalOrgId', $org->id)
            ->set('connectionModalOrgRelType', OrganizationRelationshipType::Member->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($character->organizations()->where('organization_id', $org->id)->exists())->toBeTrue();
    });

    it('can edit a character organization connection', function () {
        $character = Character::factory()->for($this->user)->create();
        $org = Organization::factory()->create();
        $membership = CharacterOrganization::create([
            'character_id' => $character->id,
            'organization_id' => $org->id,
            'relationship_type' => OrganizationRelationshipType::Member,
        ]);

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('connectionModalType', 'org')
            ->set('editingConnectionId', $membership->id)
            ->set('connectionModalOrgId', $org->id)
            ->set('connectionModalOrgRelType', OrganizationRelationshipType::FormerMember->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($membership->fresh()->relationship_type)->toBe(OrganizationRelationshipType::FormerMember);
    });

    it('can delete a character organization connection', function () {
        $character = Character::factory()->for($this->user)->create();
        $org = Organization::factory()->create();
        $membership = CharacterOrganization::create([
            'character_id' => $character->id,
            'organization_id' => $org->id,
            'relationship_type' => OrganizationRelationshipType::Enemy,
        ]);

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->call('deleteConnection', $membership->id, 'org')
            ->assertHasNoErrors();

        expect(CharacterOrganization::find($membership->id))->toBeNull();
    });
});

describe('NPC organizations (from NPC)', function () {
    it('can connect an NPC to an organization', function () {
        $npc = Npc::factory()->create();
        $org = Organization::factory()->create();

        Livewire::test(EditNpc::class, ['npc' => $npc])
            ->set('connectionModalType', 'org')
            ->set('connectionModalOrgId', $org->id)
            ->set('connectionModalOrgRelType', OrganizationRelationshipType::Patron->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($npc->organizations()->where('organization_id', $org->id)->exists())->toBeTrue();
    });
});

describe('organization members (from organization)', function () {
    it('can add a character member from the organization side', function () {
        $org = Organization::factory()->create();
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditOrganization::class, ['organization' => $org])
            ->set('connectionModalType', 'character')
            ->set('connectionModalCharacterId', $character->id)
            ->set('connectionModalCharacterRelType', OrganizationRelationshipType::Ally->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($org->characterMemberships()->where('character_id', $character->id)->exists())->toBeTrue();
    });

    it('can add an NPC member from the organization side', function () {
        $org = Organization::factory()->create();
        $npc = Npc::factory()->create();

        Livewire::test(EditOrganization::class, ['organization' => $org])
            ->set('connectionModalType', 'npc')
            ->set('connectionModalNpcId', $npc->id)
            ->set('connectionModalNpcRelType', OrganizationRelationshipType::Contact->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($org->npcMemberships()->where('npc_id', $npc->id)->exists())->toBeTrue();
    });
});
