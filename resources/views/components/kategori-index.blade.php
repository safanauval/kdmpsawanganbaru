<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })" class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Kategori</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola kategori produk</p>
        </div>
    </div>

    {{-- Pencarian --}}
    <div class="flex gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama kategori..."
                icon="magnifying-glass" clearable />
        </div>
        <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus">
            Tambah Kategori
        </flux:button>
    </div>

    <div class="relative justify-between overflow-hidden rounded-xl dark:border-neutral-700 sm:p-8 p-2">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-16">No</flux:table.column>
                <flux:table.column align="center">Nama Kategori</flux:table.column>
                <flux:table.column align="center">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($kategori as $kat)
                    <flux:table.row>
                        <flux:table.cell class="text-neutral-500 dark:text-neutral-400" >
                            {{ ($kategori->currentPage() - 1) * $kategori->perPage() + $loop->iteration }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong" class="text-center">
                            {{ $kat->nama }}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-2 py-2">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $kat->id }})" color="blue" size="sm"
                                        icon="pencil-square" wire:navigate>
                                        Edit
                                    </flux:button >
                                    <form action="{{ route('kategori.destroy', $kat) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" size="sm" icon="trash" variant="danger">
                                            Hapus
                                        </flux:button>
                                    </form>
                                </flux:button.group>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                            Belum ada data kategori.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($kategori->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $kategori->links() }}
            </div>
        @endif

        {{-- MODAL CREATE KATEGORI --}}
        <flux:modal wire:model="showCreateModal" title="Tambah Kategori" class="max-w-md" style="width: 200px;">
            <form wire:submit="store" class="space-y-5">
                <flux:field>
                    <flux:label>Nama Kategori</flux:label>
                    <flux:input wire:model="namaBaru" required autofocus />
                    @error('namaBaru') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>

                <div class="flex justify-end gap-3 border-t border-zinc-200 dark:border-zinc-700"
                    style="padding-top: 20px;">
                    <flux:button type="button" variant="primary" color="red" wire:click="cancelCreate">Batal
                    </flux:button>
                    <flux:button type="submit" variant="primary" color="blue">Simpan</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- MODAL EDIT KATEGORI --}}
        <flux:modal wire:model="showEditModal" title="Edit Kategori" class="max-w-md" style="width: 200px;">
            <form wire:submit="update" class="space-y-5">
                <flux:field>
                    <flux:label>Nama Kategori</flux:label>
                    <flux:input wire:model="nama" required autofocus />
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>
                <div class="flex justify-end gap-3 border-t border-zinc-200 dark:border-zinc-700"
                    style="padding-top: 20px;">
                    <flux:button type="button" variant="primary" color="red" wire:click="cancelEdit">Batal</flux:button>
                    <flux:button type="submit" variant="primary" color="blue">Perbarui</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>
</div>