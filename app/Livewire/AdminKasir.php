<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use App\Models\StokBarang;
use App\Models\Anggota;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

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

    // ========== DISKON (REAL-TIME) ==========

    public function calculateDiscount()
    {
        if (empty($this->cart)) {
            $this->discountAmount = 0;
            return;
        }

        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $discountPercent = $this->id_anggota ? $this->memberDiscountPercent : $this->nonMemberDiscountPercent;
        $this->discountAmount = $subtotal * $discountPercent / 100;
    }

    /**
     * Total akhir = subtotal - diskon
     */
    public function getTotalProperty()
    {
        $this->calculateDiscount(); // ← Hitung ulang setiap kali diakses
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return max(0, $subtotal - $this->discountAmount);
    }

    // ========== KERANJANG ==========

    public function addToCart($productId)
    {
        $product = StokBarang::findOrFail($productId);
        if ($product->stok <= 0) {
            $this->dispatch('notify', 'Stok habis.', 'error');
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
        $this->calculateDiscount(); // ← Hitung ulang diskon
        $this->dispatch('cart-updated');
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
                    $this->dispatch('notify', 'Stok tidak mencukupi.', 'error');
                    return $item;
                }
                $item['quantity'] = $quantity;
            }
            return $item;
        })->toArray();

        $this->saveCartToSession();
        $this->calculateDiscount(); // ← Jika ada
        $this->dispatch('cart-updated');
    }
    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->reject(fn($item) => $item['id'] == $productId)->toArray();
        $this->saveCartToSession();
        $this->calculateDiscount(); // ← Hitung ulang diskon
        $this->dispatch('cart-updated');
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discountAmount = 0;
        session()->forget('cart');
        $this->dispatch('cart-updated', cartCount: 0);
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

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required|in:tunai,non-tunai',
            'customerName' => 'nullable|string|max:100',
            'id_anggota' => 'nullable|exists:anggota,id_anggota',
        ]);

        // ========== PEMBAYARAN TUNAI ==========
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
        }

        // ========== PEMBAYARAN NON-TUNAI (MIDTRANS SNAP) ==========
        try {
            $orderId = 'KPDES-' . strtoupper(uniqid()) . '-' . time();
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
                    'price' => (int) round($item['price']),
                    'quantity' => (int) $item['quantity'],
                    'name' => substr($item['name'], 0, 50),
                ];
            }

            // Konfigurasi Midtrans (SUDAH TERBUKTI BERFUNGSI)
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$clientKey = config('services.midtrans.client_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('services.midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('services.midtrans.is_3ds');

            $params = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan order
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

            // Buka popup Midtrans
            $this->dispatch('open-snap', snapToken: $snapToken);
            $this->closePaymentModal();

        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage());
            $this->dispatch('notify', 'Gagal memproses pembayaran. Silakan coba lagi.', 'error');
        }
    }

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

    public function printReceipt($orderId)
    {
        // Cari order
        $order = Order::where('order_id', $orderId)->firstOrFail();

        // Generate PDF
        $pdf = Pdf::loadView('pdf.struk', compact('order'));

        // Set ukuran kertas struk (58mm x 80mm)
        $pdf->setPaper([0, 0, 164.41, 226.77], 'portrait');

        // Set options
        $pdf->setOptions([
            'defaultFont' => 'Courier New',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        // Stream PDF ke browser (tampil dan bisa di-download)
        return Response::stream(
            function () use ($pdf) {
                echo $pdf->output();
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="struk-' . $orderId . '.pdf"',
            ]
        );
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
            $this->id_anggota = null;
            $this->calculateDiscount(); // ← Update diskon saat anggota berubah
            return;
        }

        $anggota = Anggota::where('kode_anggota', $value)->first();
        if ($anggota) {
            $this->id_anggota = $anggota->id_anggota;
            $this->customerName = $anggota->nama_anggota;
        } else {
            $this->id_anggota = null;
        }
        $this->calculateDiscount(); // ← Update diskon saat anggota berubah
    }

    public function render()
    {
        return view('components.kasir', [
            'products' => $this->filteredProducts,
            'categories' => $this->categories,
            'anggotas' => $this->anggotaList,
        ]);
    }
}