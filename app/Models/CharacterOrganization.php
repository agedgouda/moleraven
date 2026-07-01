<?php

namespace App\Models;

use App\Enums\OrganizationRelationshipType;
use Database\Factories\CharacterOrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $character_id
 * @property int $organization_id
 * @property OrganizationRelationshipType $relationship_type
 * @property string|null $notes
 * @property-read Character $character
 * @property-read Organization $organization
 */
#[Fillable(['character_id', 'organization_id', 'relationship_type', 'notes'])]
class CharacterOrganization extends Model
{
    /** @use HasFactory<CharacterOrganizationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['relationship_type' => OrganizationRelationshipType::class];
    }

    /** @return BelongsTo<Character, $this> */
    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
