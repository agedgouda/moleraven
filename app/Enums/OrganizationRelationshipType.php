<?php

namespace App\Enums;

enum OrganizationRelationshipType: string
{
    case Member = 'member';
    case FormerMember = 'former_member';
    case Patron = 'patron';
    case Ally = 'ally';
    case Contact = 'contact';
    case Enemy = 'enemy';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Member',
            self::FormerMember => 'Former Member',
            self::Patron => 'Patron',
            self::Ally => 'Ally',
            self::Contact => 'Contact',
            self::Enemy => 'Enemy',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Member => 'success',
            self::FormerMember => 'gray',
            self::Patron => 'info',
            self::Ally => 'primary',
            self::Contact => 'warning',
            self::Enemy => 'danger',
        };
    }
}
