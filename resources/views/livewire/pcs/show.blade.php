<x-entity-show :name="$character->name" :upp="$character->uppString()" :image-path="$character->image_path" :image-alt="$character->name">
    <x-slot name="identity">
        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            @if ($character->lastKnownPlanet)
                <div class="text-zinc-500 dark:text-zinc-400">Last Known Planet</div>
                <div class="text-zinc-800 dark:text-zinc-200">{{ $character->lastKnownPlanet->display_label }}</div>
            @endif
            @if ($character->homeworld)
                <div class="text-zinc-500 dark:text-zinc-400">Homeworld</div>
                <div class="text-zinc-800 dark:text-zinc-200">{{ $character->homeworld->display_label }}</div>
            @endif
            @if ($character->age)
                <div class="text-zinc-500 dark:text-zinc-400">Age</div>
                <div class="text-zinc-800 dark:text-zinc-200">{{ $character->age }}</div>
            @endif
            @if ($character->credits)
                <div class="text-zinc-500 dark:text-zinc-400">Credits</div>
                <div class="font-mono text-zinc-800 dark:text-zinc-200">Cr{{ number_format($character->credits) }}</div>
            @endif
        </div>
    </x-slot>

    {{-- Two columns: left (tabs + diary + notes) | right (UPP + skills) --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            {{-- Connections / Background / Inventory tabs --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700" x-data="{ tab: 'connections' }">
                <div class="border-b border-zinc-200 px-4 pb-1 dark:border-zinc-700">
                    <flux:navbar class="pb-2">
                        <flux:navbar.item :current="false" x-on:click="tab = 'connections'" x-bind:data-current="tab === 'connections' ? '' : null">Connections</flux:navbar.item>
                        <flux:navbar.item :current="false" x-on:click="tab = 'background'" x-bind:data-current="tab === 'background' ? '' : null">Background</flux:navbar.item>
                        <flux:navbar.item :current="false" x-on:click="tab = 'inventory'" x-bind:data-current="tab === 'inventory' ? '' : null">Inventory</flux:navbar.item>
                    </flux:navbar>
                </div>

                {{-- Connections --}}
                <div x-show="tab === 'connections'" class="p-6">
                    @if ($this->connections->isEmpty())
                        <flux:text class="text-sm text-zinc-400">No connections.</flux:text>
                    @else
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
                                    <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/40"
                                        onclick="window.location='{{ $conn['route'] }}'">
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
                    @endif
                </div>

                {{-- Background --}}
                <div x-show="tab === 'background'" class="p-6">
                    @if ($this->careerTerms->isEmpty())
                        <flux:text class="text-sm text-zinc-400">No background yet.</flux:text>
                    @else
                        <div class="overflow-hidden rounded-lg border border-zinc-100 dark:border-zinc-700">
                            <table class="w-full text-sm">
                                <thead class="bg-zinc-50 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Term</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Career</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Assignment</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Rank</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Title</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                    @foreach ($this->careerTerms as $term)
                                        <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                                            <td class="px-3 py-2 font-mono">{{ $term->term }}</td>
                                            <td class="px-3 py-2">{{ $term->career }}</td>
                                            <td class="px-3 py-2 text-zinc-500">{{ $term->assignment ?? '—' }}</td>
                                            <td class="px-3 py-2">{{ $term->rank }}</td>
                                            <td class="px-3 py-2 text-zinc-500">{{ $term->rank_title ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Inventory --}}
                <div x-show="tab === 'inventory'" class="space-y-3 p-6">
                    @if ($character->credits)
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">Credits: <span class="font-mono font-semibold text-zinc-800 dark:text-zinc-200">Cr{{ number_format($character->credits) }}</span></div>
                    @endif
                    @if ($this->inventoryItems->isEmpty())
                        <flux:text class="text-sm text-zinc-400">No inventory items.</flux:text>
                    @else
                        <div class="space-y-2">
                            @foreach ($this->inventoryItems as $item)
                                <div class="rounded-lg border border-zinc-100 px-3 py-2 dark:border-zinc-700 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $item->name }}</span>
                                    <span class="ml-2 text-sm text-zinc-400">× {{ $item->quantity }}</span>
                                    @if ($item->description)
                                        <div class="text-xs text-zinc-400">{{ $item->description }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Diary --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <flux:heading size="lg">Diary</flux:heading>
                    @auth
                        @if (auth()->id() === $character->user_id)
                            <flux:button href="{{ route('diary.create', $character) }}" icon="plus" variant="ghost" size="sm" class="ml-auto" wire:navigate />
                        @endif
                    @endauth
                </div>
                @forelse ($character->diaryEntries as $entry)
                    <a href="{{ route('home', ['entry_id' => $entry->id]) }}"
                       class="block px-4 py-3 hover:bg-zinc-100 dark:hover:bg-zinc-700/40 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                        @if ($entry->entry_date)
                            <span class="font-mono text-sm text-zinc-500">{{ $entry->entry_date }}</span>
                        @endif
                        @if ($entry->entry)
                            <span class="{{ $entry->entry_date ? 'ml-2' : '' }} text-sm text-zinc-700 dark:text-zinc-300">{{ Str::limit(strip_tags($entry->entry), 80) }}</span>
                        @endif
                    </a>
                @empty
                    <div class="px-4 py-3 text-sm text-zinc-400">No diary entries yet.</div>
                @endforelse
            </div>

            {{-- Notes --}}
            @if ($character->notes)
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-3">Notes</flux:heading>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $character->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Right column: UPP + Skills --}}
        <div class="space-y-4">
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">UPP</flux:heading>
                </div>
                @foreach([['strength','Strength'],['dexterity','Dexterity'],['endurance','Endurance'],['intelligence','Intelligence'],['education','Education'],['social_standing','Social Standing']] as [$field, $label])
                    @php $val = $character->$field; $dm = \App\Support\Mgt2::dm($val); @endphp
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
</x-entity-show>
