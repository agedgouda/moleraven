<?php

namespace App\Support;

class PlanetImage
{
    public static function svg(?string $uwp, string $sector, string $hex): string
    {
        $seed = abs(crc32($sector.$hex));
        $id = 'p'.substr(md5($sector.$hex), 0, 8);

        [$base, $light, $dark, $atmoColor] = self::colors($uwp, $seed);

        $atmoGlow = $atmoColor
            ? "<circle cx='20' cy='20' r='19.5' fill='{$atmoColor}' opacity='0.3'/>"
            : '';

        $patches = self::patches($seed);

        return "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'>"
            .'<defs>'
            ."<radialGradient id='b-{$id}' cx='38%' cy='32%' r='70%'>"
            ."<stop offset='0%' stop-color='{$light}'/>"
            ."<stop offset='55%' stop-color='{$base}'/>"
            ."<stop offset='100%' stop-color='{$dark}'/>"
            .'</radialGradient>'
            ."<radialGradient id='s-{$id}' cx='68%' cy='62%' r='52%'>"
            ."<stop offset='20%' stop-color='transparent'/>"
            ."<stop offset='100%' stop-color='rgba(0,0,0,0.45)'/>"
            .'</radialGradient>'
            ."<clipPath id='c-{$id}'><circle cx='20' cy='20' r='17.5'/></clipPath>"
            .'</defs>'
            .$atmoGlow
            ."<circle cx='20' cy='20' r='17.5' fill='url(#b-{$id})'/>"
            ."<g clip-path='url(#c-{$id})'>{$patches}</g>"
            ."<circle cx='20' cy='20' r='17.5' fill='url(#s-{$id})'/>"
            .'</svg>';
    }

    /** @return array{0: string, 1: string, 2: string, 3: string|null} */
    private static function colors(?string $uwp, int $seed): array
    {
        if (! $uwp || strlen($uwp) < 4) {
            return ['#888888', '#aaaaaa', '#555555', null];
        }

        $size = hexdec($uwp[1]);
        $atmo = hexdec($uwp[2]);
        $hydro = hexdec($uwp[3]);

        if ($size === 0) {
            return ['#6b6b6b', '#999999', '#3a3a3a', null];
        }

        if ($atmo >= 11) {
            return ['#8a7018', '#b89838', '#504008', '#a09028'];
        }

        if ($atmo === 10) {
            return ['#c87020', '#e89840', '#805010', '#d88020'];
        }

        if ($atmo <= 1 && $hydro === 0) {
            return ['#6b6b6b', '#909090', '#3a3a3a', null];
        }

        if ($atmo <= 1) {
            return ['#a8ccd8', '#d8eef8', '#6a9cb8', '#9bbfd0'];
        }

        if ($hydro <= 1) {
            return ['#c08040', '#e0a860', '#805020', '#d09030'];
        }

        if ($hydro <= 4) {
            return ['#80a050', '#a8c870', '#507030', '#70a8c8'];
        }

        if ($hydro <= 7) {
            return ['#3870a8', '#5898d0', '#1a4870', '#80b8e8'];
        }

        return ['#1a509e', '#2e78c8', '#0a2860', '#60a0e8'];
    }

    private static function patches(int $seed): string
    {
        $out = '';
        for ($i = 0; $i < 3; $i++) {
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $cx = 6 + ($seed % 28);
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $cy = 6 + ($seed % 28);
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $rx = 3 + ($seed % 7);
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $ry = 2 + ($seed % 5);
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $rot = $seed % 360;
            $seed = (int) (($seed * 1103515245 + 12345) & 0x7FFFFFFF);
            $opacity = round(0.08 + ($seed % 12) / 100, 2);
            $out .= "<ellipse cx='{$cx}' cy='{$cy}' rx='{$rx}' ry='{$ry}' fill='white' opacity='{$opacity}' transform='rotate({$rot},{$cx},{$cy})'/>";
        }

        return $out;
    }
}
