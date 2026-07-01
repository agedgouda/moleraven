<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\DiaryEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiaryEntry>
 */
class DiaryEntryFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->numberBetween(1100, 1110);
        $day = fake()->numberBetween(1, 365);

        return [
            'character_id' => Character::factory(),
            'entry_date' => sprintf('%d-%03d', $year, $day),
            'entry' => '<p>'.fake()->paragraph().'</p>',
        ];
    }
}
