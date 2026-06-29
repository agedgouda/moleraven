<?php

namespace App\Models;

use App\Enums\OrganizationRelationshipType;
use Database\Factories\NpcOrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $npc_id
 * @property int $organization_id
 * @property OrganizationRelationshipType $relationship_type
 * @property string|null $notes
 */
#[Fillable(['npc_id', 'organization_id', 'relationship_type', 'notes'])]
class NpcOrganization extends Model
{
    /** @use HasFactory<NpcOrganizationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['relationship_type' => OrganizationRelationshipType::class];
    }

    public function npc(): BelongsTo
    {
        return $this->belongsTo(Npc::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
