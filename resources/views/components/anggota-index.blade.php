<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })" class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
            <flux:heading size="xl">Anggota Koperasi</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola data anggota</p>
        </div>
        <div>
            <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus" class="whitespace-nowrap">
                Tambah Anggota
            </flux:button>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode anggota..." />
        </div>
    </div>

    {{-- Tabel Anggota --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column align="start">Kode</flux:table.column>
                    <flux:table.column align="start">Nama</flux:table.column>
                    <flux:table.column align="start">Email</flux:table.column>
                    <flux:table.column align="center">Telepon</flux:table.column>
                    <flux:table.column align="center">Tanggal Masuk</flux:table.column>
                    <flux:table.column align="center">Total Simpanan</flux:table.column>
                    <flux:table.column align="center" >Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($anggotas as $a)
                        <flux:table.row wire:key="anggota-{{ $a->id_anggota }}">
                            <flux:table.cell align="start" class="font-mono font-semibold py-2">
                                {{ $a->kode_anggota }}
                            </flux:table.cell>
                            <flux:table.cell align="start">
                                {{ $a->nama_anggota }}
                            </flux:table.cell>
                            <flux:table.cell align="start">
                                {{ $a->email_anggota ?: '-' }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                {{ $a->telepon_anggota ?: '-' }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                {{ $a->tanggal_masuk ? \Carbon\Carbon::parse($a->tanggal_masuk)->translatedFormat('d M Y') : '-' }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                Rp {{ number_format($this->getTotalSimpanan($a->id_anggota), 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="end" class="py-2">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $a->id_anggota }})" size="sm" icon="pencil-square"
                                        wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $a->id_anggota }})"
                                        wire:confirm="Yakin hapus anggota ini?" size="sm" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                📭 Belum ada data anggota
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $anggotas->links() }}
        </div>
    </div>

    {{-- Modal Form Anggota dengan Refresh Kode --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit Anggota' : 'Tambah Anggota'" class="max-w-lg"
        style="width: 800px;">
        <form wire:submit="save" class="space-y-5">
            {{-- Kode Anggota dengan tombol refresh (hanya di mode tambah) --}}
            <flux:field>
                <flux:label>Kode Anggota</flux:label>
                <div class="flex-1 justify-between gap-2 flex">
                    <flux:input wire:model="kode_anggota" class="lg" required>
                        <x-slot name="iconTrailing">
                            @if(!$editMode)
                                <flux:button variant="ghost" wire:click="refreshKode" class="flex-1">
                                    ↻ Refresh
                                </flux:button>
                            @endif
                        </x-slot>
                    </flux:input>
                    @error('kode_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </flux:field>
            <flux:field>
                <flux:label>Nama Anggota</flux:label>
                <flux:input wire:model="nama_anggota" required />
                @error('nama_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <flux:field>
                <flux:label>Email (opsional)</flux:label>
                <flux:input type="email" wire:model="email_anggota" />
                @error('email_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <flux:field>
                <flux:label>Telepon (opsional)</flux:label>
                <flux:input wire:model="telepon_anggota" />
                @error('telepon_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <flux:field>
                <flux:label>Alamat (opsional)</flux:label>
                <flux:textarea wire:model="alamat_anggota" rows="2" />
                @error('alamat_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <flux:field>
                <flux:label>Tanggal Masuk</flux:label>
                <flux:input type="date" wire:model="tanggal_masuk" required />
                @error('tanggal_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>
            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700"
                style="padding-top: 20px;">
                <flux:button variant="primary" color="red" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary" color="blue">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>