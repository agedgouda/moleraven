<?php

namespace App\Support;

class Mgt2
{
    /** @var list<string> */
    public const SKILLS = [
        'Admin', 'Advocate',
        'Animals', 'Animals (Handling)', 'Animals (Training)', 'Animals (Veterinary)',
        'Art', 'Art (Holography)', 'Art (Instrument)', 'Art (Performer)', 'Art (Sculptor)', 'Art (Visual Media)', 'Art (Write)',
        'Astrogation',
        'Athletics', 'Athletics (Dexterity)', 'Athletics (Endurance)', 'Athletics (Strength)',
        'Broker', 'Carouse', 'Deception', 'Diplomat',
        'Drive', 'Drive (Hovercraft)', 'Drive (Mole)', 'Drive (Track)', 'Drive (Walker)', 'Drive (Wheel)',
        'Electronics', 'Electronics (Comms)', 'Electronics (Computers)', 'Electronics (Remote Ops)', 'Electronics (Sensors)',
        'Engineer', 'Engineer (J-drive)', 'Engineer (Life Support)', 'Engineer (M-drive)', 'Engineer (Power)',
        'Explosives',
        'Flyer', 'Flyer (Airship)', 'Flyer (Grav)', 'Flyer (Ornithopter)', 'Flyer (Rotor)', 'Flyer (Wing)',
        'Gambler',
        'Gun Combat', 'Gun Combat (Archaic)', 'Gun Combat (Energy)', 'Gun Combat (Slug)',
        'Gunner', 'Gunner (Capital)', 'Gunner (Ortillery)', 'Gunner (Screen)', 'Gunner (Turret)',
        'Heavy Weapons', 'Heavy Weapons (Artillery)', 'Heavy Weapons (Man Portable)', 'Heavy Weapons (Vehicle)',
        'Investigate', 'Jack-of-All-Trades', 'Language', 'Leadership', 'Mechanic', 'Medic', 'Navigation', 'Persuade',
        'Melee', 'Melee (Blade)', 'Melee (Bludgeon)', 'Melee (Natural)', 'Melee (Unarmed)',
        'Pilot', 'Pilot (Capital Ships)', 'Pilot (Small Craft)', 'Pilot (Spacecraft)',
        'Profession', 'Recon',
        'Science', 'Science (Archaeology)', 'Science (Astronomy)', 'Science (Biology)', 'Science (Chemistry)',
        'Science (Cosmology)', 'Science (Economics)', 'Science (Genetics)', 'Science (History)',
        'Science (Linguistics)', 'Science (Philosophy)', 'Science (Physics)', 'Science (Planetology)',
        'Science (Psionicology)', 'Science (Psychology)', 'Science (Robotics)', 'Science (Sophontology)',
        'Seafarer', 'Seafarer (Ocean Ships)', 'Seafarer (Personal)', 'Seafarer (Sail)', 'Seafarer (Submarine)',
        'Stealth', 'Steward', 'Streetwise', 'Survival',
        'Tactics', 'Tactics (Military)', 'Tactics (Naval)',
        'Vacc Suit',
    ];

    public static function dm(int $value): int
    {
        return match (true) {
            $value <= 0 => -3,
            $value <= 2 => -2,
            $value <= 5 => -1,
            $value <= 8 => 0,
            $value <= 11 => 1,
            $value <= 14 => 2,
            default => 3,
        };
    }

    public static function dmLabel(int $value): string
    {
        $dm = self::dm($value);

        return 'DM '.($dm >= 0 ? '+' : '').$dm;
    }

    public static function uppHex(int $value): string
    {
        return strtoupper(dechex(max(0, min(15, $value))));
    }

    /** @return array<int, string> */
    public static function statOptions(): array
    {
        return collect(range(0, 15))
            ->mapWithKeys(fn (int $v) => [$v => strtoupper(dechex($v))])
            ->all();
    }
}
