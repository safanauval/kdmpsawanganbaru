<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    x-on:cart-updated.window="$wire.$refresh()" class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1"
    style="padding-top: 20px;">
    <div class="flex-1" style="padding-right: 330px;">
        <!-- Search & Filter -->
        <div class="flex flex-col sm:flex-row gap-4 p-1 sm:p-1" style="padding-bottom: 20px;">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari produk..." icon="magnifying-glass"
                    clearable />
            </div>
            <div class="sm:w-50">
                <flux:select wire:model.live="selectedCategory" placeholder="Semua Kategori">
                    <flux:select.option value="">Semua</flux:select.option>
                    @foreach($categories as $cat)
                        <flux:select.option value="{{ $cat->id }}">{{ $cat->nama }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <!-- Grid Produk -->
        <div class="grid gap-4 p-1 gap-4 p-1" style="grid-template-columns: auto auto auto auto;">
            @foreach($products as $product)
                <flux:card class="space-y-2" style="cursor: pointer;">
                    <!-- Gambar -->
                    <div wire:click="addToCart({{ $product->id }})"
                        class="flex items-center justify-center w-full h-32 rounded-lg bg-white dark:bg-zinc-700">
                        @if($product->gambar_url)
                            <img src="{{ $product->gambar_url }}" alt="{{ $product->nama_barang }}" class="rounded-lg"
                                style="width: 140px; height: 140px; object-fit: cover;" />
                        @else
                            <flux:icon.photo style="width: 140px; height: 140px; object-fit: cover;"></flux:icon.photo>
                        @endif
                    </div>
                    <flux:text class="text-lg text-center gap-4" variant="strong">{{ $product->nama_barang }}</flux:text>
                    <div class="flex justify-between gap-2 lg-2">
                        <flux:text class="text-sm dark:text-zinc-400">Stok: {{ $product->stok }}</flux:text>
                        <flux:text class="text-sm font-bold text-green-600 dark:text-green-400">Rp
                            {{ number_format($product->harga_jual, 0, ',', '.') }}
                        </flux:text>
                    </div>

                    @if($product->stok > 0)
                        <div class="mt-auto pt-2 w-full">
                            @php
                                $cartItem = collect($cart)->firstWhere('id', $product->id);
                                $qty = $cartItem['quantity'] ?? 0;
                            @endphp
                            @if($qty > 0)
                                <div class="flex items-center justify-between">
                                    <flux:button wire:click="updateQuantity({{ $product->id }}, {{ $qty - 1 }})" size="sm"
                                        icon="minus" variant="danger" />
                                    <span class="font-bold">{{ $qty }}</span>
                                    <flux:button wire:click="updateQuantity({{ $product->id }}, {{ $qty + 1 }})" size="sm"
                                        icon="plus" variant="primary" :disabled="$product->stok <= $qty" />
                                </div>
                            @else
                                <flux:button wire:click="addToCart({{ $product->id }})" class="w-full" size="sm" variant="primary"
                                    icon="shopping-cart">Tambah</flux:button>
                            @endif
                        </div>
                    @else
                        <flux:button class="w-full" variant="danger" disabled>Habis</flux:button>
                    @endif
                </flux:card>
            @endforeach
        </div>
        @if($products->isEmpty())
            <div class="text-center py-10 text-zinc-500">Produk tidak ditemukan.</div>
        @endif
    </div>

    {{-- PANEL KERANJANG FIXED --}}
    <div class="fixed bottom-0 right-0 h-full w-[340px] bg-white dark:bg-zinc-800 z-50 flex flex-col border-l border-zinc-200 dark:border-zinc-700"
        style="height: full; padding-top: 10px; padding-right: 20px;">
        <!-- Header Keranjang -->
        <div class="flex justify-between items-center p-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-sm flex items-center gap-2" variant="strong">
                <flux:icon.shopping-cart class="w-3 h-3" />
                Keranjang ({{ count($cart) }})
            </h2>
            <div class="flex gap-2">
                @if(count($cart))
                    <flux:button wire:click="clearCart" size="xs" variant="ghost" class="text-red-500">Kosongkan
                    </flux:button>
                @endif
                <flux:button @click="$gtore.cart.open = false" variant="ghost" size="xs" icon="x-mark" />
            </div>
        </div>
        <!-- Daftar Item Dalam Keranjang -->
        <div class="flex-1 overflow-y-auto space-y-2 p-2">
            @forelse($cart as $item)
                <div class="flex items-center justify-between gap-3 p-2 mt-1 rounded-lg bg-zinc-50 dark:bg-zinc-700"
                    style="width: 300px;">
                    <img src="{{ $item['image_url'] ?? asset('img/placeholder.svg') }}" class="rounded-lg"
                        style="object-cover; width: 50px; height: 50px; " alt="{{ $item['name'] }}">
                    <div class="flex-1 item-center justify-between gap-2">
                        <div class="flex justify-between p-2">
                            <flux:text class="text-sm font-bold">{{ $item['name'] }}</flux:text>
                            <flux:text class="text-sm" variant="strong">Rp
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </flux:text>
                        </div>
                        <div class="flex justify-between items-center gap-2 p-1">
                            <div class="flex gap-4 justify-content-center">
                                <flux:button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                    size="xs" icon="minus" variant="filled" />
                                <flux:text class="text-sm font-bold mx-2">{{ $item['quantity'] }}</flux:text>
                                <flux:button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                    size="xs" icon="plus" variant="filled"
                                    :disabled="$item['quantity'] >= $item['stock']" />
                            </div>
                            <flux:button wire:click="removeFromCart({{ $item['id'] }})" size="xs" variant="subtle"
                                class="justify-end mt-1">
                                <flux:icon.trash class="w-4 h-4" color="red" />
                            </flux:button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-sm text-zinc-500 py-8">Keranjang kosong</p>
            @endforelse
        </div>

        {{-- Total & Bayar SELALU TAMPIL --}}
        <div class="border-t p-4 space-y-2" style="width: 320px;">
            @php
                $totalQty = collect($cart)->sum('quantity');

                // Hitung diskon real-time di view
                $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
                $discPercent = $id_anggota ? $memberDiscountPercent : $nonMemberDiscountPercent;
                $discAmount = $subtotal * $discPercent / 100;
                $totalAfterDisc = max(0, $subtotal - $discAmount);
            @endphp

            <div class="flex justify-between text-sm font-bold">
                <span>Total QTY</span>
                <span>{{ $totalQty }}</span>
            </div>

            <div class="flex justify-between text-sm font-bold text-black-600">
                <span>Diskon</span>
                <span>-Rp {{ number_format($discAmount, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between text-base">
                <span class="text-base font-bold">Total</span>
                <span class="text-base font-bold text-green-600">Rp
                    {{ number_format($totalAfterDisc, 0, ',', '.') }}</span>
            </div>

            <flux:button wire:click="openPaymentModal" color="blue" variant="primary" class="w-full" icon="credit-card"
                :disabled="count($cart) === 0">
                Bayar Sekarang
            </flux:button>
        </div>
    </div>

    <!-- Modal Pembayaran (tidak berubah) -->
    <flux:modal wire:model="showPaymentModal" title="Pembayaran" class="max-w-md" style="width: 800px; height: 550px;">
        <div class="space-y-4 p-6">
            <div class="bg-zinc-100 dark:bg-zinc-700 p-3 rounded">
                <div class="flex justify-between font-bold">
                    <span>Total Tagihan</span>
                    <span class="text-green-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Input Kode Anggota --}}
            <flux:field>
                <flux:label>Kode Anggota (opsional)</flux:label>
                <flux:input wire:model.live="kodeAnggota" placeholder="Masukkan kode anggota..." />
                @if($id_anggota)
                    <div class="flex items-center gap-2 mt-2 text-green-500">
                        <flux:icon.check-circle color="green" class="w-4 h-4" />
                        <span class="text-xs">Anggota terdaftar - Diskon anggota diterapkan</span>
                    </div>
                @elseif($kodeAnggota && !$id_anggota)
                    <div class="flex items-center gap-2 mt-2 text-red-500">
                        <flux:icon.x-circle color="red" class="w-4 h-4" />
                        <span class="text-xs">Anggota tidak ditemukan</span>
                    </div>
                @endif
            </flux:field>

            {{-- Nama Pelanggan --}}
            <flux:field>
                <flux:label>Nama Pelanggan</flux:label>
                <flux:input wire:model="namaPelanggan" placeholder="Nama pelanggan" />
            </flux:field>

            {{-- Pilihan metode pembayaran dengan Tabs --}}
            <flux:field>
                <flux:label>Metode Pembayaran</flux:label>
                <flux:radio.group variant="segmented" wire:model.live="paymentMethod">
                    <flux:radio value="tunai" label="Tunai" icon="banknotes"></flux:radio>
                    <flux:radio value="non-tunai" label="QRIS / Transfer" icon="qr-code"></flux:radio>
                </flux:radio.group>
            </flux:field>

            {{-- Konten untuk metode Tunai --}}
            @if($paymentMethod === 'tunai')
                <flux:field>
                    <flux:label>Jumlah Bayar</flux:label>
                    <flux:input type="number" wire:model.live="paymentAmount" min="0" />
                    {{-- Pesan uang kurang --}}
                    @if($paymentMethod === 'tunai' && is_numeric($paymentAmount) && $paymentAmount > 0 && $paymentAmount < $this->total)
                        <p class="text-sm text-red-500 mt-1">
                            ⚠️ Uang kurang Rp {{ number_format($this->total - $paymentAmount, 0, ',', '.') }}
                        </p>
                    @endif
                </flux:field>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded flex justify-between">
                    <span>Kembalian</span>
                    <span class="font-bold text-blue-600">Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            @elseif($paymentMethod === 'non-tunai')
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded">
                    <p class="text-sm text-yellow-800">Pilih "Proses Pembayaran" untuk melanjutkan ke pembayaran QRIS /
                        Transfer.</p>
                </div>
            @endif

            <div class="flex gap-2 justify-between mt-4">
                <flux:button wire:click="closePaymentModal" color="red" variant="danger" style="width: 50%;">Batal
                </flux:button>
                <flux:button wire:click="processPayment" color="blue" variant="primary" style="width: 50%;"
                    :disabled="$paymentMethod === 'tunai' && $paymentAmount < $this->total">
                    Proses Pembayaran
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Modal Struk Pembayaran -->
    <flux:modal wire:model="showReceiptModal" title="Struk Pembayaran" class="max-w-sm" id="receipt-modal">
        @if($lastOrder)
            <div class="p-4 text-sm font-mono" id="receipt-content">
                {{-- Header Toko --}}
                <div class="text-center border-b pb-2 mb-2">
                    <h2 class="font-bold text-lg">
                        {{ \App\Helpers\SettingHelper::get('company_name', 'Koperasi Merah Putih') }}
                    </h2>
                    <p>{{ \App\Helpers\SettingHelper::get('address', 'Alamat belum diatur') }}</p>
                    <p>Telp: {{ \App\Helpers\SettingHelper::get('phone', 'Telepon belum diatur') }}</p>
                    <p>{{ now()->format('d/m/Y H:i:s') }}</p>
                </div>

                {{-- Informasi Order --}}
                <div class="mb-2">
                    <p><strong>No. Order:</strong> {{ $lastOrder->order_id }}</p>
                    <p><strong>Kasir:</strong> {{ auth()->user()->name ?? 'Admin' }}</p>
                    <p><strong>Pelanggan:</strong> {{ $lastOrder->nama_pelanggan ?? $lastOrder->customer_name ?: 'Umum' }}
                    </p>
                    <p><strong>Metode:</strong> {{ ucfirst($lastOrder->payment_method) }}</p>
                </div>

                {{-- Tabel Item --}}
                <table class="w-full text-left border-t border-b my-2">
                    <thead>
                        <tr>
                            <th class="py-1">Item</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lastOrder->cart_items as $item)
                            <tr>
                                <td class="py-1">{{ $item['name'] }}</td>
                                <td class="text-right">{{ $item['quantity'] }}</td>
                                <td class="text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t">
                        {{-- Subtotal --}}
                        @php
                            $subtotal = collect($lastOrder->cart_items)->sum(fn($item) => $item['price'] * $item['quantity']);
                        @endphp
                        <tr>
                            <td colspan="3" class="text-right py-1">Subtotal:</td>
                            <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>

                        {{-- Diskon --}}
                        @if($lastOrder->discount_amount > 0)
                            <tr class="text-green-600">
                                <td colspan="3" class="text-right py-1">Diskon:</td>
                                <td class="text-right">-Rp {{ number_format($lastOrder->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif

                        {{-- Total --}}
                        <tr>
                            <th colspan="3" class="text-right py-1">Total:</th>
                            <th class="text-right">Rp {{ number_format($lastOrder->total, 0, ',', '.') }}</th>
                        </tr>

                        {{-- Pembayaran Tunai --}}
                        @if($lastOrder->payment_method === 'tunai' && $lastOrder->payment_amount)
                            <tr>
                                <td colspan="3" class="text-right py-1">Bayar:</td>
                                <td class="text-right">Rp {{ number_format($lastOrder->payment_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right py-1">Kembali:</td>
                                <td class="text-right">Rp
                                    {{ number_format($lastOrder->payment_amount - $lastOrder->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>

                <div class="text-center text-xs text-gray-500 mt-4">
                    {{ \App\Helpers\SettingHelper::get('footer_text', 'Terima kasih atas kunjungan Anda') }}<br>
                    *** Simpan struk sebagai bukti ***
                </div>
            </div>

            <div class="flex gap-2 justify-end mt-4 no-print">
                <flux:button wire:click="closeReceiptModal" variant="ghost">Tutup</flux:button>
                <flux:button x-on:click="window.open('{{ route('struk.pdf', $lastOrder->order_id) }}', '_blank')"
                    variant="primary" icon="printer">
                    Cetak Struk
                </flux:button>
            </div>
        @endif
    </flux:modal>
    @push('scripts')
        {{-- Midtrans Snap --}}
        <script
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}">
            </script>

        {{-- Midtrans Handler --}}
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('open-snap', (event) => {
                    const snapToken = event.snapToken;
                    if (!snapToken) {
                        alert('Gagal mendapatkan token pembayaran.');
                        return;
                    }
                    if (typeof window.snap !== 'undefined' && typeof window.snap.pay === 'function') {
                        window.snap.pay(snapToken, {
                            onSuccess: function (result) { @this.call('handlePaymentSuccess', result); },
                            onPending: function (result) { @this.call('handlePaymentPending', result); },
                            onError: function (result) { @this.call('handlePaymentError', result); },
                            onClose: function () { @this.call('handlePaymentClose'); }
                        });
                    } else {
                        alert('Midtrans Snap belum dimuat.');
                    }
                });
            });
        </script>
    @endpush
