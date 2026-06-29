<?php

namespace App\Models;

use App\Enums\CharacterStatus;
use Database\Factories\CharacterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $is_current
 * @property CharacterStatus $status
 * @property int $strength
 * @property int $dexterity
 * @property int $endurance
 * @property int $intelligence
 * @property int $education
 * @property int $social_standing
 * @property int $age
 * @property int|null $homeworld_planet_id
 * @property int|null $last_known_planet_id
 * @property int $credits
 * @property string|null $notes
 */
#[Fillable([
    'user_id', 'name', 'is_current', 'status',
    'strength', 'dexterity', 'endurance', 'intelligence', 'education', 'social_standing',
    'age', 'homeworld_planet_id', 'last_known_planet_id', 'credits', 'notes',
])]
class Character extends Model
{
    /** @use HasFactory<CharacterFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'status' => CharacterStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Character $character) {
            if ($character->is_current) {
                static::where('user_id', $character->user_id)
                    ->where('id', '!=', $character->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function uppString(): string
    {
        return implode('', array_map(
            fn (int $stat) => strtoupper(dechex($stat)),
            [$this->strength, $this->dexterity, $this->endurance, $this->intelligence, $this->education, $this->social_standing]
        ));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeworld(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'homeworld_planet_id');
    }

    public function lastKnownPlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'last_known_planet_id');
    }

    public function careerTerms(): HasMany
    {
        return $this->hasMany(CareerTerm::class)->orderBy('term');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('name');
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class)->orderBy('name');
    }

    public function characterNpcs(): HasMany
    {
        return $this->hasMany(CharacterNpc::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(CharacterOrganization::class);
    }
}
