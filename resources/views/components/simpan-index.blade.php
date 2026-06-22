<div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
            <flux:heading size="xl">Simpanan Anggota</flux:heading>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Kelola simpanan pokok & wajib anggota</p>
        </div>
        <div>
            <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus" class="whitespace-nowrap">
                Catat Simpanan
            </flux:button>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari nama anggota..." />
        </div>
        <div class="sm:w-48">
            <flux:select wire:model.live="filterJenis" placeholder="Semua Jenis">
                <flux:select.option value="">Semua Jenis</flux:select.option>
                <flux:select.option value="pokok">Pokok</flux:select.option>
                <flux:select.option value="wajib">Wajib</flux:select.option>
            </flux:select>
        </div>
        <div class="sm:w-44">
            <flux:input type="date" wire:model.live="dateFrom" placeholder="Dari Tanggal" />
        </div>
        <div class="sm:w-44">
            <flux:input type="date" wire:model.live="dateTo" placeholder="Sampai Tanggal" />
        </div>
    </div>

    {{-- Tabel Simpanan --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column>Kode</flux:table.column>
                    <flux:table.column>Nama Anggota</flux:table.column>
                    <flux:table.column>Jenis</flux:table.column>
                    <flux:table.column align="end">Jumlah (Rp)</flux:table.column>
                    <flux:table.column>Metode</flux:table.column>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($simpanans as $s)
                        <flux:table.row wire:key="simpanan-{{ $s->id }}">
                            <flux:table.cell class="font-medium">
                                {{ $s->kode_anggota ?? ($s->anggota->kode_anggota ?? '-') }}
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">
                                {{ $s->nama_anggota ?? ($s->anggota->nama_anggota ?? '-') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $s->jenis === 'pokok' ? 'blue' : 'purple' }}">
                                    {{ ucfirst($s->jenis) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end" class="font-bold tabular-nums">
                                Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $s->metode_pembayaran === 'cash' ? 'green' : 'zinc' }}">
                                    {{ $s->metode_pembayaran === 'cash' ? '💵 Cash' : '📱 QRIS' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $s->formatted_date }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $s->id }})" size="xs" icon="pencil-square"
                                        wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $s->id }})" wire:confirm="Yakin hapus simpanan ini?"
                                        size="xs" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-8 text-zinc-500">
                                📭 Belum ada data simpanan
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $simpanans->links() }}
        </div>
    </div>

    {{-- Modal Form Simpanan --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit Simpanan' : 'Tambah Simpanan'" class="max-w-lg"
        style="width: 800px;">
        <form wire:submit="save" class="space-y-5">
            <flux:field>
                <flux:label>Anggota</flux:label>
                <flux:select wire:model="id_anggota" required>
                    <option value="">- Pilih Anggota -</option>
                    @foreach($anggotas as $ag)
                        <option value="{{ $ag->id_anggota }}">{{ $ag->nama_anggota }} ({{ $ag->kode_anggota }})</option>
                    @endforeach
                </flux:select>
                @error('id_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Jenis Simpanan</flux:label>
                <flux:select wire:model="jenis" required>
                    <option value="">- Pilih -</option>
                    <option value="pokok">Pokok</option>
                    <option value="wajib">Wajib</option>
                </flux:select>
                @error('jenis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Jumlah (Rp)</flux:label>
                <flux:input type="number" wire:model="jumlah" min="0" step="1000" required />
                @error('jumlah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Metode Pembayaran</flux:label>
                <flux:select wire:model="metode_pembayaran" required>
                    <option value="">- Pilih Pembayaran -</option>
                    <option value="cash">Cash</option>
                    <option value="qris">QRIS</option>
                </flux:select>
                @error('metode_pembayaran') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Tanggal</flux:label>
                <flux:input type="date" wire:model="tanggal" required />
                @error('tanggal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700"
                style="padding-top: 20px;">
                <flux:button variant="primary" color="red" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button type="submit" variant="primary" color="blue">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>