<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Riwayat Transaksi</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Daftar transaksi, status, dan data pelanggan</p>
        </div>
    </div>
    {{-- Filter & Pencarian --}}
    <div class="space-y-2">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1" style="padding-top: 24px;">
                <flux:input wire:model.live.debounce.100ms="search" placeholder="Cari Order ID / Nama Pelanggan"
                    icon="magnifying-glass" clearable />
            </div>
            <div class="sm:w-50" style="padding-top: 24px;">
                <flux:select wire:model.live="filterStatus" placeholder="Semua Status">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="paid">Lunas</flux:select.option>
                    <flux:select.option value="pending">Pending</flux:select.option>
                    <flux:select.option value="failed">Gagal</flux:select.option>
                </flux:select>
            </div>
            <div class="sm:w-50">
                <flux:label>Dari Tanggal</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </div>
            <div class="sm:w-50">
                <flux:label>Sampai Tanggal</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 border-neutral-200 dark:border-neutral-700 gap-4">
        <flux:card class="rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700" style="height: 110px;">
            <flux:text class="flex items-center gap-2 text-black">
                <flux:icon.currency-dollar variant="solid" class="h-4 w-4 text-green-600" />
                <span class="text-base font-medium text-black dark:text-white">Pemasukan Hari Ini</span>
            </flux:text>
            <div class="flex p-2">
                <flux:heading size="xl" class="mb-1 flex-1 ">Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </flux:heading>
                <flux:icon.arrow-trending-up variant="solid" class="justify-end w-8 h-8 text-green-600"
                    style="items-align: center;" />
            </div>
        </flux:card>
        <flux:card class="rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700" style="height: 110px;">
            <flux:text class="flex items-center gap-2 text-black">
                <flux:icon.bell-alert variant="solid" class="h-4 w-4 text-blue-600" />
                <span class="text-base font-medium text-black dark:text-white">Status Terbanyak</span>
            </flux:text>
            <div class="flex p-2">
                <flux:heading size="xl" class="mb-1 flex-1 ">{{ $mostStatus ?? '-' }}
                </flux:heading>
                <flux:icon.chart-bar variant="solid" class="justify-end w-8 h-8 text-blue-600" />
            </div>
        </flux:card>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white dark:bg-zinc-800">
        <div class="overflow-y-auto">
            <flux:table container>
                <flux:table.columns sticky>
                    <flux:table.column>ID Pesanan</flux:table.column>
                    <flux:table.column>Pelanggan</flux:table.column>
                    <flux:table.column>Anggota</flux:table.column>
                    <flux:table.column>Metode</flux:table.column>
                    <flux:table.column class="text-center">Item</flux:table.column>
                    <flux:table.column align="center">Total</flux:table.column>
                    <flux:table.column align="center">Status</flux:table.column>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column>Detail</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($orders as $order)
                        <flux:table.row wire:key="order-{{ $order->id }}">
                            <flux:table.cell class="font-mono font-semibold">
                                {{ $order->order_id ?? $order->id }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $order->customer_name ?: 'Tanpa Nama' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $order->nama_anggota ?? '-' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ ucfirst($order->payment_method) }}
                            </flux:table.cell>
                            <flux:table.cell class="text-center">
                                {{ $order->total_items }} item
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <span class="font-bold">{{ $order->total_rupiah }}</span>
                            </flux:table.cell>
                            <flux:table.cell align="center">
                                @if($order->payment_status === 'paid')
                                    <flux:badge color="green" size="sm">Lunas</flux:badge>
                                @elseif($order->payment_status === 'pending')
                                    <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                @else
                                    <flux:badge color="red" size="sm">Gagal</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $order->formatted_date }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="ghost" size="xs" icon="eye" wire:click="openDetail({{ $order->id }})">
                                    Lihat
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <!-- Perhatikan colspan sekarang 9 karena penambahan satu kolom -->
                            <flux:table.cell colspan="9" class="text-center py-8 text-zinc-500">
                                📭 Belum ada transaksi
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- Ringkasan & Cetak --}}
    <div class="flex justify-end">
        <flux:button wire:click="printReport" variant="primary" icon="printer">
            Cetak Laporan
        </flux:button>
    </div>

    {{-- Modal Detail Pesanan --}}
    <flux:modal wire:model="showDetailModal" title="Detail Pesanan" class="max-w-2xl">
        @if ($selectedOrder)
            <div class="space-y-6">
                {{-- Info Pesanan --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-zinc-500">ID Pesanan</p>
                        <p class="font-semibold">{{ $selectedOrder->order_id ?? $selectedOrder->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Tanggal</p>
                        <p class="font-semibold">{{ $selectedOrder->formatted_date }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Pelanggan</p>
                        <p class="font-semibold">{{ $selectedOrder->customer_name ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Anggota</p>
                        <p class="font-semibold">{{ $selectedOrder->nama_anggota ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Status</p>
                        @if($selectedOrder->payment_status === 'paid')
                            <flux:badge color="green" size="sm">Lunas</flux:badge>
                        @elseif($selectedOrder->payment_status === 'pending')
                            <flux:badge color="yellow" size="sm">Pending</flux:badge>
                        @else
                            <flux:badge color="red" size="sm">Gagal</flux:badge>
                        @endif
                    </div>
                </div>

                {{-- Daftar Produk --}}
                <div>
                    <h4 class="font-semibold text-sm mb-3">Daftar Produk</h4>
                    <div class="space-y-2">
                        @foreach ($selectedOrder->cart_items as $item)
                            <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                <img src="{{ $item['image_url'] ?? asset('img/placeholder.svg') }}" alt="{{ $item['name'] }}"
                                    class="w-10 h-10 rounded-lg object-cover">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item['name'] }}</p>
                                    <p class="text-xs text-zinc-500">{{ $item['quantity'] }} x Rp
                                        {{ number_format($item['price'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="text-sm font-bold">Rp
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Total --}}
                <div class="flex justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <span class="font-medium">Total Pembayaran</span>
                    <span
                        class="text-lg font-bold text-green-600 dark:text-green-400">{{ $selectedOrder->total_rupiah }}</span>
                </div>
            </div>
        @endif
    </flux:modal>
</div>