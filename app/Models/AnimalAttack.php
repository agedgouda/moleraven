<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $animal_id
 * @property string $name
 * @property string $damage
 * @property string|null $attack_traits
 */
#[Fillable(['animal_id', 'name', 'damage', 'attack_traits'])]
class AnimalAttack extends Model
{
    /** @return BelongsTo<Animal, $this> */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
