<?php

namespace Database\Factories;

use App\Enums\CharacterStatus;
use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'is_current' => false,
            'status' => CharacterStatus::Active,
            'strength' => fake()->numberBetween(1, 15),
            'dexterity' => fake()->numberBetween(1, 15),
            'endurance' => fake()->numberBetween(1, 15),
            'intelligence' => fake()->numberBetween(1, 15),
            'education' => fake()->numberBetween(1, 15),
            'social_standing' => fake()->numberBetween(1, 15),
            'age' => fake()->numberBetween(18, 60),
            'homeworld_planet_id' => null,
            'last_known_planet_id' => null,
            'credits' => fake()->numberBetween(0, 100000),
            'notes' => null,
        ];
    }

    public function current(): static
    {
        return $this->state(['is_current' => true]);
    }

    public function retired(): static
    {
        return $this->state(['status' => CharacterStatus::Retired, 'is_current' => false]);
    }

    public function deceased(): static
    {
        return $this->state(['status' => CharacterStatus::Deceased, 'is_current' => false]);
    }
}
