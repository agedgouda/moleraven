<?php

namespace App\Models;

use App\Enums\BehaviorSubtype;
use App\Enums\BehaviorType;
use Database\Factories\AnimalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int|null $native_planet_id
 * @property int|null $parent_animal_id
 * @property int|null $hits
 * @property int|null $speed
 * @property BehaviorType|null $behavior_type
 * @property BehaviorSubtype|null $behavior_subtype
 * @property string|null $notes
 * @property string|null $image_path
 */
#[Fillable(['name', 'native_planet_id', 'parent_animal_id', 'hits', 'speed', 'behavior_type', 'behavior_subtype', 'notes', 'image_path'])]
class Animal extends Model
{
    /** @use HasFactory<AnimalFactory> */
    use HasFactory;

    protected $casts = [
        'behavior_type' => BehaviorType::class,
        'behavior_subtype' => BehaviorSubtype::class,
    ];

    /** @return BelongsTo<Planet, $this> */
    public function nativePlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'native_planet_id');
    }

    /** @return BelongsTo<Animal, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'parent_animal_id');
    }

    /** @return HasMany<Animal, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(Animal::class, 'parent_animal_id');
    }

    /** @return HasMany<AnimalSkill, $this> */
    public function skills(): HasMany
    {
        return $this->hasMany(AnimalSkill::class);
    }

    /** @return HasMany<AnimalAttack, $this> */
    public function attacks(): HasMany
    {
        return $this->hasMany(AnimalAttack::class);
    }

    /** @return HasMany<AnimalTrait, $this> */
    public function traits(): HasMany
    {
        return $this->hasMany(AnimalTrait::class);
    }
}
