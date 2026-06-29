<?php

use App\Enums\NpcRelationshipType;
use App\Filament\Resources\Characters\Pages\EditCharacter;
use App\Filament\Resources\Characters\RelationManagers\CharacterNpcsRelationManager;
use App\Filament\Resources\Npcs\Pages\CreateNpc;
use App\Filament\Resources\Npcs\Pages\EditNpc;
use App\Filament\Resources\Npcs\Pages\ListNpcs;
use App\Filament\Resources\Npcs\RelationManagers\CharacterConnectionsRelationManager;
use App\Models\Character;
use App\Models\CharacterNpc;
use App\Models\Npc;
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

describe('NPC list', function () {
    it('shows all NPCs to any logged-in user', function () {
        $npc = Npc::factory()->create(['name' => 'Baron Hendricks']);

        Livewire::test(ListNpcs::class)
            ->assertCanSeeTableRecords([$npc]);
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
            ->fillForm([
                'name' => 'Lady Voss',
                'strength' => 6, 'dexterity' => 8, 'endurance' => 7,
                'intelligence' => 10, 'education' => 9, 'social_standing' => 12,
                'homeworld' => 'Regina',
                'age' => 45,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

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
    it('can add skills to an NPC via the inline repeater', function () {
        $npc = Npc::factory()->create();

        Livewire::test(EditNpc::class, ['record' => $npc->id])
            ->fillForm([
                'skills' => [
                    ['name' => 'Persuade', 'level' => 3],
                    ['name' => 'Streetwise', 'level' => 2],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($npc->skills()->count())->toBe(2)
            ->and($npc->skills()->where('name', 'Persuade')->where('level', 3)->exists())->toBeTrue();
    });
});

describe('character NPC connections', function () {
    it('can connect an NPC to a character as an ally', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(CharacterNpcsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'npc_id' => $npc->id,
                'relationship_type' => NpcRelationshipType::Ally->value,
                'notes' => 'Met during the Spinward Marches campaign.',
            ])
            ->assertHasNoActionErrors();

        expect($character->characterNpcs()->where('npc_id', $npc->id)->exists())->toBeTrue()
            ->and($character->characterNpcs()->first()->relationship_type)->toBe(NpcRelationshipType::Ally);
    });

    it('can connect a character to an NPC from the NPC side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(CharacterConnectionsRelationManager::class, [
            'ownerRecord' => $npc,
            'pageClass' => EditNpc::class,
        ])
            ->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'character_id' => $character->id,
                'relationship_type' => NpcRelationshipType::Contact->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterConnectionsRelationManager::class, [
            'ownerRecord' => $npc,
            'pageClass' => EditNpc::class,
        ])
            ->callAction(TestAction::make(EditAction::class)->table($connection->id), [
                'relationship_type' => NpcRelationshipType::Rival->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterConnectionsRelationManager::class, [
            'ownerRecord' => $npc,
            'pageClass' => EditNpc::class,
        ])
            ->callAction(TestAction::make(DeleteAction::class)->table($connection->id))
            ->assertHasNoActionErrors();

        expect(CharacterNpc::find($connection->id))->toBeNull();
    });

    it('can connect the same NPC with different relationship types to different characters', function () {
        $npc = Npc::factory()->create();
        $ally = Character::factory()->for($this->user)->create();
        $enemy = Character::factory()->for(User::factory()->create())->create();

        CharacterNpc::create([
            'character_id' => $ally->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Ally,
        ]);

        CharacterNpc::create([
            'character_id' => $enemy->id,
            'npc_id' => $npc->id,
            'relationship_type' => NpcRelationshipType::Enemy,
        ]);

        expect($npc->characterConnections()->count())->toBe(2);
    });
});
