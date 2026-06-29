<?php

use App\Enums\OrganizationRelationshipType;
use App\Filament\Resources\Characters\Pages\EditCharacter;
use App\Filament\Resources\Characters\RelationManagers\CharacterOrganizationsRelationManager;
use App\Filament\Resources\Npcs\Pages\EditNpc;
use App\Filament\Resources\Npcs\RelationManagers\NpcOrganizationsRelationManager;
use App\Filament\Resources\Organizations\Pages\CreateOrganization;
use App\Filament\Resources\Organizations\Pages\EditOrganization;
use App\Filament\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Resources\Organizations\RelationManagers\CharacterMembersRelationManager;
use App\Filament\Resources\Organizations\RelationManagers\NpcMembersRelationManager;
use App\Models\Character;
use App\Models\CharacterOrganization;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('organization list', function () {
    it('shows all organizations', function () {
        $org = Organization::factory()->create(['name' => 'Zhodani Consulate']);

        Livewire::test(ListOrganizations::class)
            ->assertCanSeeTableRecords([$org]);
    });
});

describe('organization creation', function () {
    it('can create an organization', function () {
        Livewire::test(CreateOrganization::class)
            ->fillForm([
                'name' => 'Imperial Navy',
                'type' => 'Military',
                'base_of_operations' => 'Capital',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Organization::where('name', 'Imperial Navy')->exists())->toBeTrue();
    });
});

describe('organization editing', function () {
    it('can edit an organization', function () {
        $org = Organization::factory()->create();

        Livewire::test(EditOrganization::class, ['record' => $org->id])
            ->fillForm(['name' => 'Updated Name', 'type' => 'Criminal'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($org->fresh()->name)->toBe('Updated Name');
    });
});

describe('character organizations (from character)', function () {
    it('can connect a character to an organization', function () {
        $character = Character::factory()->for($this->user)->create();
        $org = Organization::factory()->create();

        Livewire::test(CharacterOrganizationsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'organization_id' => $org->id,
                'relationship_type' => OrganizationRelationshipType::Member->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterOrganizationsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(EditAction::class)->table($membership->id), [
                'relationship_type' => OrganizationRelationshipType::FormerMember->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterOrganizationsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(DeleteAction::class)->table($membership->id))
            ->assertHasNoActionErrors();

        expect(CharacterOrganization::find($membership->id))->toBeNull();
    });
});

describe('NPC organizations (from NPC)', function () {
    it('can connect an NPC to an organization', function () {
        $npc = Npc::factory()->create();
        $org = Organization::factory()->create();

        Livewire::test(NpcOrganizationsRelationManager::class, [
            'ownerRecord' => $npc,
            'pageClass' => EditNpc::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'organization_id' => $org->id,
                'relationship_type' => OrganizationRelationshipType::Patron->value,
            ])
            ->assertHasNoActionErrors();

        expect($npc->organizations()->where('organization_id', $org->id)->exists())->toBeTrue();
    });
});

describe('organization members (from organization)', function () {
    it('can add a character member from the organization side', function () {
        $org = Organization::factory()->create();
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(CharacterMembersRelationManager::class, [
            'ownerRecord' => $org,
            'pageClass' => EditOrganization::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'character_id' => $character->id,
                'relationship_type' => OrganizationRelationshipType::Ally->value,
            ])
            ->assertHasNoActionErrors();

        expect($org->characterMemberships()->where('character_id', $character->id)->exists())->toBeTrue();
    });

    it('can add an NPC member from the organization side', function () {
        $org = Organization::factory()->create();
        $npc = Npc::factory()->create();

        Livewire::test(NpcMembersRelationManager::class, [
            'ownerRecord' => $org,
            'pageClass' => EditOrganization::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'npc_id' => $npc->id,
                'relationship_type' => OrganizationRelationshipType::Contact->value,
            ])
            ->assertHasNoActionErrors();

        expect($org->npcMemberships()->where('npc_id', $npc->id)->exists())->toBeTrue();
    });
});
