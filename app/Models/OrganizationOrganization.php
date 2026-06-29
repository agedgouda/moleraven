<?php

namespace App\Models;

use App\Enums\OrganizationRelationshipType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $organization_id
 * @property int $related_organization_id
 * @property OrganizationRelationshipType $relationship_type
 * @property string|null $notes
 */
#[Fillable(['organization_id', 'related_organization_id', 'relationship_type', 'notes'])]
class OrganizationOrganization extends Model
{
    protected function casts(): array
    {
        return ['relationship_type' => OrganizationRelationshipType::class];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function relatedOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'related_organization_id');
    }
}
