@php
    $sector = $get('sector');
    $hex = $get('hex');
    $world = null;

    if ($sector && $hex) {
        $world = \App\Filament\Resources\Planets\Schemas\PlanetForm::getWorldData($sector, $hex);
    }
@endphp

<div class="text-sm">
    @if (! $sector || ! $hex)
        <p class="text-gray-500 dark:text-gray-400">Enter sector and hex above to load world data from Travellermap.</p>
    @elseif (! $world)
        <p class="text-danger-600 dark:text-danger-400">World not found. Check the sector name and hex code.</p>
    @else
        <div class="space-y-3">
            {{-- Jump map image --}}
            <img
                src="https://travellermap.com/api/jumpmap?{{ http_build_query(['sector' => $sector, 'hex' => $hex, 'jump' => 1, 'style' => 'poster']) }}"
                alt="Jump map for {{ $world['Name'] ?? $hex }}"
                class="rounded border border-gray-200 dark:border-gray-700"
            >

            {{-- Name and UWP --}}
            <div class="flex items-baseline gap-4">
                <span class="text-lg font-semibold text-gray-950 dark:text-white">{{ $world['Name'] ?? 'Unknown' }}</span>
                <span class="font-mono text-base tracking-widest text-primary-600 dark:text-primary-400">{{ $world['UWP'] ?? '' }}</span>
                @if (($world['Zone'] ?? '') === 'A')
                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 ring-1 ring-amber-400">Amber Zone</span>
                @elseif (($world['Zone'] ?? '') === 'R')
                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800 ring-1 ring-red-400">Red Zone</span>
                @endif
            </div>

            {{-- Key stats --}}
            <div class="grid grid-cols-2 gap-x-8 gap-y-1 text-gray-700 dark:text-gray-300">
                @if (!empty($world['Allegiance']))
                    <div><span class="font-medium">Allegiance:</span> {{ $world['Allegiance'] }}</div>
                @endif
                @if (!empty($world['Bases']))
                    <div><span class="font-medium">Bases:</span> {{ $world['Bases'] }}</div>
                @endif
                @if (!empty($world['PBG']))
                    <div><span class="font-medium">PBG:</span> {{ $world['PBG'] }}</div>
                @endif
                @if (!empty($world['Stars']))
                    <div><span class="font-medium">Stars:</span> {{ $world['Stars'] }}</div>
                @endif
            </div>

            {{-- Trade codes / remarks --}}
            @if (!empty($world['Remarks']))
                <div class="flex flex-wrap gap-1">
                    @foreach (array_filter(explode(' ', $world['Remarks'])) as $code)
                        <span class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $code }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
