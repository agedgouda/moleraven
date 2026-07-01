@props(['planet'])
@php
    $uwp = \App\Support\TravellerMap::getWorldData($planet->sector, $planet->hex)['UWP'] ?? null;
    $svg = \App\Support\PlanetImage::svg($uwp, $planet->sector, $planet->hex);
@endphp
<div {{ $attributes->merge(['class' => 'h-8 w-8 shrink-0']) }}>
    {!! $svg !!}
</div>
