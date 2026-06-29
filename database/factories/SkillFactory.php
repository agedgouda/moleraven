<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    private static array $skills = [
        'Admin', 'Advocate', 'Animals', 'Art', 'Astrogation', 'Athletics', 'Broker',
        'Carouse', 'Deception', 'Diplomat', 'Drive', 'Electronics', 'Engineer',
        'Explosives', 'Flyer', 'Gambler', 'Gunner', 'Gun Combat', 'Heavy Weapons',
        'Investigate', 'Jack-of-All-Trades', 'Language', 'Leadership', 'Mechanic',
        'Medic', 'Melee', 'Navigation', 'Persuade', 'Pilot', 'Profession',
        'Recon', 'Science', 'Seafarer', 'Stealth', 'Steward', 'Streetwise',
        'Survival', 'Tactics', 'Vacc Suit',
    ];

    public function definition(): array
    {
        return [
            'character_id' => Character::factory(),
            'name' => fake()->randomElement(self::$skills),
            'level' => fake()->numberBetween(0, 4),
        ];
    }
}
