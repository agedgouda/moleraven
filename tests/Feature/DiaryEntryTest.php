<?php

use App\Models\Animal;
use App\Models\Character;
use App\Models\DiaryEntry;
use App\Models\Npc;
use App\Models\Organization;
use App\Models\Planet;

it('belongs to a character', function () {
    $character = Character::factory()->create();
    $entry = DiaryEntry::factory()->for($character)->create();

    expect($entry->character->id)->toBe($character->id);
});

it('appears in character diaryEntries relation', function () {
    $character = Character::factory()->create();
    DiaryEntry::factory()->for($character)->create(['entry_date' => '1105-100']);
    DiaryEntry::factory()->for($character)->create(['entry_date' => '1105-050']);

    $dates = $character->diaryEntries->pluck('entry_date')->all();

    expect($dates)->toBe(['1105-100', '1105-050']);
});

it('can attach planets, npcs, animals, and organizations', function () {
    $entry = DiaryEntry::factory()->create();
    $planet = Planet::factory()->create();
    $npc = Npc::factory()->create();
    $animal = Animal::factory()->create();
    $org = Organization::factory()->create();

    $entry->planets()->attach($planet);
    $entry->npcs()->attach($npc);
    $entry->animals()->attach($animal);
    $entry->organizations()->attach($org);

    expect($entry->planets)->toHaveCount(1)
        ->and($entry->npcs)->toHaveCount(1)
        ->and($entry->animals)->toHaveCount(1)
        ->and($entry->organizations)->toHaveCount(1);
});

it('deletes pivot rows when diary entry is deleted', function () {
    $entry = DiaryEntry::factory()->create();
    $planet = Planet::factory()->create();
    $entry->planets()->attach($planet);

    $entry->delete();

    expect(DB::table('diary_entry_planets')->count())->toBe(0);
});
