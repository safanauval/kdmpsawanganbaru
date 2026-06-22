<x-layouts::app :title="__('Stok Barang')">
    <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        {{-- Livewire Component Stok Barang dengan fitur pencarian --}}
        <livewire:stok-barang-index />
    </div>
</x-layouts::app>