<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class TravellerMap
{
    /** @var array<string, string> */
    private const SECTORS = [
        'Ley' => 'Ley',
        'Core' => 'Core',
        'Massilia' => 'Massilia',
        'Antares' => 'Antares',
        'Magyar' => 'Magyar',
        'Fornast' => 'Fornast',
        'Lishun' => 'Lishun',
        'Dagudashaag' => 'Dagudashaag',
        'Vland' => 'Vland',
        'Corridor' => 'Corridor',
        'Deneb' => 'Deneb',
        'Spinward Marches' => 'Spinward Marches',
        'Trojan Reach' => 'Trojan Reach',
        'Reft' => 'Reft',
        'Diaspora' => 'Diaspora',
        'Old Expanses' => 'Old Expanses',
        'Ilelish' => 'Ilelish',
        'Zarushagar' => 'Zarushagar',
        'Empty Quarter' => 'Empty Quarter',
        'Daibei' => 'Daibei',
        'Alpha Crucis' => 'Alpha Crucis',
        'Solomani Rim' => 'Solomani Rim',
        'Aldebaran' => 'Aldebaran',
        'Canopus' => 'Canopus',
        'Capella' => 'Capella',
        'Gateway' => 'Gateway',
        'Glimmerdrift Reaches' => 'Glimmerdrift Reaches',
        'Hinterworlds' => 'Hinterworlds',
        'Verge' => 'Verge',
        'Riftspan Reaches' => 'Riftspan Reaches',
        'Zhodane' => 'Zhodane',
        'Cronor' => 'Cronor',
        'Querion' => 'Querion',
        'Gvurrdon' => 'Gvurrdon',
        'Tuglikki' => 'Tuglikki',
        'Provence' => 'Provence',
        'Windhorn' => 'Windhorn',
        'Lair' => 'Lair',
        'Amdukan' => 'Amdukan',
        'Afawahisa' => 'Afawahisa',
        "Reaver's Deep" => "Reaver's Deep",
        'Dark Nebula' => 'Dark Nebula',
        'Ealiyasiyw' => 'Ealiyasiyw',
        'Etakhasoa' => 'Etakhasoa',
        'Hlakhoi' => 'Hlakhoi',
        'Iakr' => 'Iakr',
        'Khaukheairl' => 'Khaukheairl',
        'Kharir' => 'Kharir',
        'Uistilrao' => 'Uistilrao',
        'Yiklerzdanzh' => 'Yiklerzdanzh',
        'Spica' => 'Spica',
        'Far Frontiers' => 'Far Frontiers',
        'Ustral Quadrant' => 'Ustral Quadrant',
        'Kkree' => 'Kkree',
        'Sol' => 'Sol',
        'Terra' => 'Terra',
    ];

    /** @return array<string, string> */
    public static function sectors(): array
    {
        $sectors = self::SECTORS;
        ksort($sectors);

        return $sectors;
    }

    /** @return array<string, string> */
    public static function worldOptions(string $sector): array
    {
        if (blank($sector)) {
            return [];
        }

        return self::parseSectorData($sector)['options'];
    }

    /** @return array<string, string>|null */
    public static function getWorldData(string $sector, string $hex): ?array
    {
        if (blank($sector) || blank($hex)) {
            return null;
        }

        return self::parseSectorData($sector)['worlds'][$hex] ?? null;
    }

    /**
     * @return array{options: array<string, string>, worlds: array<string, array<string, string>>}
     */
    private static function parseSectorData(string $sector): array
    {
        $cacheKey = 'travellermap_sector_v2_'.md5($sector);

        return cache()->remember($cacheKey, now()->addHours(24), function () use ($sector): array {
            try {
                $response = Http::timeout(15)->get('https://travellermap.com/api/sec', [
                    'sector' => $sector,
                    'type' => 'TabDelimited',
                ]);

                if ($response->failed()) {
                    return ['options' => [], 'worlds' => []];
                }

                $lines = explode("\n", trim($response->body()));
                $idx = array_flip(explode("\t", (string) array_shift($lines)));

                if (! isset($idx['Hex'], $idx['Name'])) {
                    return ['options' => [], 'worlds' => []];
                }

                $options = [];
                $worlds = [];

                foreach ($lines as $line) {
                    if (blank($line)) {
                        continue;
                    }
                    $cols = explode("\t", $line);
                    $hex = $cols[$idx['Hex']] ?? '';
                    $name = $cols[$idx['Name']] ?? '';
                    if (! $hex || ! $name) {
                        continue;
                    }
                    $options[$hex] = "$name ($hex)";
                    $worlds[$hex] = [
                        'Name' => $name,
                        'UWP' => $cols[$idx['UWP']] ?? '',
                        'Bases' => $cols[$idx['Bases']] ?? '',
                        'Remarks' => $cols[$idx['Remarks']] ?? '',
                        'Zone' => $cols[$idx['Zone']] ?? '',
                        'PBG' => $cols[$idx['PBG']] ?? '',
                        'Allegiance' => $cols[$idx['Allegiance']] ?? '',
                        'Stars' => $cols[$idx['Stars']] ?? '',
                    ];
                }

                ksort($options);

                return ['options' => $options, 'worlds' => $worlds];
            } catch (\Exception) {
                return ['options' => [], 'worlds' => []];
            }
        });
    }
}
