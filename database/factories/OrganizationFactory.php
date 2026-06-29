<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    private static array $types = [
        'Corporation', 'Government', 'Military', 'Criminal', 'Religious',
        'Scout Service', 'Mercenary', 'Trade Guild', 'Noble House',
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(self::$types),
            'base_of_operations_planet_id' => null,
            'notes' => null,
        ];
    }
}
