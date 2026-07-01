<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 pl-6! pr-6!">
            <x-app-logo href="{{ route('home') }}" wire:navigate />
            <flux:spacer />
            @auth
                <flux:button href="{{ route('party') }}" variant="ghost" size="sm" wire:navigate>Dashboard</flux:button>
            @else
                <flux:button href="{{ route('login') }}" variant="ghost" size="sm" wire:navigate>Log in</flux:button>
                <flux:button href="{{ route('register') }}" variant="primary" size="sm" wire:navigate>Register</flux:button>
            @endauth
        </flux:header>
        {{ $slot }}
        @fluxScripts
    </body>
</html>
