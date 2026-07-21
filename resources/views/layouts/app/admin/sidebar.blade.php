<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<link rel="stylesheet" href="">

<head>
    @include('partials.head')
    @fluxAppearance
    <!-- FlyonUI Chart -->
    <link rel="stylesheet" href="../path/to/apexcharts/apexcharts.css">
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
    {{-- HEADER UTAMA (Full Width) --}}
    <flux:header sticky collapsible="mobile"
        class="transparent border-b dark:bg-zinc-900 border-zinc-700 dark:border-zinc-700"
        style="backdrop-filter: blur(10px);">

        {{-- Logo di Header --}}
        <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

        <flux:spacer />

        {{-- Ikon Aksi (Search, Settings, Help) --}}
        <flux:navbar class="me-4">
            <flux:dropdown x-data align="end">
                <flux:button variant="subtle" square class="group " aria-label="Preferred color scheme">
                    <flux:icon.sun class="size-8" x-show="$flux.appearance === 'light'" variant="solid" />
                    <flux:icon.moon class="size-8" x-show="$flux.appearance === 'dark'" variant="solid" />
                    <flux:icon.moon class="size-8" x-show="$flux.appearance === 'system' && $flux.dark"
                        variant="solid" />
                    <flux:icon.sun class="size-8" x-show="$flux.appearance === 'system' && ! $flux.dark"
                        variant="solid" />
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
        class="border-r border-black-200 bg-black-900 dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.nav scrollable style="padding-bottom: 20px; height: calc(100% - 55px);" class="overflow-y-hidden">
            {{-- Grup Platform --}}
            <flux:sidebar.group :heading="__('Overview')" class="grid">
                <flux:sidebar.item icon="presentation-chart-bar" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Grup Transaksi --}}
            <flux:sidebar.group :heading="__('Transaksi')" class="grid">
                <flux:sidebar.item icon="calculator" :href="route('adminKasir')"
                    :current="request()->routeIs('adminKasir')" wire:navigate>
                    {{ __('Kasir') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" :href="route('riwayat-transaksi.index')"
                    :current="request()->routeIs('riwayat-transaksi')" wire:navigate>
                    {{ __('Riwayat Transaksi') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Grup Manajemen Data --}}
            <flux:sidebar.group :heading="__('Manajemen Data')" class="grid">
                <flux:sidebar.item icon="clipboard-document-list" :href="route('kategori.index')"
                    :current="request()->routeIs('kategori.*')" wire:navigate>
                    {{ __('Kategori') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="archive-box" :href="route('stok-barang.index')"
                    :current="request()->routeIs('stok-barang.*')" wire:navigate>
                    {{ __('Stok Barang') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="home" :href="route('gudang.index')" :current="request()->routeIs('gudang.*')"
                    wire:navigate>
                    {{ __('Gudang Supplier') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="shield-check" :href="route('users.index')"
                    :current="request()->routeIs('user.*')" wire:navigate>
                    {{ __('User') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('settings.index')"
                    :current="request()->routeIs('settings.*')" wire:navigate>
                    {{ __('Pengaturan') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Grup Menu Anggota --}}
            <div x-data="{ open: false }">
                <flux:button.group variant="outline" class="w-full justify-between">
                    <flux:button variant="subtle" size="sm" class="flex-1 justify-start" @click="open = !open">
                        <flux:text variant="subtle" style="font-weight: 500;"> {{ __('Menu Anggota') }}</flux:text>
                        <flux:icon.chevron-down style="margin-left: auto;" x-bind:class="{ 'rotate-180': open }"
                            class="size-4 transition-transform" />
                    </flux:button>
                </flux:button.group>
                <div x-show="open" class="mt-2 space-y-1">
                    <flux:sidebar.item icon="users" :href="route('anggota.index')"
                        :current="request()->routeIs('anggota.*')" wire:navigate>
                        {{ __('Anggota') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wallet" :href="route('simpanan.index')"
                        :current="request()->routeIs('simpanan.*')" wire:navigate>
                        {{ __('Simpanan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="banknotes" :href="route('pinjaman.index')" :current="request()->routeIs('admin.pinjaman.*')" wire:navigate>
                        Pinjaman
                    </flux:sidebar.item>
                </div>
            </div>
        </flux:sidebar.nav>


        <div class="fixed bottom-0 flex items-center border-black-200 dark:border-zinc-700 bg-black-900 dark:border-zinc-700 dark:bg-zinc-900"
            style="black; padding: 10px; width: 230px;">
            <flux:profile class="flex-1 leading-tight" :name="auth()->user()->name"
                :initials="auth()->user()->initials()" :chevron="false" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle" />
            </form>
        </div>
    </flux:sidebar>

    {{-- MAIN CONTENT (Area Dinamis) --}}
    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast position="top end" />
        <!-- Customize top padding for things like navbars... -->
        <flux:toast position="top end" class="pt-24" />
    </flux:toast.group>
    @endpersist

    @fluxScripts
    <!-- Midtrans Snap -->
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}">
        </script>
</body>

</html>