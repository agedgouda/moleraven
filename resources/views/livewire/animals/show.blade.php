<div class="flex h-full w-full flex-1 flex-col gap-6 py-6 pl-20 pr-6">
    <div class="flex items-center gap-3">
        <x-entity-icon :model="$animal" class="h-10 w-10 rounded-full" />
        <flux:heading size="xl">
            {{ $animal->name }}
            @if ($animal->parent)
                <span class="text-base font-normal text-zinc-400">(variant of {{ $animal->parent->name }})</span>
            @endif
        </flux:heading>
    </div>

    {{-- Identity: full width with image on the right --}}
    <div class="flex gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <div class="flex-1 space-y-3">
            <flux:heading size="lg">Identity</flux:heading>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if ($animal->nativePlanet)
                    <div class="text-zinc-500 dark:text-zinc-400">Native Planet</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $animal->nativePlanet->display_label }}</div>
                @endif
                @if ($animal->hits)
                    <div class="text-zinc-500 dark:text-zinc-400">Hits</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $animal->hits }}</div>
                @endif
                @if ($animal->speed)
                    <div class="text-zinc-500 dark:text-zinc-400">Speed</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $animal->speed }}m</div>
                @endif
                @if ($animal->behavior_type)
                    <div class="text-zinc-500 dark:text-zinc-400">Behavior</div>
                    <div class="text-zinc-800 dark:text-zinc-200">
                        {{ $animal->behavior_type->label() }}
                        @if ($animal->behavior_subtype)
                            — {{ $animal->behavior_subtype->label() }}
                        @endif
                    </div>
                @endif
                @if ($animal->notes)
                    <div class="col-span-2 mt-2 text-zinc-700 dark:text-zinc-300">{{ $animal->notes }}</div>
                @endif
            </div>
        </div>
        <img src="{{ $animal->image_path ? asset('storage/' . $animal->image_path) : asset('images/tas.svg') }}"
             alt="{{ $animal->name }}" class="h-40 w-40 rounded-lg object-cover shrink-0">
    </div>

    {{-- 2x2 grid: skills | attacks / traits | variants --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

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

        {{-- Attacks --}}
        @if ($this->attacks->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Attacks</flux:heading>
                </div>
                <div class="pb-4">
                    @foreach ($this->attacks as $attack)
                        <div class="px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                            <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $attack->name }}</span>
                            <span class="ml-2 font-mono text-sm text-blue-600 dark:text-blue-400">{{ $attack->damage }}</span>
                            @if ($attack->attack_traits)
                                <span class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $attack->attack_traits }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Traits --}}
        @if ($this->animalTraits->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Traits</flux:heading>
                </div>
                <div class="pb-4">
                    @foreach ($this->animalTraits as $trait)
                        <div class="px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                            <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $trait->name }}</span>
                            @if ($trait->value)
                                <span class="ml-2 font-mono text-sm text-blue-600 dark:text-blue-400">{{ $trait->value }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Variants --}}
        @if ($this->variants->isNotEmpty())
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Variants</flux:heading>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach ($this->variants as $variant)
                            <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
                                <td class="px-6 py-3">
                                    <a href="{{ route('animals.show', $variant) }}" class="inline-flex items-center gap-2 font-medium text-zinc-800 hover:text-blue-600 dark:text-zinc-200">
                                        <x-entity-icon :model="$variant" />
                                        {{ $variant->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $variant->nativePlanet?->display_label ?? '—' }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $variant->hits ? 'Hits ' . $variant->hits : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
