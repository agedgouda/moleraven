<?php

namespace Database\Factories;

use App\Models\CareerTerm;
use App\Models\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareerTerm>
 */
class CareerTermFactory extends Factory
{
    private static array $careers = ['Navy', 'Marines', 'Scout', 'Army', 'Merchant', 'Agent', 'Noble', 'Rogue', 'Entertainer', 'Scholar'];

    public function definition(): array
    {
        return [
            'character_id' => Character::factory(),
            'career' => fake()->randomElement(self::$careers),
            'assignment' => null,
            'term' => fake()->numberBetween(1, 4),
            'rank' => fake()->numberBetween(0, 6),
            'rank_title' => null,
            'notes' => null,
        ];
    }
}
