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

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function planets(): BelongsToMany
    {
        return $this->belongsToMany(Planet::class, 'diary_entry_planets');
    }

    public function npcs(): BelongsToMany
    {
        return $this->belongsToMany(Npc::class, 'diary_entry_npcs');
    }

    public function animals(): BelongsToMany
    {
        return $this->belongsToMany(Animal::class, 'diary_entry_animals');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'diary_entry_organizations');
    }
}
