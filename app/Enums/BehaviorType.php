<?php

namespace App\Enums;

enum BehaviorType: string
{
    case Carnivore = 'carnivore';
    case Herbivore = 'herbivore';
    case Omnivore = 'omnivore';
    case Scavenger = 'scavenger';

    public function label(): string
    {
        return match ($this) {
            self::Carnivore => 'Carnivore',
            self::Herbivore => 'Herbivore',
            self::Omnivore => 'Omnivore',
            self::Scavenger => 'Scavenger',
        };
    }
}
