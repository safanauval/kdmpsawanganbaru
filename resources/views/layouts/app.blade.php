{{-- resources/views/layouts/role-app.blade.php --}}
@php
    $role = auth()->user()->role ?? 'guest';
@endphp

@if($role === 'admin')
    <x-layouts::app.admin.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts::app.admin.sidebar>
@elseif($role === 'kasir')
    <x-layouts::app.kasir.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts::app.kasir.sidebar>
@endif