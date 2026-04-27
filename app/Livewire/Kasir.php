<?php

namespace App\Livewire;

use App\Models\StokBarang;
use Livewire\Component;

class Kasir extends Component
{
    public $search = '';
    public $selectedCategory = '';
    public $cart = [];               // item: [ 'id' => $productId, 'quantity' => int, 'price' => ..., 'name' => ..., 'image_url' => ... ]
    public $showPaymentModal = false;
    public $customerName = '';
    public $paymentMethod = 'tunai';
    public $paymentAmount = 0;
    public $change = 0;

    protected $listeners = ['cartUpdated' => '$refresh']; // agar keranjang ter-update (opsional)

    // computed property untuk produk yang difilter (tidak perlu function, bisa di render)
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

    // computed property untuk daftar kategori
    public function getCategoriesProperty()
    {
        return \App\Models\Kategori::orderBy('nama')->get();
    }

    // computed property untuk menghitung total
    public function getTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    // tambah ke keranjang
    public function addToCart($productId)
    {
        $product = StokBarang::findOrFail($productId);
        if ($product->stok <= 0) {
            session()->flash('error', 'Stok habis.');
            return;
        }

        // cek apakah sudah ada di keranjang
        $existing = collect($this->cart)->firstWhere('id', $productId);
        if ($existing) {
            // tambah quantity
            $this->updateQuantity($productId, $existing['quantity'] + 1);
            return;
        }

        // tambahkan item baru
        $this->cart[] = [
            'id' => $product->id,
            'name' => $product->nama_barang,
            'price' => $product->harga_jual,
            'quantity' => 1,
            'image_url' => $product->gambar_url, // accessor di model
            'stock' => $product->stok,
            'max_qty' => $product->stok,
        ];
    }

    // update quantity item
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }

        $this->cart = collect($this->cart)->map(function ($item) use ($productId, $quantity) {
            if ($item['id'] == $productId) {
                // cek stok
                $product = StokBarang::find($productId);
                if ($product && $quantity > $product->stok) {
                    session()->flash('error', 'Stok tidak mencukupi.');
                    return $item;
                }
                $item['quantity'] = $quantity;
            }
            return $item;
        })->toArray();
    }

    // hapus dari keranjang
    public function removeFromCart($productId)
    {
        $this->cart = collect($this->cart)->reject(fn($item) => $item['id'] == $productId)->toArray();
    }

    // kosongkan keranjang
    public function clearCart()
    {
        $this->cart = [];
    }

    // buka modal pembayaran
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

    // tutup modal
    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['customerName', 'paymentMethod', 'paymentAmount', 'change']);
    }

    // hitung kembalian secara realtime
    public function updatedPaymentAmount()
    {
        $this->change = max(0, $this->paymentAmount - $this->total);
    }

    // proses pembayaran (dummy)
    public function processPayment()
    {
        // validasi sederhana
        if ($this->paymentMethod === 'tunai' && $this->paymentAmount < $this->total) {
            session()->flash('error', 'Jumlah bayar kurang.');
            return;
        }

        // kurangi stok produk
        foreach ($this->cart as $item) {
            $product = StokBarang::find($item['id']);
            if ($product) {
                $product->decrement('stok', $item['quantity']);
            }
        }

        // reset keranjang & modal
        $this->cart = [];
        $this->closePaymentModal();
        session()->flash('success', 'Transaksi berhasil! Stok telah diperbarui.');
    }

    public function render()
    {
        return view('components.kasir', [
            'products' => $this->filteredProducts,
            'categories' => $this->categories,
        ]);
    }
}