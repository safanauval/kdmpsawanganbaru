<div  x-data x-on:notify.window="Flux.toast({text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
            <flux:heading size="xl">Pinjaman Anggota</flux:heading>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Kelola pinjaman anggota koperasi</p>
        </div>
        <div>
            <flux:button wire:click="openCreate" variant="primary" color="blue" icon="plus" class="whitespace-nowrap">
                Tambah Pinjaman
            </flux:button>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1" style="padding-top: 24px;">
            <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari kode / nama anggota..." />
        </div>
        <div class="sm:w-48" style="padding-top: 24px;">
            <flux:select wire:model.live="filterStatus" placeholder="Status">
                <flux:select.option value="">Semua Status</flux:select.option>
                <flux:select.option value="aktif">Aktif</flux:select.option>
                <flux:select.option value="lunas">Lunas</flux:select.option>
                <flux:select.option value="macet">Macet</flux:select.option>
            </flux:select>
        </div>
        <div class="sm:w-44">
            <flux:label>Dari Tanggal</flux:label>
            <flux:input type="date" wire:model.live="dateFrom" placeholder="Dari Tanggal" />
        </div>
        <div class="sm:w-44">
            <flux:label>Sampai Tanggal</flux:label>
            <flux:input type="date" wire:model.live="dateTo" placeholder="Sampai Tanggal" />
        </div>
    </div>

    {{-- Tabel Pinjaman --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column>Kode</flux:table.column>
                    <flux:table.column>Nama Anggota</flux:table.column>
                    <flux:table.column align="center">Pinjaman</flux:table.column>
                    <flux:table.column align="center">Bunga</flux:table.column>
                    <flux:table.column align="center">Tenor</flux:table.column>
                    <flux:table.column align="center">Angsuran</flux:table.column>
                    <flux:table.column align="center">Sisa</flux:table.column>
                    <flux:table.column align="center">Status</flux:table.column>
                    <flux:table.column align="center">Tanggal</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($pinjamans as $p)
                        <flux:table.row wire:key="pinjaman-{{ $p->id }}">
                            <flux:table.cell class="font-mono py-2" >
                                {{ $p->kode_pinjaman }}
                            </flux:table.cell>
                            <flux:table.cell class="py-2 text-base">
                                {{ $p->anggota->nama_anggota ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell class="font-bold text-base" align="center">
                                Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">{{ $p->bunga_persen }}%</flux:table.cell>
                            <flux:table.cell align="center">{{ $p->tenor }} bln</flux:table.cell>
                            <flux:table.cell align="center">
                                Rp {{ number_format($p->angsuran_per_bulan, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="font-bold text-base {{ $p->sisa_pinjaman > 0 ? 'text-red-600' : 'text-green-600' }}">
                                Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:badge color="{{ $p->status === 'aktif' ? 'blue' : ($p->status === 'lunas' ? 'green' : 'red') }}">
                                    {{ ucfirst($p->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-600 text-base" align="center">
                                {{ $p->formatted_date }}
                            </flux:table.cell>
                            <flux:table.cell align="end" class="py-2">
                                <flux:button.group>
                                    @if($p->status === 'aktif')
                                        <flux:button wire:click="openBayar({{ $p->id }})" size="sm" icon="banknotes" variant="primary" color="blue">
                                            Bayar
                                        </flux:button>
                                    @endif
                                    <flux:button wire:click="openEdit({{ $p->id }})" size="sm" icon="pencil-square" /> 
                                    <flux:button wire:click="delete({{ $p->id }})" wire:confirm="Yakin hapus?" size="sm" icon="trash" variant="danger" />                                 
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="10" class="text-center py-8 text-zinc-500">
                                📭 Belum ada data pinjaman
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $pinjamans->links() }}
        </div>
    </div>

    {{-- Modal Form Pinjaman --}}
    <flux:modal wire:model="showModal" :title="$editMode ? 'Edit Pinjaman' : 'Tambah Pinjaman'" class="max-w-lg" style="width: 800px;">
        <div class="space-y-4 p-6">
            <flux:field>
                <flux:label>Anggota</flux:label>
                <flux:select wire:model.live="id_anggota" required>
                    <option value="">- Pilih Anggota -</option>
                    @foreach($anggotas as $ag)
                        <option value="{{ $ag->id_anggota }}">{{ $ag->nama_anggota }} ({{ $ag->kode_anggota }})</option>
                    @endforeach
                </flux:select>
                @error('id_anggota') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Jumlah Pinjaman (Rp)</flux:label>
                <flux:input type="number" wire:model.live="jumlah_pinjaman" min="0" required />
                @error('jumlah_pinjaman') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Bunga per Bulan (%)</flux:label>
                    <flux:input type="number" wire:model.live="bunga_persen" min="0" max="100" step="0.1" required />
                </flux:field>
                <flux:field>
                    <flux:label>Tenor (Bulan)</flux:label>
                    <flux:input type="number" wire:model.live="tenor" min="1" max="60" required />
                </flux:field>
            </div>

            {{-- Ringkasan Perhitungan --}}
            @if($jumlah_pinjaman > 0 && $tenor > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Angsuran per Bulan</span>
                        <span class="font-bold">Rp {{ number_format($angsuran_per_bulan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Total Pengembalian</span>
                        <span class="font-bold">Rp {{ number_format($total_pengembalian ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Jatuh Tempo</span>
                        <span class="font-bold">{{ $tanggal_jatuh_tempo ?? '-' }}</span>
                    </div>
                </div>
            @endif

            <flux:field>
                <flux:label>Tanggal Pinjaman</flux:label>
                <flux:input type="date" wire:model="tanggal_pinjaman" required />
            </flux:field>

            <flux:field>
                <flux:label>Keterangan (Opsional)</flux:label>
                <flux:textarea wire:model="keterangan" rows="2" />
            </flux:field>

            <div class="flex gap-2 justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('showModal', false)" color="red" variant="danger" style="width: 50%;">Batal</flux:button>
                <flux:button wire:click="save" variant="primary" color="blue" style="width: 50%;">
                    {{ $editMode ? 'Perbarui' : 'Simpan' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Bayar Angsuran --}}
    <flux:modal wire:model="showBayarModal" title="Bayar Angsuran" class="max-w-lg" style="width: 800px;">
        <div class="space-y-4 p-6">
            {{-- Ringkasan Pinjaman --}}
            @if($selectedPinjaman)
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-blue-700 dark:text-blue-300">Kode Pinjaman</span>
                        <span class="font-bold text-white-600 dark:text-white-300">{{ $selectedPinjaman->kode_pinjaman }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-white-700 dark:text-white-300">Anggota</span>
                        <span class="font-bold text-white-600 dark:text-white-300">{{ $selectedPinjaman->anggota->nama_anggota ?? '-' }}</span>
                    </div>
                    <div class="border-t border-white-200 dark:border-white-700" >
                        <div class="flex justify-between text-sm" style="padding-top: 8px;">
                            <span class="text-white-700 dark:text-white-300">Sisa Pinjaman</span>
                            <span class="font-bold text-red-600">Rp {{ number_format($selectedPinjaman->sisa_pinjaman, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-white-700 dark:text-white-300">Angsuran per Bulan</span>
                        <span class="font-bold text-white-600 dark:text-white-300">Rp {{ number_format($selectedPinjaman->angsuran_per_bulan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-white-700 dark:text-white-300">Angsuran ke</span>
                        <span class="font-bold text-white-600 dark:text-blue-300">{{ $selectedPinjaman->angsurans->count() + 1 }} dari {{ $selectedPinjaman->tenor }}</span>
                    </div>
                </div>
            @endif

            {{-- Jumlah Bayar --}}
            <flux:field>
                <flux:label>Jumlah Bayar (Rp)</flux:label>
                <flux:input 
                    type="number" 
                    wire:model="jumlah_bayar" 
                    min="0" 
                    step="1000" 
                    readonly 
                    placeholder="Masukkan jumlah pembayaran"
                />
                @error('jumlah_bayar') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </flux:field>

            {{-- Metode Pembayaran --}}
            <flux:field>
                <flux:label>Metode Pembayaran</flux:label>
                <flux:radio.group variant="segmented" wire:model.live="payment_method">
                    <flux:radio value="tunai" label="Tunai" icon="banknotes"></flux:radio>
                    <flux:radio value="non-tunai" label="QRIS / Transfer" icon="qr-code"></flux:radio>
                </flux:radio.group>
                @error('payment_method') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                @enderror
            </flux:field>

            {{-- Info Metode Pembayaran --}}
            @if($payment_method === 'non-tunai')
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        Pembayaran akan diproses melalui QRIS atau Transfer Bank.
                    </p>
                </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="flex gap-2 justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('showBayarModal', false)" color="red" variant="danger" style="width: 50%;">
                    Batal
                </flux:button>
                <flux:button wire:click="bayarAngsuran" variant="primary" color="blue" style="width: 50%;" icon="banknotes">
                    Bayar Angsuran
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal QRIS --}}
    <flux:modal wire:model="showQrisModal" title="Pembayaran QRIS" class="max-w-sm">
        <div class="text-center space-y-4 p-6">
            <p class="text-sm">Scan QR code berikut untuk melakukan pembayaran:</p>
            
            @if($qrisUrl)
                <div class="flex justify-center">
                    <img src="{{ $qrisUrl }}" alt="QRIS" class="w-64 h-64" />
                </div>
            @endif
            
            <div class="text-sm space-y-1">
                <p>Order ID: <strong>{{ $qrisOrderId }}</strong></p>
                <p>Total: <strong>Rp {{ number_format($jumlah_bayar ?? 0, 0, ',', '.') }}</strong></p>
            </div>
            
            <div class="flex gap-2 justify-center">
                <flux:button variant="primary" color="red" wire:click="closeQrisModal">Tutup</flux:button>
                <flux:button variant="primary" color="blue" wire:click="checkPaymentStatus">Cek Status</flux:button>
            </div>
        </div>
    </flux:modal>
</div>