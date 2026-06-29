<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationForm
{
    private static array $types = [
        'Corporation' => 'Corporation',
        'Government' => 'Government',
        'Military' => 'Military',
        'Criminal' => 'Criminal',
        'Religious' => 'Religious',
        'Scout Service' => 'Scout Service',
        'Mercenary' => 'Mercenary',
        'Trade Guild' => 'Trade Guild',
        'Noble House' => 'Noble House',
        'Other' => 'Other',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('type')
                        ->options(self::$types)
                        ->searchable(),

                    TextInput::make('base_of_operations')
                        ->label('Base of Operations')
                        ->maxLength(255),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
