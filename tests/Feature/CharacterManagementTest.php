<?php

use App\Enums\NpcRelationshipType;
use App\Livewire\Party\Index as PartyIndex;
use App\Livewire\Pcs\Create as CreateCharacter;
use App\Livewire\Pcs\Edit as EditCharacter;
use App\Livewire\Pcs\Index as ListCharacters;
use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\Npc;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('character list', function () {
    it('shows all characters', function () {
        Character::factory()->for($this->user)->create(['name' => 'My Character']);
        Character::factory()->create(['name' => 'Other Character']);

        Livewire::test(ListCharacters::class)
            ->assertSee('My Character')
            ->assertSee('Other Character');
    });

    it('displays the UPP string', function () {
        Character::factory()->for($this->user)->create([
            'strength' => 7, 'dexterity' => 7, 'endurance' => 7,
            'intelligence' => 7, 'education' => 7, 'social_standing' => 7,
        ]);

        Livewire::test(ListCharacters::class)->assertSee('777777');
    });
});

describe('character creation', function () {
    it('creates a character assigned to the authenticated user', function () {
        Livewire::test(CreateCharacter::class)
            ->set('name', 'Ander Voss')
            ->set('strength', 8)
            ->set('dexterity', 9)
            ->set('endurance', 7)
            ->set('intelligence', 10)
            ->set('education', 8)
            ->set('socialStanding', 6)
            ->set('age', 26)
            ->set('credits', 5000)
            ->call('save')
            ->assertHasNoErrors();

        $character = Character::where('name', 'Ander Voss')->first();
        expect($character)->not->toBeNull()
            ->and($character->user_id)->toBe($this->user->id);
    });
});

describe('current character toggle', function () {
    it('sets a character as current', function () {
        $character = Character::factory()->for($this->user)->create(['is_current' => false]);

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('isCurrent', true)
            ->call('save');

        expect($character->fresh()->is_current)->toBeTrue();
    });

    it('unsets a current character', function () {
        $character = Character::factory()->for($this->user)->current()->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('isCurrent', false)
            ->call('save');

        expect($character->fresh()->is_current)->toBeFalse();
    });
});

describe('current character enforcement', function () {
    it('unsets other current characters when a new one is set', function () {
        $first = Character::factory()->for($this->user)->current()->create();
        $second = Character::factory()->for($this->user)->create();

        $second->update(['is_current' => true]);

        expect($first->fresh()->is_current)->toBeFalse()
            ->and($second->fresh()->is_current)->toBeTrue();
    });

    it('does not affect other users\' current characters', function () {
        $otherUser = User::factory()->create();
        $otherCharacter = Character::factory()->for($otherUser)->current()->create();

        Character::factory()->for($this->user)->current()->create();

        expect($otherCharacter->fresh()->is_current)->toBeTrue();
    });
});

describe('character editing', function () {
    it('can edit a character', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('name', 'Updated Name')
            ->set('credits', 9999)
            ->call('save')
            ->assertHasNoErrors();

        expect($character->fresh()->name)->toBe('Updated Name')
            ->and($character->fresh()->credits)->toBe(9999);
    });
});

describe('UPP calculation', function () {
    it('formats characteristics as hex', function () {
        $character = Character::factory()->for($this->user)->make([
            'strength' => 10, 'dexterity' => 11, 'endurance' => 12,
            'intelligence' => 13, 'education' => 14, 'social_standing' => 15,
        ]);

        expect($character->uppString())->toBe('ABCDEF');
    });

    it('formats single digit characteristics correctly', function () {
        $character = Character::factory()->for($this->user)->make([
            'strength' => 7, 'dexterity' => 6, 'endurance' => 8,
            'intelligence' => 9, 'education' => 5, 'social_standing' => 4,
        ]);

        expect($character->uppString())->toBe('768954');
    });
});

describe('career terms', function () {
    it('can create a career term', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('careerModalCareer', 'Navy')
            ->set('careerModalAssignment', 'Flight')
            ->set('careerModalTerm', 1)
            ->set('careerModalRank', 2)
            ->set('careerModalRankTitle', 'Lieutenant')
            ->call('saveCareerTerm')
            ->assertHasNoErrors();

        expect($character->careerTerms()->count())->toBe(1)
            ->and($character->careerTerms()->first()->career)->toBe('Navy');
    });
});

describe('skills', function () {
    it('can add a skill', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('skillModalName', 'Pilot')
            ->set('skillModalLevel', 2)
            ->call('saveSkill')
            ->assertHasNoErrors();

        expect($character->skills()->where('name', 'Pilot')->where('level', 2)->exists())->toBeTrue();
    });

    it('can add multiple skills', function () {
        $character = Character::factory()->for($this->user)->create();
        $component = Livewire::test(EditCharacter::class, ['character' => $character]);

        $component->set('skillModalName', 'Pilot')->set('skillModalLevel', 2)->call('saveSkill');
        $component->set('skillModalName', 'Astrogation')->set('skillModalLevel', 1)->call('saveSkill');

        expect($character->skills()->count())->toBe(2)
            ->and($character->skills()->where('name', 'Astrogation')->where('level', 1)->exists())->toBeTrue();
    });
});

describe('inventory', function () {
    it('can add an inventory item', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('inventoryModalName', 'Autopistol')
            ->set('inventoryModalQuantity', 1)
            ->call('saveInventoryItem')
            ->assertHasNoErrors();

        expect($character->inventoryItems()->where('name', 'Autopistol')->exists())->toBeTrue();
    });
});

describe('character NPC connections', function () {
    it('can create a connection from the character side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('connectionModalType', 'npc')
            ->set('connectionModalNpcId', $npc->id)
            ->set('connectionModalNpcRelType', NpcRelationshipType::Rival->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($character->characterNpcs()->where('npc_id', $npc->id)->exists())->toBeTrue();
    });

    it('can edit a connection from the character side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();
        $connection = CharacterNpc::create([
            'character_id' => $character->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Contact,
        ]);

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('connectionModalType', 'npc')
            ->set('editingConnectionId', $connection->id)
            ->set('connectionModalNpcId', $npc->id)
            ->set('connectionModalNpcRelType', NpcRelationshipType::Ally->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($connection->fresh()->relationship_type)->toBe(NpcRelationshipType::Ally);
    });

    it('can delete a connection from the character side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();
        $connection = CharacterNpc::create([
            'character_id' => $character->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Enemy,
        ]);

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->call('deleteConnection', $connection->id, 'npc')
            ->assertHasNoErrors();

        expect(CharacterNpc::find($connection->id))->toBeNull();
    });
});

describe('party page', function () {
    it('shows current characters from all users', function () {
        $other = User::factory()->create();
        Character::factory()->for($other)->current()->create(['name' => 'Zanith Rol']);
        Character::factory()->for($other)->retired()->create(['name' => 'Old Zanith']);

        Livewire::test(PartyIndex::class)
            ->assertSee('Zanith Rol')
            ->assertDontSee('Old Zanith');
    });

    it('shows characters from other users on the party page', function () {
        $other = User::factory()->create();
        Character::factory()->for($other)->current()->create(['name' => 'Ren Sulaar']);

        Livewire::test(PartyIndex::class)->assertSee('Ren Sulaar');
    });
});
