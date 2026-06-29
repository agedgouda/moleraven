<?php

use App\Enums\NpcRelationshipType;
use App\Livewire\Npcs\Create as CreateNpc;
use App\Livewire\Npcs\Edit as EditNpc;
use App\Livewire\Npcs\Index as ListNpcs;
use App\Livewire\Pcs\Edit as EditCharacter;
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

describe('NPC list', function () {
    it('shows all NPCs to any logged-in user', function () {
        Npc::factory()->create(['name' => 'Baron Hendricks']);

        Livewire::test(ListNpcs::class)->assertSee('Baron Hendricks');
    });

    it('displays the UPP string', function () {
        Npc::factory()->create([
            'name' => 'Test NPC',
            'strength' => 7, 'dexterity' => 7, 'endurance' => 7,
            'intelligence' => 7, 'education' => 7, 'social_standing' => 7,
        ]);

        Livewire::test(ListNpcs::class)->assertSee('777777');
    });
});

describe('NPC creation', function () {
    it('can create an NPC', function () {
        Livewire::test(CreateNpc::class)
            ->set('name', 'Lady Voss')
            ->set('strength', 6)
            ->set('dexterity', 8)
            ->set('endurance', 7)
            ->set('intelligence', 10)
            ->set('education', 9)
            ->set('socialStanding', 12)
            ->set('age', 45)
            ->call('save')
            ->assertHasNoErrors();

        expect(Npc::where('name', 'Lady Voss')->exists())->toBeTrue();
    });
});

describe('NPC UPP', function () {
    it('formats characteristics as hex', function () {
        $npc = Npc::factory()->make([
            'strength' => 10, 'dexterity' => 11, 'endurance' => 12,
            'intelligence' => 13, 'education' => 14, 'social_standing' => 15,
        ]);

        expect($npc->uppString())->toBe('ABCDEF');
    });
});

describe('NPC skills', function () {
    it('can add skills to an NPC', function () {
        $npc = Npc::factory()->create();

        Livewire::test(EditNpc::class, ['npc' => $npc])
            ->set('skillModalName', 'Persuade')
            ->set('skillModalLevel', 3)
            ->call('saveSkill')
            ->assertHasNoErrors();

        expect($npc->skills()->where('name', 'Persuade')->where('level', 3)->exists())->toBeTrue();
    });
});

describe('character NPC connections', function () {
    it('can connect an NPC to a character as an ally', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(EditCharacter::class, ['character' => $character])
            ->set('connectionModalType', 'npc')
            ->set('connectionModalNpcId', $npc->id)
            ->set('connectionModalNpcRelType', NpcRelationshipType::Ally->value)
            ->set('connectionModalNotes', 'Met during the Spinward Marches campaign.')
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($character->characterNpcs()->where('npc_id', $npc->id)->exists())->toBeTrue()
            ->and($character->characterNpcs()->first()->relationship_type)->toBe(NpcRelationshipType::Ally);
    });

    it('can connect a character to an NPC from the NPC side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(EditNpc::class, ['npc' => $npc])
            ->set('connectionModalType', 'character')
            ->set('connectionModalCharacterId', $character->id)
            ->set('connectionModalCharacterRelType', NpcRelationshipType::Contact->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($npc->characterConnections()->where('character_id', $character->id)->exists())->toBeTrue()
            ->and($npc->characterConnections()->first()->relationship_type)->toBe(NpcRelationshipType::Contact);
    });

    it('can edit a connection from the NPC side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();
        $connection = CharacterNpc::create([
            'character_id' => $character->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Contact,
        ]);

        Livewire::test(EditNpc::class, ['npc' => $npc])
            ->set('connectionModalType', 'character')
            ->set('editingConnectionId', $connection->id)
            ->set('connectionModalCharacterId', $character->id)
            ->set('connectionModalCharacterRelType', NpcRelationshipType::Rival->value)
            ->call('saveConnection')
            ->assertHasNoErrors();

        expect($connection->fresh()->relationship_type)->toBe(NpcRelationshipType::Rival);
    });

    it('can delete a connection from the NPC side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();
        $connection = CharacterNpc::create([
            'character_id' => $character->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Enemy,
        ]);

        Livewire::test(EditNpc::class, ['npc' => $npc])
            ->call('deleteConnection', $connection->id, 'character')
            ->assertHasNoErrors();

        expect(CharacterNpc::find($connection->id))->toBeNull();
    });

    it('can connect the same NPC with different relationship types to different characters', function () {
        $npc = Npc::factory()->create();
        $ally = Character::factory()->for($this->user)->create();
        $enemy = Character::factory()->for(User::factory()->create())->create();

        CharacterNpc::create(['character_id' => $ally->id, 'npc_id' => $npc->id, 'relationship_type' => NpcRelationshipType::Ally]);
        CharacterNpc::create(['character_id' => $enemy->id, 'npc_id' => $npc->id, 'relationship_type' => NpcRelationshipType::Enemy]);

        expect($npc->characterConnections()->count())->toBe(2);
    });
});
