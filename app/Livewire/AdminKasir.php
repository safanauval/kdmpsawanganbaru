<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use App\Models\StokBarang;
use App\Models\Anggota;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminKasir extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $cart = [];
    public $showPaymentModal = false;
    public $customerName = '';
    public $paymentMethod = 'tunai';
    public $paymentAmount = 0;
    public $change = 0;
    public $snapToken;
    public $kodeAnggota = '';
    public $showReceiptModal = false;
    public $lastOrder = null;
    public $id_anggota = null;
    public $nama_anggota = '';

    // Properti diskon
    public $memberDiscountPercent = 0;
    public $nonMemberDiscountPercent = 0;
    public $discountAmount = 0;

    protected $listeners = ['cartUpdated' => '$refresh'];

    // ========== MOUNT & SESSION ==========

    public function mount()
    {
        // Muat diskon dari setting
        $this->memberDiscountPercent = (float) Setting::getValue('member_discount', 0);
        $this->nonMemberDiscountPercent = (float) Setting::getValue('non_member_discount', 0);

        // Muat keranjang dari session
        $this->loadCartFromSession();
    }

    /**
     * Simpan keranjang ke session
     */
    private function saveCartToSession()
    {
        session()->put('cart', $this->cart);
    }

    /**
     * Muat keranjang dari session
     */
    private function loadCartFromSession()
    {
        $this->cart = session()->get('cart', []);
    }

    // ========== COMPUTED PROPERTIES ==========

    public function getFilteredProductsProperty()
    {
        return StokBarang::query()
            ->with('kategori')
            ->when($this->search, function ($q) {
                $q->where('nama_barang', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function ($q) {
                $q->where('kategori_id', $this->selectedCategory);
            })
            ->orderBy('nama_barang')
            ->get();
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
        $product = StokBarang::findOrFail($productId);
        if ($product->stok <= 0) {
            session()->flash('error', 'Stok habis.');
            return;
        }

        $existing = collect($this->cart)->firstWhere('id', $productId);
        if ($existing) {
            $this->updateQuantity($productId, $existing['quantity'] + 1);
            return;
        }

        $this->cart[] = [
            'id' => $product->id,
            'name' => $product->nama_barang,
            'price' => $product->harga_jual,
            'quantity' => 1,
            'image_url' => $product->gambar_url,
            'stock' => $product->stok,
            'max_qty' => $product->stok,
        ];

        $this->saveCartToSession();
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }

        $this->cart = collect($this->cart)->map(function ($item) use ($productId, $quantity) {
            if ($item['id'] == $productId) {
                $product = StokBarang::find($productId);
                if ($product && $quantity > $product->stok) {
                    session()->flash('error', 'Stok tidak mencukupi.');
                    return $item;
                }
                $item['quantity'] = $quantity;
            }
            return $item;
        })->toArray();

        $this->saveCartToSession();
    }

    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->reject(fn($item) => $item['id'] == $productId)->toArray();
        $this->saveCartToSession();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discountAmount = 0;
        session()->forget('cart');
        $this->dispatch('cart-updated', cartCount: 0);
    }

    /**
     * Total akhir = subtotal - diskon (persentase)
     */
    public function getTotalProperty()
    {
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        $discountPercent = 0;
        if ($this->id_anggota) {
            $discountPercent = $this->memberDiscountPercent;
        } else {
            $discountPercent = $this->nonMemberDiscountPercent;
        }

        $this->discountAmount = $subtotal * $discountPercent / 100;
        return max(0, $subtotal - $this->discountAmount);
    }

    // ========== MODAL PEMBAYARAN ==========

    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong.');
            return;
        }
        $this->showPaymentModal = true;
        $this->paymentAmount = 0;
        $this->change = 0;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['customerName', 'paymentMethod', 'paymentAmount', 'change', 'id_anggota', 'nama_anggota']);
    }

    public function updatedPaymentAmount()
    {
        $this->change = max(0, (float) $this->paymentAmount - (float) $this->total);
    }

    // ========== PROSES PEMBAYARAN ==========

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required',
            'customerName' => 'nullable|string|max:100',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
        ]);

        if ($this->paymentMethod === 'tunai') {
            $this->validate(['paymentAmount' => 'required|numeric|min:' . $this->total]);

            $order = null;
            DB::transaction(function () use (&$order) {
                $order = Order::create([
                    'order_id' => 'KPDES-CASH-' . time(),
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? null,
                    'id_anggota' => $this->id_anggota,
                    'nama_anggota' => $this->nama_anggota,
                    'customer_name' => $this->customerName,
                    'total' => $this->total,
                    'discount_amount' => $this->discountAmount,
                    'payment_method' => 'tunai',
                    'payment_status' => 'paid',
                    'payment_amount' => $this->paymentAmount,
                    'cart_items' => $this->cart,
                ]);

                $this->reduceStock($this->cart);
            });

            $this->lastOrder = $order;
            $this->showReceiptModal = true;
            $this->clearCart();
            $this->closePaymentModal();
            $this->dispatch('notify', 'Pembayaran tunai berhasil!');
            return;

        } elseif ($this->paymentMethod === 'non-tunai') {
            // --- Transfer / QRIS via Midtrans Snap ---
            try {
                $orderId = 'KPDES-EPAY' . strtoupper(uniqid()) . '-' . time();
                $grossAmount = (int) $this->total;

                $transactionDetails = [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ];

                $customerDetails = [];
                if ($this->customerName) {
                    $customerDetails['first_name'] = $this->customerName;
                }

                $itemDetails = [];
                foreach ($this->cart as $item) {
                    $itemDetails[] = [
                        'id' => (string) $item['id'],
                        'price' => (int) $item['price'],
                        'quantity' => $item['quantity'],
                        'name' => substr($item['name'], 0, 50),
                    ];
                }

                if ($this->discountAmount > 0) {
                    $itemDetails[] = [
                        'id' => 'DISKON',
                        'price' => -(int) $this->discountAmount,
                        'quantity' => 1,
                        'name' => 'Diskon ' . ($this->id_anggota ? 'Anggota' : 'Non-Anggota'),
                    ];
                }

                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
                \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
                \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');

                $params = [
                    'transaction_details' => $transactionDetails,
                    'customer_details' => $customerDetails,
                    'item_details' => $itemDetails,
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                Order::create([
                    'order_id' => $orderId,
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name ?? null,
                    'id_anggota' => $this->id_anggota,
                    'nama_anggota' => $this->nama_anggota,
                    'customer_name' => $this->customerName,
                    'total' => $this->total,
                    'discount_amount' => $this->discountAmount,
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => 'pending',
                    'payment_amount' => $this->total,
                    'snap_token' => $snapToken,
                    'cart_items' => $this->cart,
                ]);

                $this->dispatch('open-snap', snapToken: $snapToken);
                $this->closePaymentModal();

            } catch (\Exception $e) {
                $this->dispatch('notify', 'Gagal terkoneksi dengan Midtrans: ' . $e->getMessage(), 'error');
            }
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
        $this->dispatch('notify', 'Pembayaran berhasil!');
    }

    public function handlePaymentPending($result)
    {
        $this->dispatch('notify', 'Pembayaran tertunda, menunggu konfirmasi.');
    }

    public function handlePaymentError($result)
    {
        $this->dispatch('notify', 'Pembayaran gagal: ' . ($result['status_message'] ?? 'Error'), 'error');
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
            $product = StokBarang::find($item['id']);
            if ($product) {
                $product->decrement('stok', $item['quantity']);
            }
        }
    }

    public function updatedKodeAnggota($value)
    {
        $value = trim($value);
        if (empty($value)) {
            $this->reset(['id_anggota', 'nama_anggota']);
            return;
        }

        $anggota = Anggota::where('kode_anggota', $value)->first();
        if ($anggota) {
            $this->id_anggota = $anggota->id_anggota;
            $this->nama_anggota = $anggota->nama_anggota;
            $this->customerName = $anggota->nama_anggota;
        } else {
            $this->reset(['id_anggota', 'nama_anggota']);
        }
    }

    public function render()
    {
        return view('components.adminkasir', [
            'products' => $this->filteredProducts,
            'categories' => $this->categories,
            'anggotas' => $this->anggotaList,
        ]);
    }
}