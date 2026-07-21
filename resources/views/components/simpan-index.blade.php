<div x-data x-on:notify.window="Flux.toast({text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
    {{-- Header --}}
    <div class="flex justify-between items-center flex-wrap gap-2">
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
        <div class="flex-1" style="padding-top: 24px;">
            <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari nama anggota..." />
        </div>
        <div class="sm:w-48" style="padding-top: 24px;">
            <flux:select wire:model.live="filterjenis_simpanan" placeholder="Filter">
                <flux:select.option value="">Semua Jenis</flux:select.option>
                <flux:select.option value="pokok">Pokok</flux:select.option>
                <flux:select.option value="wajib">Wajib</flux:select.option>
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

    {{-- Tabel Simpanan --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column>Kode</flux:table.column>
                    <flux:table.column align="start">Nama Anggota</flux:table.column>
                    <flux:table.column align="center">Jenis Simpanan</flux:table.column>
                    <flux:table.column align="center">Jumlah (Rp)</flux:table.column>
                    <flux:table.column align="center">Metode</flux:table.column>
                    <flux:table.column align="center">Tanggal</flux:table.column>
                    <flux:table.column align="center">Total Simpanan</flux:table.column>
                    <flux:table.column align="center">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows class="w-10 h-10">
                    @forelse($simpanans as $s)
                        <flux:table.row wire:key="simpanan-{{ $s->id }}">
                            <flux:table.cell class="font-medium" align="start">
                                {{ $s->kode_anggota ?? ($s->anggota->kode_anggota ?? '-') }}
                            </flux:table.cell>
                            <flux:table.cell class="font-medium" align="start">
                                {{ $s->nama_anggota ?? ($s->anggota->nama_anggota ?? '-') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:badge size="sm" color="{{ $s->jenis_simpanan === 'pokok' ? 'blue' : 'purple' }}">
                                    {{ ucfirst($s->jenis_simpanan) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-bold tabular-nums py-2" align="center">
                                Rp {{ number_format($s->jumlah, 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                <flux:badge size="sm" color="{{ $s->payment_method === 'tunai' ? 'green' : 'purple' }}">
                                    {{ $s->payment_method === 'tunai' ? 'Cash' : 'QRIS' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($s->tanggal)->format('l, d M Y') }}
                            </flux:table.cell>
                            <flux:table.cell align="center" class="font-bold text-green-600">
                                Rp {{ number_format($this->getTotalSimpanan($s->id_anggota), 0, ',', '.') }}
                            </flux:table.cell>
                            <flux:table.cell align="end" class="py-2">
                                <flux:button.group>
                                    <flux:button wire:click="openEdit({{ $s->id }})" size="sm" icon="pencil-square"
                                        wire:navigate>
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $s->id }})" wire:confirm="Yakin hapus simpanan ini?"
                                        size="sm" icon="trash" variant="danger">
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
        <div class="space-y-4 p-6">
            {{-- Ringkasan (jika edit mode) --}}
            @if($editMode)
                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded">
                    <div class="flex justify-between">
                        <span class="text-sm text-blue-700 dark:text-blue-300">ID Simpanan</span>
                        <span class="text-sm font-bold text-blue-700 dark:text-blue-300">#{{ $simpanId }}</span>
                    </div>
                </div>
            @endif

            {{-- Pilih Anggota --}}
            <flux:field>
                <flux:label>Anggota</flux:label>
                <flux:select wire:model.live="id_anggota" required>
                    <option value="">- Pilih Anggota -</option>
                    @foreach($anggotas as $ag)
                        <option value="{{ $ag->id_anggota }}">
                            {{ $ag->nama_anggota }} ({{ $ag->kode_anggota }})
                            {{ $this->hasPokok($ag->id_anggota) ? ' - Pokok ✅' : '' }}
                        </option>
                    @endforeach
                </flux:select>
                @error('id_anggota')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </flux:field>

            {{-- Jenis Simpanan (Live Update) --}}
            <flux:field>
                <flux:label>Jenis Simpanan</flux:label>
                <flux:select wire:model.live="jenis_simpanan" required>
                    <option value="">- Pilih -</option>

                    @php
                        $hasPokok = $id_anggota ? $this->hasPokok($id_anggota) : false;
                    @endphp

                    {{-- Tampilkan opsi pokok hanya jika belum bayar atau sedang edit pokok --}}
                    @if(!$hasPokok || ($editMode && $jenis_simpanan === 'pokok'))
                        <option value="pokok">Pokok (Rp 100.000)</option>
                    @endif

                    <option value="wajib">Wajib</option>
                </flux:select>

                {{-- Info Status Pokok --}}
                @if($id_anggota)
                    @if($hasPokok && !($editMode && $jenis_simpanan === 'pokok'))
                        <div class="flex items-center gap-2 mt-2 text-green-600 dark:text-green-400">
                            <flux:icon.check-circle class="w-4 h-4" />
                            <span class="text-xs">Simpanan pokok sudah lunas. Hanya simpanan wajib yang dapat
                                ditambahkan.</span>
                        </div>
                    @elseif(!$hasPokok && !$editMode)
                        <div class="flex items-center gap-2 mt-2 text-yellow-600 dark:text-yellow-400">
                            <flux:icon.exclamation-circle class="w-4 h-4" />
                            <span class="text-xs">Simpanan pokok belum dibayarkan. Silakan pilih "Pokok" untuk pembayaran
                                pertama.</span>
                        </div>
                    @endif
                @endif

                @error('jenis_simpanan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </flux:field>

            {{-- Jumlah (Auto-update) --}}
            <flux:field>
                <flux:label>Jumlah (Rp)</flux:label>

                @if($jenis_simpanan === 'pokok')
                    <div class="bg-zinc-100 dark:bg-zinc-700 p-3 rounded flex justify-between items-center">
                        <span class="text-zinc-900 dark:text-white font-bold">Rp 100.000</span>
                        <span class="text-xs text-blue-600 dark:text-blue-400">Simpanan Pokok</span>
                    </div>
                    <input type="hidden" wire:model="jumlah" value="100000" />
                @else
                    <flux:input type="number" wire:model="jumlah" min="0" step="1000" required
                        placeholder="Masukkan jumlah simpanan" />
                @endif

                @error('jumlah')
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

            {{-- Tanggal --}}
            <flux:field>
                <flux:label>Tanggal</flux:label>
                <flux:input type="date" wire:model="tanggal" required />
                @error('tanggal')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </flux:field>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2 justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('showModal', false)" color="red" variant="danger" style="width: 50%;">
                    Batal
                </flux:button>
                <flux:button wire:click="processPayment" color="blue" variant="primary" style="width: 50%;"
                    icon="credit-card">
                    Bayar
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal QRIS --}}
    <flux:modal wire:model="showQrisModal" title="Pembayaran QRIS" class="max-w-sm">
        <div class="text-center space-y-4 p-6">
            <p class="text-sm">Scan QR code berikut untuk melakukan pembayaran:</p>

            {{-- QR Code Image --}}
            @if($qrisUrl)
                <div class="flex justify-center">
                    <img src="{{ $qrisUrl }}" alt="QRIS" class="w-64 h-64" />
                </div>
            @endif

            <div class="text-sm space-y-1">
                <p>Order ID: <strong>{{ $qrisOrderId }}</strong></p>
                <p>Total: <strong>Rp {{ number_format($jumlah ?? 0, 0, ',', '.') }}</strong></p>
            </div>

            <div class="flex gap-2 justify-center">
                <flux:button variant="primary" color="red" wire:click="closeQrisModal">Tutup</flux:button>
                <flux:button variant="primary" color="blue" wire:click="checkPaymentStatus">Cek Status</flux:button>
            </div>
        </div>
    </flux:modal>

</div>