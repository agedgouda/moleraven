<?php

namespace App\Enums;

enum BehaviorSubtype: string
{
    // Carnivore
    case Chaser = 'chaser';
    case Killer = 'killer';
    case Pouncer = 'pouncer';
    case Siren = 'siren';
    case Trapper = 'trapper';

    // Herbivore
    case Filter = 'filter';
    case Gatherer = 'gatherer';
    case Grazer = 'grazer';
    case Intermittent = 'intermittent';

    // Omnivore
    case Eater = 'eater';
    case Hunter = 'hunter';

    // Scavenger
    case CarrionEater = 'carrion_eater';
    case Hijacker = 'hijacker';
    case Intimidator = 'intimidator';
    case Reducer = 'reducer';

    public function label(): string
    {
        return match ($this) {
            self::Chaser => 'Chaser',
            self::Killer => 'Killer',
            self::Pouncer => 'Pouncer',
            self::Siren => 'Siren',
            self::Trapper => 'Trapper',
            self::Filter => 'Filter',
            self::Gatherer => 'Gatherer',
            self::Grazer => 'Grazer',
            self::Intermittent => 'Intermittent',
            self::Eater => 'Eater',
            self::Hunter => 'Hunter',
            self::CarrionEater => 'Carrion-eater',
            self::Hijacker => 'Hijacker',
            self::Intimidator => 'Intimidator',
            self::Reducer => 'Reducer',
        };
    }

    /** @return BehaviorSubtype[] */
    public static function forType(BehaviorType $type): array
    {
        return match ($type) {
            BehaviorType::Carnivore => [self::Chaser, self::Killer, self::Pouncer, self::Siren, self::Trapper],
            BehaviorType::Herbivore => [self::Filter, self::Gatherer, self::Grazer, self::Intermittent],
            BehaviorType::Omnivore => [self::Eater, self::Gatherer, self::Hunter],
            BehaviorType::Scavenger => [self::CarrionEater, self::Hijacker, self::Intimidator, self::Reducer],
        };
    }
}
