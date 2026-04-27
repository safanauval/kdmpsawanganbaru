<x-layouts::app :title="__('Kategori')">
    <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Kategori</flux:heading>
                <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola kategori produk</p>
            </div>
            <flux:button href="{{ route('kategori.create') }}" variant="primary" color="blue" icon="plus" wire:navigate>
                Tambah Kategori
            </flux:button>
        </div>

        <!-- Livewire Component Kategori  -->
        <livewire:kategori-index />
    </div>
</x-layouts::app>