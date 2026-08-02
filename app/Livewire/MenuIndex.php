<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Menu;
use App\Models\Kategori;

class MenuIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;

    // Form Fields
    public $menuId;
    public $kode_menu;
    public $nama_menu;
    public $kategori_id;
    public $stok = 999;
    public $harga_hpp = 0;
    public $harga = 0;
    public $satuan = 'porsi';
    public $estimasi_waktu = 15; // Estimasi waktu penyajian (menit)
    public $deskripsi;
    public $gambar;

    protected function rules()
    {
        return [
            // PERBAIKAN: Diubah dari unique:menu menjadi unique:menu
            'kode_menu'      => 'required|string|max:20|unique:menu,kode_menu,' . $this->menuId,
            'nama_menu'      => 'required|string|max:100',
            'kategori_id'    => 'nullable|exists:kategori,id',
            'stok'           => 'required|numeric|min:0',
            'harga_hpp'      => 'required|numeric|min:0',
            'harga'          => 'required|numeric|min:0',
            'satuan'         => 'required|string|max:50',
            'estimasi_waktu' => 'required|numeric|min:1',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $this->menuId         = $menu->id;
            $this->kode_menu      = $menu->kode_menu;
            $this->nama_menu      = $menu->nama_menu;
            $this->kategori_id    = $menu->kategori_id;
            $this->stok           = $menu->stok;
            $this->harga_hpp      = $menu->harga_hpp;
            $this->harga          = $menu->harga;
            $this->satuan         = $menu->satuan;
            $this->estimasi_waktu = $menu->estimasi_waktu ?? 15;
            $this->deskripsi      = $menu->deskripsi;
            $this->editMode       = true;
            $this->showModal      = true;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'kode_menu'      => $this->kode_menu,
            'nama_menu'      => $this->nama_menu,
            'kategori_id'    => $this->kategori_id ?: null,
            'stok'           => $this->stok,
            'harga_hpp'      => $this->harga_hpp,
            'harga'          => $this->harga,
            'satuan'         => $this->satuan,
            'estimasi_waktu' => $this->estimasi_waktu,
            'deskripsi'      => $this->deskripsi,
        ];

        // Jika ada upload gambar baru, simpan konten binary gambar
        if ($this->gambar && is_object($this->gambar)) {
            $data['gambar'] = file_get_contents($this->gambar->getRealPath());
        }

        if ($this->editMode) {
            $menu = Menu::find($this->menuId);
            if ($menu) {
                $menu->update($data);
                $this->dispatch('notify', 'Menu berhasil diperbarui!', 'success');
            }
        } else {
            Menu::create($data);
            $this->dispatch('notify', 'Menu berhasil ditambahkan!', 'success');
        }

        $this->showModal = false;
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $menu->delete();
            $this->dispatch('notify', 'Menu berhasil dihapus!', 'success');
        }
    }

    private function resetInputFields()
    {
        $this->reset([
            'menuId', 'kode_menu', 'nama_menu', 'kategori_id', 
            'stok', 'harga_hpp', 'harga', 'satuan', 
            'estimasi_waktu', 'deskripsi', 'gambar'
        ]);
        $this->stok = 999;
        $this->satuan = 'porsi';
        $this->estimasi_waktu = 15;
        $this->resetValidation();
    }

    public function render()
    {
        $menuResto = Menu::with('kategori')
            ->when($this->search, function ($q) {
                $q->where('nama_menu', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_menu', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        $kategoris = Kategori::orderBy('nama')->get();

        return view('components.menu-index', [
            'menuResto' => $menuResto,
            'kategoris' => $kategoris,
        ]);
    }
}