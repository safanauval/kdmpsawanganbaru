<?php

namespace App\Livewire;

use App\Models\Kategori;
use Livewire\Component;
use Livewire\WithPagination;

class KategoriIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Untuk Edit
    public $showEditModal = false;
    public $selectedKategoriId;
    public $nama;

    // Untuk Create
    public $showCreateModal = false;
    public $namaBaru;

    protected $rules = [
        'nama' => 'required|string|max:255|unique:kategori,nama',
    ];

    protected $messages = [
        'nama.required' => 'Nama kategori wajib diisi.',
        'nama.unique' => 'Nama kategori sudah ada.',
    ];

    // Create methods
    public function openCreate()
    {
        $this->reset(['namaBaru']);
        $this->showCreateModal = true;
    }

    public function store()
    {
        $this->validate([
            'namaBaru' => 'required|string|max:255|unique:kategori,nama',
        ]);

        Kategori::create(['nama' => $this->namaBaru]);

        $this->reset(['showCreateModal', 'namaBaru']);
        $this->dispatch('notify', 'Kategori berhasil ditambahkan.', 'success');
    }

    // Edit methods
    public function openEdit($id)
    {
        $ktgori = Kategori::findOrFail($id);
        $this->selectedKategoriId = $id;
        $this->nama = $ktgori->nama;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required|string|max:30|unique:kategori,nama,' . $this->selectedKategoriId,
        ]);

        $ktgori = Kategori::findOrFail($this->selectedKategoriId);
        $ktgori->update(['nama' => $this->nama]);

        $this->reset(['showEditModal', 'selectedKategoriId', 'nama']);
        $this->dispatch('notify', 'Kategori berhasil diperbarui.', 'success');
    }

    public function cancelEdit()
    {
        $this->reset(['showEditModal', 'selectedKategoriId', 'nama']);
    }

    public function cancelCreate()
    {
        $this->reset(['showCreateModal', 'namaBaru']);
    }

     public function delete($id)
    {
        Kategori::findOrFail($id)->delete();
        $this->dispatch('notify', 'Kategori berhasil dihapus.', 'success');
    }

    public function render()
    {
        $kategori = Kategori::query()
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama')
            ->paginate(10);

        return view('components.kategori-index', [
            'kategori' => $kategori,
        ]);
    }
}