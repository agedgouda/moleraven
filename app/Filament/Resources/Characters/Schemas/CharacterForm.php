<?php

namespace App\Filament\Resources\Characters\Schemas;

use App\Enums\CharacterStatus;
use App\Filament\Resources\Planets\PlanetResource;
use App\Models\Planet;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CharacterForm
{
    /** @var list<string> */
    private const MGT2_SKILLS = [
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

    /** @return array<int, string> */
    private static function statOptions(): array
    {
        return collect(range(0, 15))
            ->mapWithKeys(fn (int $v) => [$v => strtoupper(dechex($v))])
            ->all();
    }

    private static function dm(int $value): int
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

    private static function dmHint(mixed $state): ?string
    {
        if ($state === null || $state === '') {
            return null;
        }

        $dm = self::dm((int) $state);

        return 'DM '.($dm >= 0 ? '+' : '').$dm;
    }

    private static function dmColor(mixed $state): string
    {
        $dm = self::dm((int) ($state ?? 7));

        return match (true) {
            $dm < 0 => 'danger',
            $dm > 0 => 'success',
            default => 'gray',
        };
    }

    private static function statSelect(string $field, string $label = ''): Select
    {
        return Select::make($field)
            ->label($label ?: ucfirst(str_replace('_', ' ', $field)))
            ->options(self::statOptions())
            ->default(7)
            ->required()
            ->live()
            ->hint(fn ($state) => self::dmHint($state))
            ->hintColor(fn ($state) => self::dmColor($state));
    }

    private static function viewPlanetAction(): Action
    {
        return Action::make('view_planet')
            ->label('View planet')
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->url(fn ($state): ?string => $state ? PlanetResource::getUrl('edit', ['record' => $state]) : null)
            ->hidden(fn ($state): bool => blank($state))
            ->openUrlInNewTab();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('homeworld_planet_id')
                            ->label('Homeworld')
                            ->options(fn () => Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id'))
                            ->searchable()
                            ->nullable()
                            ->hintAction(self::viewPlanetAction()),

                        Select::make('last_known_planet_id')
                            ->label('Last Known Planet')
                            ->options(fn () => Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id'))
                            ->searchable()
                            ->nullable()
                            ->hintAction(self::viewPlanetAction()),

                        TextInput::make('age')
                            ->numeric()
                            ->integer()
                            ->minValue(18)
                            ->maxValue(150)
                            ->default(18),

                        TextInput::make('credits')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Cr'),

                        Select::make('status')
                            ->options(collect(CharacterStatus::cases())->mapWithKeys(
                                fn (CharacterStatus $s) => [$s->value => $s->label()]
                            ))
                            ->default(CharacterStatus::Active->value)
                            ->required(),
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make('UPP — Universal Personality Profile')
                            ->columns(3)
                            ->schema([
                                self::statSelect('strength'),
                                self::statSelect('dexterity'),
                                self::statSelect('endurance'),
                                self::statSelect('intelligence'),
                                self::statSelect('education'),
                                self::statSelect('social_standing', 'Social Standing'),
                            ]),

                        Section::make('Skills')
                            ->schema([
                                Repeater::make('skills')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->datalist(self::MGT2_SKILLS)
                                            ->columnSpan(2),
                                        TextInput::make('level')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->maxValue(6)
                                            ->default(0)
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add skill')
                                    ->reorderableWithButtons()
                                    ->collapsible(),
                            ]),
                    ]),

                Section::make('Inventory')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('inventoryItems')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                                Textarea::make('description')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Add item')
                            ->reorderableWithButtons()
                            ->collapsible(),
                    ]),

                Section::make('Notes')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
