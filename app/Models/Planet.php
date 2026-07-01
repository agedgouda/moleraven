<?php

namespace App\Models;

use App\Support\TravellerMap;
use Database\Factories\PlanetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $sector
 * @property string $hex
 * @property string|null $notes
 * @property-read string $name
 * @property-read string $display_label
 */
#[Fillable(['sector', 'hex', 'notes'])]
class Planet extends Model
{
    /** @use HasFactory<PlanetFactory> */
    use HasFactory;

    /** @return Attribute<string, never> */
    protected function name(): Attribute
    {
        return Attribute::get(fn () => $this->display_label);
    }

    /** @return Attribute<string, never> */
    protected function displayLabel(): Attribute
    {
        return Attribute::get(function () {
            $data = TravellerMap::getWorldData($this->sector, $this->hex);

            return $data ? "{$data['Name']} ({$this->hex})" : "$this->sector / $this->hex";
        });
    }
}
