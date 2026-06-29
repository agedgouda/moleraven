@props([
    'connections',
    'card' => true,
    'sortBy' => 'name',
    'sortDir' => 'asc',
])

@php
    $isEmpty = $connections->isEmpty();
    $sortIcon = fn (string $col) => $sortBy === $col ? ($sortDir === 'asc' ? '↑' : '↓') : '';
@endphp

@if ($card)
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between px-6 pt-6 pb-4">
            <flux:heading size="lg">Connections</flux:heading>
            <flux:button size="sm" icon="plus" wire:click="openConnectionModal">Add</flux:button>
        </div>

        <div class="px-6 pb-3">
            <flux:input wire:model.live="connectionSearch" placeholder="Filter..." icon="magnifying-glass" size="sm" clearable />
        </div>

        @if ($isEmpty)
            <div class="px-6 pb-6">
                <flux:text class="text-sm text-zinc-400">No connections found.</flux:text>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-zinc-100 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('name')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Name <span>{{ $sortIcon('name') }}</span></button>
                        </th>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('type')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Type <span>{{ $sortIcon('type') }}</span></button>
                        </th>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('relation')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Relation <span>{{ $sortIcon('relation') }}</span></button>
                        </th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($connections as $item)
                        @include('components.partials.connection-item', ['item' => $item])
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@else
    <div class="space-y-3">
        <div class="flex items-center gap-3">
            <div class="flex-1">
                <flux:input wire:model.live="connectionSearch" placeholder="Filter..." icon="magnifying-glass" size="sm" clearable />
            </div>
            <flux:button size="sm" icon="plus" wire:click="openConnectionModal">Add</flux:button>
        </div>

        @if ($isEmpty)
            <flux:text class="text-sm text-zinc-400">No connections found.</flux:text>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-zinc-100 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('name')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Name <span>{{ $sortIcon('name') }}</span></button>
                        </th>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('type')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Type <span>{{ $sortIcon('type') }}</span></button>
                        </th>
                        <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">
                            <button wire:click="sortConnections('relation')" class="flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">Relation <span>{{ $sortIcon('relation') }}</span></button>
                        </th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($connections as $item)
                        @include('components.partials.connection-item', ['item' => $item])
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endif
