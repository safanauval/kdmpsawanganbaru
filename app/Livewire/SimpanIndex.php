<?php

namespace App\Livewire;

use App\Models\Simpan;
use App\Models\Anggota;
use Livewire\Component;
use Livewire\WithPagination;

class SimpanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $simpanId, $id_anggota, $jenis, $jumlah, $metode_pembayaran, $tanggal;

    protected function rules()
    {
        return [
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'jenis' => 'required|in:pokok,wajib',
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:cash,qris',
            'tanggal' => 'required|date',
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
        $simpan = Simpan::with('anggota')->findOrFail($id);
        $this->simpanId = $id;
        $this->id_anggota = $simpan->id_anggota;
        $this->jenis = $simpan->jenis;
        $this->jumlah = $simpan->jumlah;
        $this->metode_pembayaran = $simpan->metode_pembayaran;
        $this->tanggal = $simpan->tanggal;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'id_anggota' => $this->id_anggota,
            'jenis' => $this->jenis,
            'jumlah' => $this->jumlah,
            'metode_pembayaran' => $this->metode_pembayaran,
            'tanggal' => $this->tanggal,
        ];

        if ($this->editMode) {
            Simpan::where('id', $this->simpanId)->update($data);
            session()->flash('success', 'Simpanan diperbarui.');
        } else {
            Simpan::create($data);
            session()->flash('success', 'Simpanan ditambahkan.');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        Simpan::destroy($id);
        session()->flash('success', 'Simpanan dihapus.');
    }

    private function resetInput()
    {
        $this->reset(['id_anggota', 'jenis', 'jumlah', 'metode_pembayaran', 'tanggal', 'simpanId', 'editMode']);
        $this->resetValidation();
    }

    public function render()
    {
        $simpanans = Simpan::with('anggota') // eager loading
            ->when($this->search, function ($q) {
                $q->whereHas('anggota', function ($q) {
                    $q->where('nama_anggota', 'like', '%' . $this->search . '%')
                        ->orWhere('kode_anggota', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $anggotas = Anggota::orderBy('nama_anggota')->get();

        return view('components.simpan-index', compact('simpanans', 'anggotas'));
    }
}