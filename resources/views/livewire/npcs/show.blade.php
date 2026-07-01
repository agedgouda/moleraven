<div class="flex h-full w-full flex-1 flex-col gap-6 py-6 pl-20 pr-6">
    <div class="flex items-center gap-3">
        <x-entity-icon :model="$npc" class="h-10 w-10 rounded-full" />
        <flux:heading size="xl">{{ $npc->name }}</flux:heading>
        <span class="font-mono text-lg tracking-widest text-zinc-500 dark:text-zinc-400">{{ $npc->uppString() }}</span>
    </div>

    {{-- Identity: full width with image on the right --}}
    <div class="flex gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <div class="flex-1 space-y-3">
            <flux:heading size="lg">Identity</flux:heading>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if ($npc->homeworld)
                    <div class="text-zinc-500 dark:text-zinc-400">Homeworld</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $npc->homeworld->display_label }}</div>
                @endif
                @if ($npc->lastKnownPlanet)
                    <div class="text-zinc-500 dark:text-zinc-400">Last Known Planet</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $npc->lastKnownPlanet->display_label }}</div>
                @endif
                @if ($npc->age)
                    <div class="text-zinc-500 dark:text-zinc-400">Age</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $npc->age }}</div>
                @endif
            </div>
        </div>
        <img src="{{ $npc->image_path ? asset('storage/' . $npc->image_path) : asset('images/tas.svg') }}"
             alt="{{ $npc->name }}" class="h-40 w-40 rounded-lg object-cover shrink-0">
    </div>

    {{-- Two columns: connections | UPP + skills --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            {{-- Connections --}}
            @if ($this->connections->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="px-6 pt-6 pb-4">
                        <flux:heading size="lg">Connections</flux:heading>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="border-b border-zinc-100 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Name</th>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Type</th>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Relation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach ($this->connections as $conn)
                                <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} {{ $conn['route'] ? 'cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/40' : '' }}"
                                    @if ($conn['route']) onclick="window.location='{{ $conn['route'] }}'" @endif>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center gap-2 font-medium text-zinc-800 dark:text-zinc-200">
                                            <x-entity-icon :model="$conn['model']" />
                                            {{ $conn['name'] }}
                                        </span>
                                        @if ($conn['notes'])
                                            <div class="truncate text-xs text-zinc-400">{{ $conn['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $conn['label'] }}</td>
                                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $conn['relation'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Notes --}}
            @if ($npc->notes)
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-3">Notes</flux:heading>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $npc->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Right column: UPP + Skills --}}
        <div class="space-y-4">
            {{-- UPP --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">UPP</flux:heading>
                </div>
                @foreach([['strength','Strength'],['dexterity','Dexterity'],['endurance','Endurance'],['intelligence','Intelligence'],['education','Education'],['social_standing','Social Standing']] as [$field, $label])
                    @php $val = $npc->$field; $dm = \App\Support\Mgt2::dm($val); @endphp
                    <div class="flex items-center gap-3 px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                        <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
                        <span class="w-8 text-right font-mono text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ \App\Support\Mgt2::uppHex($val) }}</span>
                        <span class="w-12 shrink-0 text-right text-xs {{ $dm > 0 ? 'text-green-600' : ($dm < 0 ? 'text-red-500' : 'text-zinc-400') }}">
                            DM {{ $dm >= 0 ? '+'.$dm : $dm }}
                        </span>
                    </div>
                @endforeach
                <div class="pb-2"></div>
            </div>

            {{-- Skills --}}
            @if ($this->skills->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="px-6 pt-6 pb-4">
                        <flux:heading size="lg">Skills</flux:heading>
                    </div>
                    <div class="pb-2">
                        @foreach ($this->skills as $skill)
                            <div class="flex items-center justify-between px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                                <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $skill->name }}</span>
                                <span class="font-mono text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $skill->level }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
