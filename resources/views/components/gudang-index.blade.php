<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Gudang</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Daftar gudang, alamat, dan contact person</p>
        </div>
    </div>
    {{-- Pencarian --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1">
                <flux:input icon="magnifying-glass" wire:model.live.debounce.100ms="search"
                    placeholder="Cari gudang..." />
            </div>
            <flux:button variant="primary" color="blue" icon="plus" wire:click="openCreate">Tambah Gudang</flux:button>
        </div>
    </div>

    {{-- Tabel Gudang --}}
    <div class="bg-white dark:bg-zinc-800">
        <div class="relative overflow-hidden rounded-xl dark:border-neutral-700 p-1">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column>Kode Gudang</flux:table.column>
                    <flux:table.column>Nama Gudang</flux:table.column>
                    <flux:table.column>Telepon</flux:table.column>
                    <flux:table.column>Alamat</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>
                @forelse($gudang as $g)
                    <flux:table.row wire:key="gudang-{{ $g->id }}">
                        <flux:table.cell>{{ $g->kode_gudang }}</flux:table.cell>
                        <flux:table.cell>{{ $g->nama_gudang }}</flux:table.cell>
                        <flux:table.cell>{{ $g->telepon ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $g->alamat ?? '-' }}</flux:table.cell>
                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-2 py-2">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $g->id }})" size="sm" icon="pencil-square"
                                        wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $g->id }})" wire:confirm="Hapus gudang ini?"
                                        size="sm" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </flux:button.group>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-zinc-500">Belum ada data gudang.</td>
                    </tr>
                @endforelse
            </flux:table>
        </div>
        <div class="p-4">
            {{ $gudang->links() }}
        </div>
    </div>

    {{-- Modal Form gudang --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit gudang' : 'Tambah gudang'" style="width: 800px;">
        <form wire:submit="save" class="space-y-5">
            <flux:field>
                <flux:label>Kode gudang</flux:label>
                <flux:input wire:model="kode_gudang" required />
                @error('kode_gudang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <flux:field>
                <flux:label>Nama gudang</flux:label>
                <flux:input wire:model="nama_gudang" required />
                @error('nama_gudang') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Telepon</flux:label>
                    <flux:input wire:model="telepon" />
                    @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </flux:field>
            </div>
            <flux:field>
                <flux:label>Alamat</flux:label>
                <flux:textarea wire:model="alamat" rows="2" />
                @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <div class="flex justify-end gap-3 border-t border-zinc-200 dark:border-zinc-700"
                style="padding-top: 20px;">
                <flux:button type="button" variant="primary" color="red" wire:click="cancelModal">Batal</flux:button>
                <flux:button type="submit" variant="primary" color="blue">Perbarui</flux:button>
            </div>
        </form>
    </flux:modal>
</div>