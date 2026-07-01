@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo" class="flex h-12 items-center gap-3">
            <x-app-logo-icon class="h-full w-auto" />
            <span class="text-2xl font-bold font-display truncate in-data-flux-sidebar-collapsed-desktop:hidden">Moleraven</span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Moleraven" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground">

        </x-slot>
    </flux:brand>
@endif
