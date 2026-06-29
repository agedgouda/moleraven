<?php

namespace App\Models;

use Database\Factories\CareerTermFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $character_id
 * @property string $career
 * @property string|null $assignment
 * @property int $term
 * @property int $rank
 * @property string|null $rank_title
 * @property string|null $notes
 */
#[Fillable(['character_id', 'career', 'assignment', 'term', 'rank', 'rank_title', 'notes'])]
class CareerTerm extends Model
{
    /** @use HasFactory<CareerTermFactory> */
    use HasFactory;

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
