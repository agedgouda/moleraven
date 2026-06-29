<?php

namespace Database\Factories;

use App\Models\Npc;
use App\Models\NpcSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NpcSkill>
 */
class NpcSkillFactory extends Factory
{
    private static array $skills = [
        'Admin', 'Advocate', 'Astrogation', 'Athletics', 'Broker', 'Carouse',
        'Deception', 'Diplomat', 'Electronics', 'Engineer', 'Flyer', 'Gambler',
        'Gun Combat', 'Gunner', 'Investigate', 'Leadership', 'Mechanic', 'Medic',
        'Melee', 'Persuade', 'Pilot', 'Recon', 'Stealth', 'Streetwise', 'Survival',
        'Tactics', 'Vacc Suit',
    ];

    public function definition(): array
    {
        return [
            'npc_id' => Npc::factory(),
            'name' => fake()->randomElement(self::$skills),
            'level' => fake()->numberBetween(0, 4),
        ];
    }
}
