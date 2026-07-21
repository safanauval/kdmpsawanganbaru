<?php

namespace App\Livewire;

use App\Models\Gudang;
use Livewire\Component;
use Livewire\WithPagination;

class GudangIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $gudangId, $kode_gudang, $nama_gudang, $alamat, $telepon, $contact_person;

    protected function rules()
    {
        return [
            'kode_gudang' => 'required|unique:gudang,kode_gudang' . ($this->editMode ? ',' . $this->gudangId : ''),
            'nama_gudang' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:150',
            'telepon' => 'nullable|string|max:15',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetInput();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $gudang = Gudang::findOrFail($id);
        $this->gudangId = $id;
        $this->kode_gudang = $gudang->kode_gudang;
        $this->nama_gudang = $gudang->nama_gudang;
        $this->alamat = $gudang->alamat;
        $this->telepon = $gudang->telepon;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'kode_gudang' => $this->kode_gudang,
            'nama_gudang' => $this->nama_gudang,
            'alamat' => $this->alamat,
            'telepon' => $this->telepon,
        ];

        if ($this->editMode) {
            Gudang::where('id', $this->gudangId)->update($data);
            $this->dispatch('notify', 'Gudang berhasil diperbaharui.', 'success');
        } else {
            Gudang::create($data);
            $this->dispatch('notify', 'Gudang berhasil ditambahkan.', 'success');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        Gudang::findOrFail($id)->delete();
        $this->dispatch('notify', 'Gudang berhasil dihapus.', 'success');
    }

    private function resetInput()
    {
        $this->reset(['showModal', 'gudangId', 'kode_gudang', 'nama_gudang', 'alamat', 'telepon', 'contact_person']);
    }

    public function cancelModal()
    {
        $this->resetInput();
    }

    public function render()
    {
        $gudang = Gudang::query()
            ->when($this->search, function ($q) {
                $q->where('nama_gudang', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_gudang', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama_gudang')
            ->paginate(10);

        return view('components.gudang-index', [
            'gudang' => $gudang,
        ]);
    }
}