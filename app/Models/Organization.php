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

    public function baseOfOperations(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'base_of_operations_planet_id');
    }

    public function characterMemberships(): HasMany
    {
        return $this->hasMany(CharacterOrganization::class);
    }

    public function npcMemberships(): HasMany
    {
        return $this->hasMany(NpcOrganization::class);
    }

    public function orgLinks(): HasMany
    {
        return $this->hasMany(OrganizationOrganization::class);
    }

    public function orgLinkedBy(): HasMany
    {
        return $this->hasMany(OrganizationOrganization::class, 'related_organization_id');
    }
}
