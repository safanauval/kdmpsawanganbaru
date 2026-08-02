<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Menu Restoran</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola daftar makanan & minuman restoran</p>
        </div>
    </div>

    {{-- Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-2">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari kode atau nama menu..."
                icon="magnifying-glass" clearable />
        </div>
        <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus">
            Tambah Menu
        </flux:button>
    </div>

    {{-- Tabel Menu --}}
    <div class="relative overflow-hidden rounded-xl dark:border-neutral-700 p-1">
        <flux:table container:class="max-h-[600px]">
            <flux:table.columns sticky>
                <flux:table.column>Gambar Menu</flux:table.column>
                <flux:table.column>Kode</flux:table.column>
                <flux:table.column>Nama Menu</flux:table.column>
                <flux:table.column align="center">Estimasi Penyajian</flux:table.column>
                <flux:table.column align="end">Stok / Porsi</flux:table.column>
                <flux:table.column align="end">HPP</flux:table.column>
                <flux:table.column align="end">Harga Jual</flux:table.column>
                <flux:table.column align="center">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($menuResto as $item)
                    <flux:table.row wire:key="menu-{{ $item->id }}">
                        <flux:table.cell class="py-2">
                            <div
                                class="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden">
                                @if($item->gambar_url)
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_menu }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="font-mono">{{ $item->kode_menu }}</flux:table.cell>
                        <flux:table.cell class="font-bold">{{ $item->nama_menu }}</flux:table.cell>
                        <flux:table.cell align="center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                ⏱️ ~{{ $item->estimasi_waktu ?? 15 }} Menit
                            </span>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <span class="font-medium text-green-600 dark:text-green-400">
                                {{ $item->stok }}
                            </span>
                            <span class="text-xs text-neutral-500 ml-1">{{ $item->satuan }}</span>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            Rp {{ number_format($item->harga_hpp ?? 0, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell align="end" variant="strong">
                            Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-2">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $item->id }})" size="sm" icon="pencil-square">
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $item->id }})" wire:confirm="Yakin hapus menu ini?"
                                        size="sm" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </flux:button.group>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        {{-- PERBAIKAN: Colspan diubah menjadi 8 --}}
                        <flux:table.cell colspan="8" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                            @if($search)
                                Tidak ditemukan menu dengan kata kunci "{{ $search }}".
                            @else
                                Belum ada data menu restoran.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if(method_exists($menuResto, 'hasPages') && $menuResto->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $menuResto->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form Menu Resto --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit Menu Resto' : 'Tambah Menu Resto'" class="max-w-2xl" style="width: 800px;">
        <div class="space-y-4 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field class="md:col-span-2">
                    <flux:label>Gambar Menu</flux:label>
                    <flux:input type="file" wire:model="gambar" accept="image/*" />
                    @error('gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Kode Menu</flux:label>
                    <flux:input wire:model="kode_menu" placeholder="Contoh: MNU-001" required />
                    @error('kode_menu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Nama Menu</flux:label>
                    <flux:input wire:model="nama_menu" placeholder="Contoh: Nasi Goreng Special" required />
                    @error('nama_menu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Kategori</flux:label>
                    <flux:select wire:model="kategori_id">
                        <option value="">- Pilih Kategori -</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </flux:select>
                    @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                {{-- Field Estimasi Waktu Penyajian --}}
                <flux:field>
                    <flux:label>Estimasi Penyajian (Menit)</flux:label>
                    <flux:input type="number" wire:model="estimasi_waktu" min="1" placeholder="15" required />
                    @error('estimasi_waktu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Stok / Porsi Harian</flux:label>
                    <flux:input type="number" wire:model="stok" min="0" required />
                    @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Satuan</flux:label>
                    <flux:input type="text" wire:model="satuan" list="satuan-resto-list" placeholder="Porsi" required />
                    <datalist id="satuan-resto-list">
                        <option value="porsi">Porsi</option>
                        <option value="piring">Piring</option>
                        <option value="mangkok">Mangkok</option>
                        <option value="gelas">Gelas</option>
                        <option value="paket">Paket</option>
                    </datalist>
                    @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Harga HPP / Modal (Rp)</flux:label>
                    <flux:input type="number" wire:model="harga_hpp" min="0" required />
                    @error('harga_hpp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Harga Jual (Rp)</flux:label>
                    <flux:input type="number" wire:model="harga" min="0" required />
                    @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>Deskripsi / Catatan Menu</flux:label>
                    <flux:textarea wire:model="deskripsi" rows="2" placeholder="Komposisi atau catatan menu..." />
                    @error('deskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700 py-2">
                <flux:button wire:click="$set('showModal', false)" color="red" variant="danger">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" color="blue">{{ $editMode ? 'Perbarui' : 'Simpan' }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>