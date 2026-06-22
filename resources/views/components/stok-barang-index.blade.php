<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Stok Barang</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola inventaris barang koperasi</p>
        </div>
    </div>
    {{-- Pencarian --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari kode atau nama barang..."
                    icon="magnifying-glass" clearable />
            </div>
            <flux:button wire:click="{{ route('stok-barang.create') }}" variant="primary" color="blue" icon="plus">
                Tambah Barang
            </flux:button>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="relative overflow-hidden rounded-xl dark:border-neutral-700 sm:p-8 p-1">
        <flux:table container:class="max-h-[600px]">
            <flux:table.columns sticky>
                <flux:table.column>Gambar</flux:table.column>
                <flux:table.column>Kode</flux:table.column>
                <flux:table.column>Nama Barang</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column>Gudang</flux:table.column>
                <flux:table.column align="end">Stok</flux:table.column>
                <flux:table.column align="end">Harga Beli</flux:table.column>
                <flux:table.column align="end">Harga Jual</flux:table.column>
                <flux:table.column align="end">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($stokBarang as $item)
                    <flux:table.row wire:key="item-{{ $item->id }}">
                        <flux:table.cell>
                            <div
                                class="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                                @if($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_barang }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono text-md font-medium">
                            {{ $item->kode_barang }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $item->nama_barang }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $item->kategori->nama ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">
                            {{ $item->gudang->nama_gudang ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <span @class([
                                'font-medium',
                                'text-red-600 dark:text-red-400' => $item->stok == 0,
                                'text-yellow-600 dark:text-yellow-400' => $item->stok > 0 && $item->stok <= 10,
                                'text-green-600 dark:text-green-400' => $item->stok > 10,
                            ])>
                                {{ $item->stok }}
                            </span>
                            <span class="text-xs text-neutral-500 ml-1">{{ $item->satuan }}</span>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell align="end" variant="strong">
                            Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button.group>
                                    <flux:button href="{{ route('stok-barang.edit', $item) }}" size="sm"
                                        icon="pencil-square" wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $item->id }})" wire:confirm="Yakin hapus barang ini?"
                                        size="sm" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </flux:button.group>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                            @if($search)
                                Tidak ditemukan barang dengan kata kunci "{{ $search }}".
                            @else
                                Belum ada data stok barang.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($stokBarang->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $stokBarang->links() }}
            </div>
        @endif
    </div>
</div>