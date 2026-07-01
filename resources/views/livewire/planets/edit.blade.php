<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('planets.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <span class="text-2xl font-semibold text-zinc-800 dark:text-white">{{ $planet->display_label }}</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Left: Form --}}
        <div class="space-y-6">
            <flux:field>
                <flux:label>Sector</flux:label>
                <flux:select wire:model.live="sector" placeholder="Select sector...">
                    @foreach($sectors as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="sector" />
            </flux:field>

            <flux:field>
                <flux:label>Hex</flux:label>
                <flux:select wire:model.live="hex" placeholder="{{ blank($sector) ? 'Select a sector first' : 'Select hex...' }}" :disabled="blank($sector)">
                    @foreach($this->hexOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="hex" />
            </flux:field>

            <flux:field>
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="notes" rows="4" placeholder="Campaign notes about this world..." />
            </flux:field>

            <div class="flex gap-3">
                <flux:button wire:click="save" variant="primary">Save</flux:button>
                <flux:button href="{{ route('planets.index') }}" variant="ghost" wire:navigate>Back</flux:button>
            </div>
        </div>

        {{-- Right: World data from Travellermap --}}
        <div>
            @php $world = $this->worldData; @endphp
            @if (!$sector || !$hex)
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-700">
                    Select a sector and hex to load world data.
                </div>
            @elseif (!$world)
                <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-sm text-red-600 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                    World not found. Check the sector and hex code.
                </div>
            @else
                <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <img
                        src="https://travellermap.com/api/jumpmap?{{ http_build_query(['sector' => $sector, 'hex' => $hex, 'jump' => 1, 'style' => 'poster']) }}"
                        alt="Jump map for {{ $world['Name'] ?? $hex }}"
                        class="w-full rounded border border-zinc-200 dark:border-zinc-700"
                    >

                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span class="text-lg font-bold text-zinc-900 dark:text-white">{{ $world['Name'] ?? 'Unknown' }}</span>
                        <span class="font-mono text-base tracking-widest text-blue-600 dark:text-blue-400">{{ $world['UWP'] ?? '' }}</span>
                        @if (($world['Zone'] ?? '') === 'A')
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Amber Zone</span>
                        @elseif (($world['Zone'] ?? '') === 'R')
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Red Zone</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm text-zinc-700 dark:text-zinc-300">
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

                    @if (!empty($world['Remarks']))
                        <div class="flex flex-wrap gap-1">
                            @foreach (array_filter(explode(' ', $world['Remarks'])) as $code)
                                <span class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $code }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
