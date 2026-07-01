<div class="py-6 pl-20 pr-6 space-y-8 max-w-7xl">
    @if ($this->latestEntry)
        @php $planet = $this->latestEntry->planets->first(); @endphp

        @if ($planet)
            @php
                $uwp = \App\Support\TravellerMap::getWorldData($planet->sector, $planet->hex)['UWP'] ?? null;
                $svg = \App\Support\PlanetImage::svg($uwp, $planet->sector, $planet->hex);
                $mapUrl = 'https://travellermap.com/?' . http_build_query(['sector' => $planet->sector, 'hex' => $planet->hex]);
                $jumpMapUrl = 'https://travellermap.com/api/jumpmap?' . http_build_query(['sector' => $planet->sector, 'hex' => $planet->hex, 'jump' => 1, 'style' => 'poster']);
            @endphp
            <div class="flex items-center justify-between gap-6 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-4">
                    <div class="h-20 w-20 shrink-0">{!! $svg !!}</div>
                    <div>
                        <div class="text-xl font-semibold text-zinc-900 dark:text-white">{{ $planet->display_label }}</div>
                        <div class="text-sm text-zinc-500">{{ $planet->sector }}</div>
                        @if ($uwp)
                            <div class="font-mono text-sm text-zinc-500">{{ $uwp }}</div>
                        @endif
                    </div>
                </div>
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="shrink-0 overflow-hidden rounded-lg border border-zinc-300 dark:border-zinc-600 hover:opacity-90 transition-opacity">
                    <img src="{{ $jumpMapUrl }}" alt="Map location of {{ $planet->display_label }}" class="h-32 w-32 object-cover" />
                </a>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <x-entity-icon :model="$this->latestEntry->character" class="h-9 w-9 shrink-0 rounded-full" />
            <div>
                <div class="font-semibold text-zinc-900 dark:text-white">{{ $this->latestEntry->character->name }}</div>
                @if ($this->latestEntry->entry_date)
                    <div class="font-mono text-sm text-zinc-500">{{ $this->latestEntry->entry_date }}</div>
                @endif
            </div>
            @auth
                @if (auth()->id() === $this->latestEntry->character->user_id)
                    <flux:button href="{{ route('diary.edit', $this->latestEntry) . '?return_to=' . urlencode(url()->current() . ($entryId ? '?entry_id=' . $entryId : '')) }}" icon="pencil" variant="ghost" size="sm" class="ml-auto" wire:navigate />
                @endif
            @endauth
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            {{-- Left: entry --}}
            <div class="lg:col-span-2">
                @if ($this->latestEntry->entry)
                    <div class="prose prose-zinc dark:prose-invert max-w-none rounded-lg border border-zinc-200 px-6 py-4 dark:border-zinc-700">
                        {!! $this->latestEntry->entry !!}
                    </div>
                @endif
            </div>

            {{-- Right: contacts --}}
            <div class="space-y-3">
                <flux:input wire:model.live="contactSearch" placeholder="Filter..." icon="magnifying-glass" size="sm" clearable />

                @if ($this->contacts->isNotEmpty())
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
                        <table class="w-full text-sm">
                            <thead class="border-b border-zinc-100 dark:border-zinc-700">
                                <tr>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                                        <button wire:click="sortContacts('name')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">
                                            Name <span>{{ $contactSortBy === 'name' ? ($contactSortDir === 'asc' ? '↑' : '↓') : '' }}</span>
                                        </button>
                                    </th>
                                    <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                                        <button wire:click="sortContacts('type')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">
                                            Type <span>{{ $contactSortBy === 'type' ? ($contactSortDir === 'asc' ? '↑' : '↓') : '' }}</span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                @foreach ($this->contacts as $contact)
                                    <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/40"
                                        onclick="window.location='{{ $contact['route'] }}'">
                                        <td class="px-4 py-2">
                                            <span class="inline-flex items-center gap-2 font-medium text-zinc-800 dark:text-zinc-200">
                                                <x-entity-icon :model="$contact['model']" />
                                                {{ $contact['name'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $contact['type'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif ($contactSearch !== '')
                    <flux:text class="text-sm text-zinc-400">No contacts found.</flux:text>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-700">
            <flux:button
                wire:click="goToPrevious"
                icon="arrow-left"
                variant="ghost"
                :disabled="$offset >= $this->totalEntries - 1"
            >Previous</flux:button>

            <span class="text-sm text-zinc-500">{{ $offset + 1 }} / {{ $this->totalEntries }}</span>

            <flux:button
                wire:click="goToNext"
                icon-trailing="arrow-right"
                variant="ghost"
                :disabled="$offset === 0"
            >Next</flux:button>
        </div>
    @else
        <p class="text-sm italic text-zinc-400">No diary entries yet.</p>
    @endif
</div>
