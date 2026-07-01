<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property int|null $base_of_operations_planet_id
 * @property string|null $notes
 * @property string|null $image_path
 */
#[Fillable(['name', 'type', 'base_of_operations_planet_id', 'notes', 'image_path'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    /** @return BelongsTo<Planet, $this> */
    public function baseOfOperations(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'base_of_operations_planet_id');
    }

    /** @return HasMany<CharacterOrganization, $this> */
    public function characterMemberships(): HasMany
    {
        return $this->hasMany(CharacterOrganization::class);
    }

    /** @return HasMany<NpcOrganization, $this> */
    public function npcMemberships(): HasMany
    {
        return $this->hasMany(NpcOrganization::class);
    }

    /** @return HasMany<OrganizationOrganization, $this> */
    public function orgLinks(): HasMany
    {
        return $this->hasMany(OrganizationOrganization::class);
    }

    /** @return HasMany<OrganizationOrganization, $this> */
    public function orgLinkedBy(): HasMany
    {
        return $this->hasMany(OrganizationOrganization::class, 'related_organization_id');
    }
}
