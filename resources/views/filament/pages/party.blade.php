<x-filament-panels::page>
    @php $characters = $this->getCurrentCharacters() @endphp

    @if ($characters->isEmpty())
        <div class="text-center text-gray-500 dark:text-gray-400 py-12">
            No current characters yet. Players can set a current character from the My Characters page.
        </div>
    @else
        <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5 text-start">
                <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr>
                        <th class="fi-ta-header-cell px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Player</th>
                        <th class="fi-ta-header-cell px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Character</th>
                        <th class="fi-ta-header-cell px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">UPP</th>
                        <th class="fi-ta-header-cell px-4 py-3 text-left text-sm font-semibold text-gray-950 dark:text-white">Last Known Planet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                    @foreach ($characters as $character)
                        <tr
                            class="fi-ta-row cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-white/5"
                            onclick="window.location='{{ \App\Filament\Resources\Characters\CharacterResource::getUrl('edit', ['record' => $character]) }}'"
                        >
                            <td class="fi-ta-cell px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $character->user->name }}
                            </td>
                            <td class="fi-ta-cell px-4 py-3 text-sm font-medium text-gray-950 dark:text-white">
                                {{ $character->name }}
                            </td>
                            <td class="fi-ta-cell px-4 py-3 font-mono text-sm tracking-widest text-primary-600 dark:text-primary-400">
                                {{ $character->uppString() }}
                            </td>
                            <td class="fi-ta-cell px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $character->lastKnownPlanet?->display_label ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
