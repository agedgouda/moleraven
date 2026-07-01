@props([
    'name',
    'upp' => null,
    'imagePath' => null,
    'imageAlt' => '',
])

<div class="flex h-full w-full flex-1 flex-col gap-6 py-6 pl-20 pr-6">
    {{-- Identity box --}}
    <div class="flex gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <div class="flex-1 space-y-3">
            <div>
                <flux:heading size="xl">{{ $name }}</flux:heading>
                @isset($subtitle)
                    {{ $subtitle }}
                @endisset
                @if ($upp)
                    <div class="font-mono text-sm tracking-widest text-zinc-500 dark:text-zinc-400">{{ $upp }}</div>
                @endif
            </div>
            @isset($identity)
                {{ $identity }}
            @endisset
        </div>
        <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/tas.svg') }}"
             alt="{{ $imageAlt ?: $name }}" class="h-40 w-40 rounded-lg object-cover shrink-0">
    </div>

    {{ $slot }}
</div>
