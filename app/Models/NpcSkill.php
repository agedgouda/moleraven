<?php

namespace App\Models;

use Database\Factories\NpcSkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $npc_id
 * @property string $name
 * @property int $level
 */
#[Fillable(['npc_id', 'name', 'level'])]
class NpcSkill extends Model
{
    /** @use HasFactory<NpcSkillFactory> */
    use HasFactory;

    /** @return BelongsTo<Npc, $this> */
    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }
}
