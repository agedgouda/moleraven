<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    private static array $items = [
        'Autopistol', 'Blade', 'Boarding Vacc Suit', 'Comm', 'Computer Tablet',
        'Flak Jacket', 'Hand Computer', 'Laser Pistol', 'Medical Kit', 'Mesh Armor',
        'Rifle', 'Scout/Courier', 'Shotgun', 'Stunner', 'TAS Membership',
        'Travellers\' Aid Society Passport',
    ];

    public function definition(): array
    {
        return [
            'character_id' => Character::factory(),
            'name' => fake()->randomElement(self::$items),
            'quantity' => fake()->numberBetween(1, 5),
            'description' => null,
        ];
    }
}
