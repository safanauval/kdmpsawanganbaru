<x-layouts::app :title="__('Stok Barang')">
    <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Stok Barang</flux:heading>
                <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola inventaris barang koperasi</p>
            </div>
            <flux:button href="{{ route('stok-barang.create') }}" variant="primary" color="blue" icon="plus">
                Tambah Barang
            </flux:button>
        </div>

        {{-- Livewire Component Stok Barang dengan fitur pencarian --}}
        <livewire:stok-barang-index />
    </div>
</x-layouts::app>