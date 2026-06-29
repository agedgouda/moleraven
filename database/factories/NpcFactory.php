<?php

namespace Database\Factories;

use App\Models\Npc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Npc>
 */
class NpcFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'strength' => fake()->numberBetween(1, 15),
            'dexterity' => fake()->numberBetween(1, 15),
            'endurance' => fake()->numberBetween(1, 15),
            'intelligence' => fake()->numberBetween(1, 15),
            'education' => fake()->numberBetween(1, 15),
            'social_standing' => fake()->numberBetween(1, 15),
            'age' => fake()->optional()->numberBetween(20, 70),
            'homeworld_planet_id' => null,
            'last_known_planet_id' => null,
            'notes' => null,
        ];
    }
}
