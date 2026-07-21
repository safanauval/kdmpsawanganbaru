<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Stok Barang</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola inventaris barang koperasi</p>
        </div>
    </div>

    {{-- Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari kode atau nama barang..."
                icon="magnifying-glass" clearable />
        </div>
        <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus">
            Tambah Barang
        </flux:button>
    </div>

    {{-- Tabel --}}
    <div class="relative overflow-hidden rounded-xl dark:border-neutral-700 p-1">
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
                <flux:table.column align="center">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($stokBarang as $item)
                    <flux:table.row wire:key="item-{{ $item->id }}">
                        <flux:table.cell class="py-2">
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
                        <flux:table.cell class="font-mono text-md font-medium">{{ $item->kode_barang }}</flux:table.cell>
                        <flux:table.cell>{{ $item->nama_barang }}</flux:table.cell>
                        <flux:table.cell class="text-neutral-600 dark:text-neutral-400">{{ $item->kategori->nama ?? '-' }}
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
                                    <flux:button wire:click="openEdit({{ $item->id }})" size="sm" icon="pencil-square">
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
                        <flux:table.cell colspan="9" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
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

    {{-- Modal Form Stok Barang --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit Stok Barang' : 'Tambah Stok Barang'" class="max-w-2xl"
        style="width: 900px;">
        <div class="space-y-4 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field class="md:col-span-2">
                    <flux:label>Gambar</flux:label>
                    <flux:input type="file" wire:model="gambar" accept="image/*" />
                </flux:field>
                <flux:field>
                    <flux:label>Kode Barang</flux:label>
                    <flux:input wire:model="kode_barang" required />
                    @error('kode_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Nama Barang</flux:label>
                    <flux:input wire:model="nama_barang" required />
                    @error('nama_barang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="kategori_id">
                        <option value="">- Pilih Kategori -</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Gudang</flux:label>
                    <flux:select wire:model="gudang_id">
                        <option value="">- Pilih Gudang -</option>
                        @foreach($gudangs as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
                <flux:field>
                    <flux:label>Stok</flux:label>
                    <flux:input type="number" wire:model="stok" min="0" required />
                </flux:field>
                <flux:field>
                    <flux:label>Satuan</flux:label>
                    <flux:input type="text" wire:model="satuan" list="satuan-list"
                        placeholder="Masukkan satuan (pcs, lusin, dus, kodi, dll)" required />
                    <datalist id="satuan-list">
                        <option value="pcs">Pcs (Per Biji)</option>
                        <option value="lusin">Lusin (12 Pcs)</option>
                        <option value="dus">Dus</option>
                        <option value="kodi">Kodi (20 Pcs)</option>
                        <option value="pack">Pack</option>
                        <option value="box">Box</option>
                        <option value="karton">Karton</option>
                        <option value="bal">Bal</option>
                        <option value="roll">Roll</option>
                        <option value="kg">Kg</option>
                        <option value="liter">Liter</option>
                        <option value="meter">Meter</option>
                        <option value="gram">Gram</option>
                    </datalist>
                    @error('satuan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Harga Beli (Rp)</flux:label>
                    <flux:input type="number" wire:model="harga_beli" min="0" required />
                </flux:field>
                <flux:field>
                    <flux:label>Harga Jual (Rp)</flux:label>
                    <flux:input type="number" wire:model="harga_jual" min="0" required />
                </flux:field>
                <flux:field class="md:col-span-2">
                    <flux:label>Deskripsi</flux:label>
                    <flux:textarea wire:model="deskripsi" rows="2" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700 py-2">
                <flux:button wire:click="$set('showModal', false)" color="red" variant="danger">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" color="blue">{{ $editMode ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
