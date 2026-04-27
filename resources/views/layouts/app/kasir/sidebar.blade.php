<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    {{-- HEADER UTAMA (Full Width) --}}
    <flux:header sticky collapsible="mobile" class="transparent border border-zinc-700 dark:border-zinc-700"
        style="backdrop-filter: blur(10px);">

        {{-- Logo di Header --}}
        <x-app-logo href="{{ route('kasir') }}" wire:navigate />

        <flux:spacer />

        {{-- Ikon Aksi (Search, Settings, Help) --}}
        <flux:navbar class="me-4">
            <flux:dropdown x-data align="end">
                <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                    <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini"
                        class="text-zinc-500 dark:text-white" />
                    <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini"
                        class="text-zinc-500 dark:text-white" />
                    <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                    <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                    <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                    <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar>

        {{-- User Dropdown (Desktop) --}}
        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:profile :initials="auth()->user()->initials()" />

            <flux:menu>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer">
                        Logout
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>

        {{-- Mobile User Dropdown --}}
        <flux:dropdown position="top" align="end" class="lg:hidden">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                </div>

                <flux:menu.separator />

                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer">
                        Logout
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{-- SIDEBAR UTAMA (Navigasi Aplikasi) --}}
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-black-200 bg-black-500 dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.nav>
            {{-- Grup Platform (Dashboard) --}}
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('kasir')" :current="request()->routeIs('kasir')"
                    wire:navigate>
                    {{ __('Kasir') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Grup Transaksi --}}
        </flux:sidebar.nav>

        <flux:spacer />

        <div class="flex items-center">
            <flux:profile class="flex-1 leading-tight" :name="auth()->user()->name"
                :initials="auth()->user()->initials()" :chevron="false" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle" />
            </form>
        </div>
        </div>
    </flux:sidebar>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>