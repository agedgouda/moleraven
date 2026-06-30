<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $animal_id
 * @property string $name
 * @property string|null $value
 */
#[Fillable(['animal_id', 'name', 'value'])]
class AnimalTrait extends Model
{
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
