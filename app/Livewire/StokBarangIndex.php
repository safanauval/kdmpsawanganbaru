<?php

namespace App\Livewire;

use App\Models\StokBarang;
use App\Models\Kategori;
use App\Models\Gudang;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class StokBarangIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $stokId;

    // Form properti StokBarang
    public $kode_barang, $nama_barang, $kategori_id, $gudang_id;
    public $stok, $harga_beli, $harga_jual, $satuan, $deskripsi, $gambar;

    // ========== RULES ==========

    protected function rules()
    {
        return [
            'kode_barang'  => 'required|unique:stok_barang,kode_barang' . ($this->editMode ? ',' . $this->stokId : ''),
            'nama_barang'  => 'required|string|max:100',
            'kategori_id'  => 'nullable|exists:kategori,id',
            'gudang_id'    => 'nullable|exists:gudang,id',
            'stok'         => 'required|integer|min:0',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'satuan'       => 'required|string|max:50',
            'deskripsi'    => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    // ========== LIFECYCLE ==========

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ========== CRUD STOK BARANG ==========

    public function openCreate()
    {
        $this->resetInput();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $item = StokBarang::with(['kategori', 'gudang'])->findOrFail($id);
        $this->stokId        = $id;
        $this->kode_barang   = $item->kode_barang;
        $this->nama_barang   = $item->nama_barang;
        $this->kategori_id   = $item->kategori_id;
        $this->gudang_id     = $item->gudang_id;
        $this->stok          = $item->stok;
        $this->harga_beli    = $item->harga_beli;
        $this->harga_jual    = $item->harga_jual;
        $this->satuan        = $item->satuan;
        $this->deskripsi     = $item->deskripsi;
        $this->editMode      = true;
        $this->showModal     = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'kode_barang'  => $this->kode_barang,
            'nama_barang'  => $this->nama_barang,
            'kategori_id'  => $this->kategori_id,
            'gudang_id'    => $this->gudang_id,
            'stok'         => $this->stok,
            'harga_beli'   => $this->harga_beli,
            'harga_jual'   => $this->harga_jual,
            'satuan'       => $this->satuan,
            'deskripsi'    => $this->deskripsi,
        ];

        if ($this->gambar && is_object($this->gambar)) {
            $data['gambar'] = file_get_contents($this->gambar->getRealPath());
        }

        if ($this->editMode) {
            StokBarang::where('id', $this->stokId)->update($data);
            $this->dispatch('notify', 'Stok barang berhasil diperbarui.', 'success');
        } else {
            StokBarang::create($data);
            $this->dispatch('notify', 'Stok barang berhasil ditambahkan.', 'success');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        StokBarang::delete($id);
        $this->dispatch('notify', 'Stok barang berhasil dihapus.', 'success');
    }

    // ========== HELPER ==========

    private function resetInput()
    {
        $this->reset([
            'stokId', 'kode_barang', 'nama_barang', 'kategori_id', 'gudang_id',
            'stok', 'harga_beli', 'harga_jual', 'satuan', 'deskripsi', 'gambar',
            'editMode',
        ]);
        $this->resetValidation();
    }

    // ========== RENDER ==========

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

        $kategoris = Kategori::orderBy('nama')->get();
        $gudangs = Gudang::orderBy('nama_gudang')->get();

        return view('components.stok-barang-index', [
            'stokBarang' => $stokBarang,
            'kategoris'  => $kategoris,
            'gudangs'    => $gudangs,
        ]);
    }
}