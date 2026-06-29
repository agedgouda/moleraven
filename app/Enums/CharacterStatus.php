<?php

namespace App\Enums;

enum CharacterStatus: string
{
    case Active = 'active';
    case Retired = 'retired';
    case Deceased = 'deceased';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Retired => 'Retired',
            self::Deceased => 'Deceased',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Retired => 'warning',
            self::Deceased => 'danger',
        };
    }
}
