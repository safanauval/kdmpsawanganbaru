{{-- resources/views/layouts/role-app.blade.php --}}
@php
    $role = auth()->user()->role ?? 'guest';
@endphp

@if($role === 'admin')
    <x-layouts::app.admin.sidebar :title="$title ?? null">
        <main style="padding-left: 2rem; padding-right: 2rem;"
            class="[grid-area:main] p-2 lg:p-2 [[data-flux-container]_&amp;]:px-2" data-flux-main="">
            {{ $slot }}
        </main>
    </x-layouts::app.admin.sidebar>
@elseif($role === 'kasir')
    <x-layouts::app.kasir.sidebar :title="$title ?? null">
        <main style="padding-left: 2rem; padding-right: 2rem;"
            class="[grid-area:main] p-2 lg:p-2 [[data-flux-container]_&amp;]:px-2" data-flux-main="">
            {{ $slot }}
        </main>
    </x-layouts::app.kasir.sidebar>
@endif