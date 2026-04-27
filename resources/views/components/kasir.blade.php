<div class="flex gap-6 h-full">
    <!-- Bagian Kiri: Pencarian & Daftar Produk -->
    <div class="flex-1 min-w-0">
        <!-- Search & Filter -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow p-4 mb-4 border border-zinc-200 dark:border-zinc-700">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="🔍 Cari produk..."
                        icon="magnifying-glass" clearable />
                </div>
                <div class="sm:w-48">
                    <flux:select wire:model.live="selectedCategory" placeholder="Semua Kategori">
                        <flux:select.option value="">Semua</flux:select.option>
                        @foreach($categories as $cat)
                            <flux:select.option value="{{ $cat->id }}">{{ $cat->nama }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>

        <!-- Grid Produk -->
        <div class="flex flex-wrap min-w-[200px] justify-between border-neutral-200 dark:border-neutral-700 p-2 gap-2">
            @foreach($products as $product)
                <flux:card class="flex-1 rounded-xl border border-b p-4">
                    <!-- Gambar -->
                    <div
                        class="w-full h-32 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-700 mb-3 flex items-center justify-center">
                        @if($product->gambar_url)
                            <img src="{{ $product->gambar_url }}" alt="{{ $product->nama_barang }}"
                                class="object-cover w-full h-full">
                        @else
                            <flux:icon.photo class="w-10 h-10 text-zinc-400" />
                        @endif
                    </div>

                    <h5 class="font-semibold text-sm text-center line-clamp-2 mb-1">{{ $product->nama_barang }}</h5>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">Rp
                        {{ number_format($product->harga_jual, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-zinc-500">Stok: {{ $product->stok }}</p>

                    @if($product->stok > 0)
                        <div class="mt-auto pt-2 w-full">
                            @php
                                $cartItem = collect($cart)->firstWhere('id', $product->id);
                                $qty = $cartItem['quantity'] ?? 0;
                            @endphp
                            @if($qty > 0)
                                <div class="flex items-center justify-between">
                                    <flux:button wire:click="updateQuantity({{ $product->id }}, {{ $qty - 1 }})" size="xs"
                                        icon="minus" variant="danger" />
                                    <span class="font-bold">{{ $qty }}</span>
                                    <flux:button wire:click="updateQuantity({{ $product->id }}, {{ $qty + 1 }})" size="xs"
                                        icon="plus" variant="primary" :disabled="$product->stok <= $qty" />
                                </div>
                            @else
                                <flux:button wire:click="addToCart({{ $product->id }})" class="w-full" variant="primary"
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

    <!-- Bagian Kanan: Keranjang Belanja (Sidebar) -->
    <div class="w-[340px] flex-shrink-0">
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl shadow p-4 border border-zinc-200 dark:border-zinc-700 sticky top-6 max-h-[calc(100vh-8rem)] flex flex-col">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <flux:icon.shopping-cart class="w-5 h-5" />
                    Keranjang ({{ count($cart) }})
                </h2>
                @if(count($cart))
                    <flux:button wire:click="clearCart" size="xs" variant="ghost" class="text-red-500">Kosongkan
                    </flux:button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 pr-1">
                @forelse($cart as $item)
                    <div class="flex items-start gap-3 p-2 rounded-lg bg-zinc-50 dark:bg-zinc-700">
                        <img src="{{ $item['image_url'] ?? asset('img/placeholder.svg') }}"
                            class="w-12 h-12 rounded object-cover" alt="{{ $item['name'] }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400">Rp
                                {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                            <div class="flex items-center gap-1 mt-1">
                                <flux:button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                    size="xs" icon="minus" variant="filled" />
                                <span class="w-6 text-center text-sm font-bold">
                                    <flux:input type="number" max="{{ $item['stock'] }}"
                                        wire:model="{{ $item['quantity'] }}" class="text-center" />
                                </span>
                                <flux:button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                    size="xs" icon="plus" variant="filled"
                                    :disabled="$item['quantity'] >= $item['stock']" />
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold">Rp
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </p>
                            <button wire:click="removeFromCart({{ $item['id'] }})"
                                class="text-xs text-red-500 hover:underline">Hapus</button>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-zinc-500 py-8">Keranjang kosong</p>
                @endforelse
            </div>

            @if(count($cart))
                <div class="border-t pt-3 mt-3 space-y-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                    <flux:button wire:click="openPaymentModal" variant="primary" class="w-full" icon="credit-card">Bayar
                        Sekarang</flux:button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <flux:modal wire:model="showPaymentModal" title="Pembayaran" class="max-w-md">
        <div class="space-y-4">
            <div class="bg-zinc-100 dark:bg-zinc-700 p-3 rounded">
                <div class="flex justify-between font-bold">
                    <span>Total Tagihan</span>
                    <span class="text-green-600">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <flux:field>
                <flux:label>Nama Pelanggan (opsional)</flux:label>
                <flux:input wire:model="customerName" placeholder="Nama" />
            </flux:field>

            <flux:field>
                <flux:label>Metode Pembayaran</flux:label>
                <flux:select wire:model="paymentMethod">
                    <option value="tunai">💵 Tunai</option>
                    <option value="transfer">🏦 Transfer</option>
                    <option value="qris">📱 QRIS</option>
                </flux:select>
            </flux:field>

            @if($paymentMethod === 'tunai')
                <flux:field>
                    <flux:label>Jumlah Bayar</flux:label>
                    <flux:input type="number" wire:model.live="paymentAmount" min="0" />
                </flux:field>
                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded flex justify-between">
                    <span>Kembalian</span>
                    <span class="font-bold text-blue-600">Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="closePaymentModal" variant="ghost">Batal</flux:button>
                <flux:button wire:click="processPayment" variant="primary"
                    :disabled="$paymentMethod === 'tunai' && $paymentAmount < $this->total">Proses Pembayaran
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>