<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        /* Opsional: efek glassmorphism untuk kartu form */
        .glass-card {
            background: rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .dark .glass-card {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="min-h-screen antialiased"
    style="background-image: url('{{ asset('img/logo/banner-desktop-hero.png') }}'); background-size: cover; background-position: center; backdrop-filter: blur(4px);">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col p-10 text-white lg:flex">
            <div class=" absolute inset-0"></div>
            <a href="{{ route('login') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                <img src="{{ asset('img/logo/simkopdes-white.png') }}" alt="Logo"
                    class="fill-current text-black dark:text-white" style="width: 80%; height: auto;" />
            </a>

            @php
                [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
            @endphp

            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                    <footer>
                        <flux:heading>{{ trim($author) }}</flux:heading>
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="glass-card lg:p-8" style="width: 450px; justify-self: center;">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <a href="{{ route('login') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden"
                    wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>

                    <span class="sr-only">{{ config('app.name', 'Simkopdes') }}</span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>