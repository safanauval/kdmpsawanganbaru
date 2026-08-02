<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use App\Models\StokBarang;
use App\Models\Anggota;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Kasir extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $cart = [];
    public $showPaymentModal = false;
    public $namaPelanggan = '';
    public $paymentMethod = 'tunai';
    public $paymentAmount = 0;
    public $change = 0;
    public $snapToken;
    public $menuType = 'toko';
    public $kodeAnggota = '';
    public $showReceiptModal = false;
    public $lastOrder = null;
    public $id_anggota = null;

    // Properti diskon
    public $memberDiscountPercent = 0;
    public $nonMemberDiscountPercent = 0;
    public $discountAmount = 0;

    protected $listeners = ['cartUpdated' => '$refresh'];

    // ========== MOUNT & SESSION ==========

    public function mount()
    {
        $this->memberDiscountPercent = (float) Setting::getValue('member_discount', 0);
        $this->nonMemberDiscountPercent = (float) Setting::getValue('non_member_discount', 0);
        $this->loadCartFromSession();
    }

    private function saveCartToSession()
    {
        session()->put('cart', $this->cart);
    }

    private function loadCartFromSession()
    {
        $this->cart = session()->get('cart', []);
    }

    // ========== COMPUTED PROPERTIES ==========

    public function getFilteredProductsProperty()
    {
        // 1. JIKA PILIH MENU RESTO (Tabel menu_tab)
        if ($this->menuType === 'resto') {
            return DB::table('menu') // Dipastikan menggunakan tabel menu_tab
                ->when($this->search, function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nama_menu', 'like', '%' . $this->search . '%')
                            ->orWhere('kode_menu', 'like', '%' . $this->search . '%');
                    });
                })
                ->get()
                ->map(function ($item) {
                    // Konversi binary BLOB menjadi Base64 Data URL
                    $gambarUrl = null;
                    if (!empty($item->gambar)) {
                        $gambarUrl = 'data:image/jpeg;base64,' . base64_encode($item->gambar);
                    }

                    return (object) [
                        'id'             => $item->id,
                        'nama_barang'    => $item->nama_menu ?? $item->nama_barang ?? 'Menu Resto',
                        'harga_jual'     => $item->harga ?? $item->harga_jual ?? 0,
                        'stok'           => $item->stok ?? 999,
                        'gambar_url'     => $gambarUrl, // <-- Sudah menjadi Base64 string yang siap dirender <img src="...">
                        'estimasi_waktu' => $item->estimasi_waktu ?? 15,
                        'is_resto'       => true,
                    ];
                });
        }

        // 2. JIKA PILIH MENU TOKO (Tabel stok_barang)
        return StokBarang::query()
            ->with('kategori')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_barang', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('kategori_id', $this->selectedCategory);
            })
            ->orderBy('nama_barang')
            ->get()
            ->map(function ($item) {
                $item->is_resto = false;
                return $item;
            });
    }

    public function getCategoriesProperty()
    {
        return \App\Models\Kategori::orderBy('nama')->get();
    }

    public function getAnggotaListProperty()
    {
        return Anggota::orderBy('nama_anggota')->get();
    }

    // ========== KERANJANG & DISKON ==========

    public function addToCart($productId)
    {
        if ($this->menuType === 'resto') {
            // 1. Data dari tabel Menu Resto (menu_tab)
            $product = DB::table('menu')->where('id', $productId)->first();
            if (!$product) {
                $this->dispatch('notify', 'Menu resto tidak ditemukan.', 'error');
                return;
            }

            if (($product->stok) <= 0) {
                $this->dispatch('notify', 'Porsi resto habis.', 'error');
                return;
            }

            // --- KONVERSI GAMBAR BLOB KE BASE64 DATA URL UNTUK CART ---
            $imageUrl = null;
            if (!empty($product->gambar)) {
                $imageUrl = 'data:image/jpeg;base64,' . base64_encode($product->gambar);
            }

            $cartId   = 'resto_' . $product->id;
            $name     = $product->nama_menu;
            $price    = $product->harga;
            $stock    = $product->stok ?? 999;
            $isResto  = true;
            $estimasi = $product->estimasi_waktu ?? 15;
        } else {
            // 2. Data dari tabel Stok Barang Toko (stok_barang)
            $product = StokBarang::findOrFail($productId);
            if ($product->stok <= 0) {
                $this->dispatch('notify', 'Stok habis.', 'error');
                return;
            }

            $cartId   = 'toko_' . $product->id;
            $name     = $product->nama_barang;
            $price    = $product->harga_jual;
            $stock    = $product->stok;
            $imageUrl = $product->gambar_url; // Sudah dalam bentuk URL/Base64 dari model
            $isResto  = false;
            $estimasi = 0;
        }

        $existing = collect($this->cart)->firstWhere('id', $cartId);
        if ($existing) {
            $this->updateQuantity($cartId, $existing['quantity'] + 1);
            return;
        }

        $this->cart[] = [
            'id'             => $cartId,
            'original_id'    => $productId,
            'name'           => $name,
            'price'          => $price,
            'quantity'       => 1,
            'image_url'      => $imageUrl, // <-- Gambar di keranjang sekarang sudah valid Data URL
            'stock'          => $stock,
            'max_qty'        => $stock,
            'is_resto'       => $isResto,
            'estimasi_waktu' => $estimasi,
        ];

        $this->saveCartToSession();
    }

    public function updateQuantity($cartId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($cartId);
            return;
        }

        $this->cart = collect($this->cart)->map(function ($item) use ($cartId, $quantity) {
            if ($item['id'] == $cartId) {
                if ($quantity > $item['stock']) {
                    $this->dispatch('notify', 'Stok tidak mencukupi.', 'error');
                    return $item;
                }
                $item['quantity'] = $quantity;
            }
            return $item;
        })->toArray();

        $this->saveCartToSession();
    }

    public function removeFromCart($cartId)
    {
        $this->cart = collect($this->cart)->reject(fn($item) => $item['id'] == $cartId)->toArray();
        $this->saveCartToSession();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discountAmount = 0;
        session()->forget('cart');
        $this->dispatch('cart-updated', cartCount: 0);
    }

    public function getTotalProperty()
    {
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $discountPercent = $this->id_anggota ? $this->memberDiscountPercent : $this->nonMemberDiscountPercent;
        $this->discountAmount = $subtotal * $discountPercent / 100;
        return max(0, $subtotal - $this->discountAmount);
    }

    // ========== GENERASI NOMOR ANTRIAN RESTO ==========

    private function generateNoAntrian()
    {
        // Cek apakah di keranjang terdapat minimal 1 menu resto
        $hasRestoItem = collect($this->cart)->contains('is_resto', true);
        if (!$hasRestoItem) {
            return null; // Tidak perlu nomor antrian jika hanya belanja barang toko
        }

        // Ambil transaksi hari ini yang memiliki nomor antrian
        $todayCount = Order::whereDate('created_at', now()->today())
            ->whereNotNull('no_antrian')
            ->count();

        $nextNumber = $todayCount + 1;
        return 'A-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // Format: A-001, A-002, dst.
    }

    // ========== MODAL PEMBAYARAN ==========

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', 'Keranjang masih kosong.', 'error');
            return;
        }
        $this->showPaymentModal = true;
        $this->paymentAmount = 0;
        $this->change = 0;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['paymentAmount', 'change']);
    }

    public function updatedPaymentAmount()
    {
        $this->change = max(0, (float) $this->paymentAmount - (float) $this->total);
    }

    // ========== AUTO-FILL NAMA PELANGGAN ==========

    public function updatedKodeAnggota($value)
    {
        $value = trim($value);

        if (empty($value)) {
            $this->id_anggota = null;
            return;
        }

        $anggota = Anggota::where('kode_anggota', $value)->first();

        if ($anggota) {
            $this->id_anggota    = $anggota->id_anggota;
            $this->namaPelanggan = $anggota->nama_anggota;
        } else {
            $this->id_anggota = null;
        }
    }

    // ========== PROSES PEMBAYARAN ==========

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required|in:tunai,non-tunai',
            'id_anggota'    => 'nullable|exists:anggota,id_anggota',
        ]);

        $noAntrian = $this->generateNoAntrian();

        // ========== PEMBAYARAN TUNAI ==========
        if ($this->paymentMethod === 'tunai') {
            $this->validate(['paymentAmount' => 'required|numeric|min:' . $this->total]);

            $order = null;
            DB::transaction(function () use (&$order, $noAntrian) {
                $order = Order::create([
                    'order_id'        => 'KPDES-CASH-' . time(),
                    'no_antrian'      => $noAntrian,
                    'user_id'         => auth()->id(),
                    'user_name'       => auth()->user()->name ?? null,
                    'id_anggota'      => $this->id_anggota,
                    'nama_pelanggan'  => $this->namaPelanggan ?: 'Umum',
                    'total'           => $this->total,
                    'discount_amount' => $this->discountAmount,
                    'payment_method'  => 'tunai',
                    'payment_status'  => 'paid',
                    'payment_amount'  => $this->paymentAmount,
                    'cart_items'      => $this->cart,
                ]);

                $this->reduceStock($this->cart);
            });

            $this->lastOrder = $order;
            $this->showReceiptModal = true;
            $this->clearCart();
            $this->closePaymentModal();
            $this->dispatch('notify', 'Pembayaran tunai berhasil!', 'success');
            return;
        }

        // ========== PEMBAYARAN NON-TUNAI (MIDTRANS SNAP) ==========
        try {
            $orderId = 'KPDES-' . strtoupper(uniqid()) . '-' . time();
            $grossAmount = (int) $this->total;

            $transactionDetails = [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ];

            $customerDetails = [];
            if ($this->namaPelanggan) {
                $customerDetails['first_name'] = $this->namaPelanggan;
            }

            $itemDetails = [];
            foreach ($this->cart as $item) {
                $itemDetails[] = [
                    'id'       => (string) $item['id'],
                    'price'    => (int) round($item['price']),
                    'quantity' => (int) $item['quantity'],
                    'name'     => substr($item['name'], 0, 50),
                ];
            }

            \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
            \Midtrans\Config::$clientKey    = config('services.midtrans.client_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized  = config('services.midtrans.is_sanitized');
            \Midtrans\Config::$is3ds        = config('services.midtrans.is_3ds');

            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details'    => $customerDetails,
                'item_details'        => $itemDetails,
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            Order::create([
                'order_id'        => $orderId,
                'no_antrian'      => $noAntrian,
                'user_id'         => auth()->id(),
                'user_name'       => auth()->user()->name ?? null,
                'id_anggota'      => $this->id_anggota,
                'nama_pelanggan'  => $this->namaPelanggan ?: 'Umum',
                'total'           => $this->total,
                'discount_amount' => $this->discountAmount,
                'payment_method'  => $this->paymentMethod,
                'payment_status'  => 'pending',
                'payment_amount'  => $this->total,
                'snap_token'      => $snapToken,
                'cart_items'      => $this->cart,
            ]);

            $this->dispatch('open-snap', snapToken: $snapToken);
            $this->closePaymentModal();

        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            $this->dispatch('notify', 'Gagal memproses pembayaran.', 'error');
        }
    }

    // ========== CALLBACK MIDTRANS ==========

    public function handlePaymentSuccess($result)
    {
        $order = Order::where('order_id', $result['order_id'])->first();
        if ($order) {
            DB::transaction(function () use ($order) {
                $order->update(['payment_status' => 'paid']);
                $this->reduceStock($order->cart_items);
            });

            $this->lastOrder = $order;
            $this->showReceiptModal = true;
        }

        $this->clearCart();
        $this->dispatch('notify', 'Pembayaran berhasil!', 'success');
    }

    public function handlePaymentPending($result)
    {
        $this->dispatch('notify', 'Pembayaran tertunda.', 'warning');
    }

    public function handlePaymentError($result)
    {
        $this->dispatch('notify', 'Pembayaran gagal.', 'error');
    }

    public function handlePaymentClose()
    {
        $this->dispatch('notify', 'Pembayaran dibatalkan.', 'warning');
    }

    // ========== LAIN-LAIN ==========

    public function printReceipt()
    {
        $this->dispatch('print-receipt');
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->lastOrder = null;
    }

    private function reduceStock(array $cartItems): void
    {
        foreach ($cartItems as $item) {
            $originalId = $item['original_id'] ?? $item['id'];

            if (!empty($item['is_resto'])) {
                // Potong stok porsi di tabel menu (Menu Resto)
                DB::table('menu')->where('id', $originalId)->decrement('stok', $item['quantity']);
            } else {
                // Potong stok di tabel stok_barang (Toko)
                $product = StokBarang::find($originalId);
                if ($product) {
                    $product->decrement('stok', $item['quantity']);
                }
            }
        }
    }

    public function render()
    {
        return view('components.kasir', [
            'products'   => $this->filteredProducts,
            'categories' => $this->categories,
            'anggotas'   => $this->anggotaList,
        ]);
    }
}