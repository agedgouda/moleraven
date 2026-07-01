<?php

namespace App\Models;

use Database\Factories\NpcFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $strength
 * @property int $dexterity
 * @property int $endurance
 * @property int $intelligence
 * @property int $education
 * @property int $social_standing
 * @property int|null $age
 * @property int|null $homeworld_planet_id
 * @property int|null $last_known_planet_id
 * @property string|null $notes
 * @property string|null $image_path
 */
#[Fillable([
    'name',
    'strength', 'dexterity', 'endurance', 'intelligence', 'education', 'social_standing',
    'age', 'homeworld_planet_id', 'last_known_planet_id', 'notes', 'image_path',
])]
class Npc extends Model
{
    /** @use HasFactory<NpcFactory> */
    use HasFactory;

    public function uppString(): string
    {
        return implode('', array_map(
            fn (int $stat) => strtoupper(dechex($stat)),
            [$this->strength, $this->dexterity, $this->endurance, $this->intelligence, $this->education, $this->social_standing]
        ));
    }

    /** @return BelongsTo<Planet, $this> */
    public function homeworld(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'homeworld_planet_id');
    }

    /** @return BelongsTo<Planet, $this> */
    public function lastKnownPlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'last_known_planet_id');
    }

    /** @return HasMany<NpcSkill, $this> */
    public function skills(): HasMany
    {
        return $this->hasMany(NpcSkill::class)->orderBy('name');
    }

    /** @return HasMany<CharacterNpc, $this> */
    public function characterConnections(): HasMany
    {
        return $this->hasMany(CharacterNpc::class);
    }

    /** @return HasMany<NpcOrganization, $this> */
    public function organizations(): HasMany
    {
        return $this->hasMany(NpcOrganization::class);
    }
}
