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
 * @property-read Character $character
 * @property-read Npc $npc
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

    /** @return BelongsTo<Character, $this> */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /** @return BelongsTo<Npc, $this> */
    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }
}
