<?php

namespace App\Enums;

enum NpcRelationshipType: string
{
    case Ally = 'ally';
    case Contact = 'contact';
    case Rival = 'rival';
    case Enemy = 'enemy';

    public function label(): string
    {
        return match ($this) {
            self::Ally => 'Ally',
            self::Contact => 'Contact',
            self::Rival => 'Rival',
            self::Enemy => 'Enemy',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ally => 'success',
            self::Contact => 'info',
            self::Rival => 'warning',
            self::Enemy => 'danger',
        };
    }
}
