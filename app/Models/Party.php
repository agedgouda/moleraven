<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $current_planet_id
 * @property string|null $notes
 */
#[Fillable(['current_planet_id', 'notes'])]
class Party extends Model
{
    public static function instance(): self
    {
        return self::firstOrCreate([]);
    }

    /** @return BelongsTo<Planet, $this> */
    public function currentPlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'current_planet_id');
    }

    /** @return HasMany<Character, $this> */
    public function members(): HasMany
    {
        return $this->hasMany(Character::class)->where('status', 'active');
    }
}
