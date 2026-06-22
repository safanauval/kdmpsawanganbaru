<?php

namespace App\Livewire;

use App\Models\StokBarang;
use Livewire\Component;
use Livewire\WithPagination;

class StokBarangIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $stokBarang = StokBarang::with(['kategori', 'gudang'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('kode_barang', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_barang', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('components.stok-barang-index', [
            'stokBarang' => $stokBarang,
        ]);
    }

    public function delete($id)
    {
        $barang = StokBarang::findOrFail($id);
        $barang->delete();
        session()->flash('success', 'Barang berhasil dihapus.');
    }
}