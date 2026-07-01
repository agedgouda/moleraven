<?php

namespace App\Models;

use Database\Factories\DiaryEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $character_id
 * @property string|null $entry_date
 * @property string|null $entry
 */
#[Fillable(['character_id', 'entry_date', 'entry'])]
class DiaryEntry extends Model
{
    /** @use HasFactory<DiaryEntryFactory> */
    use HasFactory;

    /** @return BelongsTo<Character, $this> */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /** @return BelongsToMany<Planet, $this> */
    public function planets(): BelongsToMany
    {
        return $this->belongsToMany(Planet::class, 'diary_entry_planets');
    }

    /** @return BelongsToMany<Npc, $this> */
    public function npcs(): BelongsToMany
    {
        return $this->belongsToMany(Npc::class, 'diary_entry_npcs');
    }

    /** @return BelongsToMany<Animal, $this> */
    public function animals(): BelongsToMany
    {
        return $this->belongsToMany(Animal::class, 'diary_entry_animals');
    }

    /** @return BelongsToMany<Organization, $this> */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'diary_entry_organizations');
    }
}
