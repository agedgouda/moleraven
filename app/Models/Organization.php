<?php

namespace App\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property string|null $base_of_operations
 * @property string|null $notes
 */
#[Fillable(['name', 'type', 'base_of_operations', 'notes'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    public function characterMemberships(): HasMany
    {
        return $this->hasMany(CharacterOrganization::class);
    }

    public function npcMemberships(): HasMany
    {
        return $this->hasMany(NpcOrganization::class);
    }
}
