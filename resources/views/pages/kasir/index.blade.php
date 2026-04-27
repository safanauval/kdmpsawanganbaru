<x-layouts::app :title="__('Kasir')">
    <!-- <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        {{-- Pencarian --}}
        <div class="flex gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama barang..."
                    icon="magnifying-glass" clearable />
            </div>
        </div>

        <div
            class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-end gap-2">
                <a
                    class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Kasir
                </a>
            </div>
        </div>
    </div> -->
    <livewire:kasir />
</x-layouts::app>