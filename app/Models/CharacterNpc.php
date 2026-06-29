<?php

namespace App\Models;

use App\Enums\NpcRelationshipType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $character_id
 * @property int $npc_id
 * @property NpcRelationshipType $relationship_type
 * @property string|null $notes
 */
#[Fillable(['character_id', 'npc_id', 'relationship_type', 'notes'])]
class CharacterNpc extends Model
{
    protected function casts(): array
    {
        return [
            'relationship_type' => NpcRelationshipType::class,
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }
}
