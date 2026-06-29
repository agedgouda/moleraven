@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Moleraven" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center  text-accent-foreground">
            <x-app-logo-icon   />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Moleraven" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md text-accent-foreground">

        </x-slot>
    </flux:brand>
@endif
