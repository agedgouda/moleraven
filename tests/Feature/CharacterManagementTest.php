<?php

use App\Enums\CharacterStatus;
use App\Enums\NpcRelationshipType;
use App\Filament\Pages\Party;
use App\Filament\Resources\Characters\CharacterResource;
use App\Filament\Resources\Characters\Pages\CreateCharacter;
use App\Filament\Resources\Characters\Pages\EditCharacter;
use App\Filament\Resources\Characters\Pages\ListCharacters;
use App\Filament\Resources\Characters\RelationManagers\CareerTermsRelationManager;
use App\Filament\Resources\Characters\RelationManagers\CharacterNpcsRelationManager;
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

describe('character list', function () {
    it('shows only the authenticated user\'s characters', function () {
        $mine = Character::factory()->for($this->user)->create();
        $other = Character::factory()->create();

        Livewire::test(ListCharacters::class)
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$other]);
    });

    it('displays the UPP string', function () {
        Character::factory()->for($this->user)->create([
            'strength' => 7, 'dexterity' => 7, 'endurance' => 7,
            'intelligence' => 7, 'education' => 7, 'social_standing' => 7,
        ]);

        Livewire::test(ListCharacters::class)
            ->assertSee('777777');
    });
});

describe('character creation', function () {
    it('creates a character assigned to the authenticated user', function () {
        Livewire::test(CreateCharacter::class)
            ->fillForm([
                'name' => 'Ander Voss',
                'strength' => 8,
                'dexterity' => 9,
                'endurance' => 7,
                'intelligence' => 10,
                'education' => 8,
                'social_standing' => 6,
                'age' => 26,
                'homeworld' => 'Regina',
                'credits' => 5000,
                'status' => CharacterStatus::Active->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $character = Character::where('name', 'Ander Voss')->first();
        expect($character)->not->toBeNull()
            ->and($character->user_id)->toBe($this->user->id);
    });
});

describe('current character header action', function () {
    it('sets a character as current via the toggle action', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->callAction('toggle_current')
            ->assertHasNoActionErrors();

        expect($character->fresh()->is_current)->toBeTrue();
    });

    it('unsets a current character via the toggle action', function () {
        $character = Character::factory()->for($this->user)->current()->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->callAction('toggle_current')
            ->assertHasNoActionErrors();

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
    it('can edit own character', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->fillForm(['name' => 'Updated Name', 'credits' => 9999])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($character->fresh()->name)->toBe('Updated Name')
            ->and($character->fresh()->credits)->toBe(9999);
    });

    it('does not include another user\'s character in the query scope', function () {
        $other = Character::factory()->create();

        $visible = CharacterResource::getEloquentQuery()->pluck('id');

        expect($visible)->not->toContain($other->id);
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

describe('career terms relation manager', function () {
    it('can create a career term', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(CareerTermsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'career' => 'Navy',
                'assignment' => 'Flight',
                'term' => 1,
                'rank' => 2,
                'rank_title' => 'Lieutenant',
            ])
            ->assertHasNoActionErrors();

        expect($character->careerTerms()->count())->toBe(1)
            ->and($character->careerTerms()->first()->career)->toBe('Navy');
    });
});

describe('skills repeater', function () {
    it('can add skills via the inline repeater', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->fillForm([
                'skills' => [
                    ['name' => 'Pilot', 'level' => 2],
                    ['name' => 'Astrogation', 'level' => 1],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($character->skills()->count())->toBe(2)
            ->and($character->skills()->where('name', 'Pilot')->where('level', 2)->exists())->toBeTrue()
            ->and($character->skills()->where('name', 'Astrogation')->where('level', 1)->exists())->toBeTrue();
    });

    it('allows custom skill names not in the predefined list', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->fillForm([
                'skills' => [
                    ['name' => 'Xenobiology', 'level' => 1],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($character->skills()->where('name', 'Xenobiology')->exists())->toBeTrue();
    });
});

describe('inventory repeater', function () {
    it('can add inventory items via the inline repeater', function () {
        $character = Character::factory()->for($this->user)->create();

        Livewire::test(EditCharacter::class, ['record' => $character->id])
            ->fillForm([
                'inventoryItems' => [
                    ['name' => 'Autopistol', 'quantity' => 1, 'description' => null],
                    ['name' => 'Vacc Suit', 'quantity' => 1, 'description' => 'Standard issue'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($character->inventoryItems()->count())->toBe(2)
            ->and($character->inventoryItems()->where('name', 'Autopistol')->exists())->toBeTrue();
    });
});

describe('character NPC relation manager', function () {
    it('can create a connection from the character side', function () {
        $character = Character::factory()->for($this->user)->create();
        $npc = Npc::factory()->create();

        Livewire::test(CharacterNpcsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'npc_id' => $npc->id,
                'relationship_type' => NpcRelationshipType::Rival->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterNpcsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(EditAction::class)->table($connection->id), [
                'relationship_type' => NpcRelationshipType::Ally->value,
            ])
            ->assertHasNoActionErrors();

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

        Livewire::test(CharacterNpcsRelationManager::class, [
            'ownerRecord' => $character,
            'pageClass' => EditCharacter::class,
        ])
            ->callAction(TestAction::make(DeleteAction::class)->table($connection->id))
            ->assertHasNoActionErrors();

        expect(CharacterNpc::find($connection->id))->toBeNull();
    });
});

describe('party page', function () {
    it('shows current characters from all users', function () {
        $other = User::factory()->create();
        Character::factory()->for($other)->current()->create(['name' => 'Zanith Rol']);
        Character::factory()->for($other)->retired()->create(['name' => 'Old Zanith']);

        Livewire::test(Party::class)
            ->assertSee('Zanith Rol')
            ->assertDontSee('Old Zanith');
    });

    it('shows characters from other users on the party page', function () {
        $other = User::factory()->create();
        Character::factory()->for($other)->current()->create(['name' => 'Ren Sulaar']);

        Livewire::test(Party::class)->assertSee('Ren Sulaar');
    });
});
