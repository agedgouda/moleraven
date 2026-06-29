<?php

namespace Database\Factories;

use App\Models\Planet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Planet>
 */
class PlanetFactory extends Factory
{
    private static array $sectors = [
        'Spinward Marches', 'Trojan Reach', 'Reft', 'Deneb', 'Corridor',
        'Vland', 'Lishun', 'Core', 'Dagudashaag', 'Antares',
    ];

    public function definition(): array
    {
        return [
            'sector' => fake()->randomElement(self::$sectors),
            'hex' => str_pad((string) fake()->numberBetween(101, 4040), 4, '0', STR_PAD_LEFT),
            'notes' => null,
        ];
    }
}
