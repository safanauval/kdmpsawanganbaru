@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand variant="strong" name="SIMKOPDES" {{ $attributes }}>
        <x-slot name="logo" class="flex size-8 aspect-square items-center justify-center rounded-md text-white text-base font-bold">
            <x-app-logo-icon />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand variant="strong" {{ $attributes }}>
        <x-slot name="logo" class="flex size-8 aspect-square items-center justify-center rounded-md">
            <x-app-logo-icon />
        </x-slot>
    </flux:brand>
   <flux:heading size="base" level="1" variant="strong" class="lg-2 dark:text-white">
        SIMKOPDES
    </flux:heading>
@endif
