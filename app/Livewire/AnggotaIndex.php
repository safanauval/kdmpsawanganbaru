<?php

namespace App\Livewire;

use App\Models\Anggota;
use App\Models\Simpan;
use Livewire\Component;
use Livewire\WithPagination;

class AnggotaIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $anggotaId, $kode_anggota, $nama_anggota, $email_anggota, $telepon_anggota, $alamat_anggota, $tanggal_masuk;

    protected function rules()
    {
        return [
            'kode_anggota'   => 'required|unique:anggota,kode_anggota' . ($this->editMode ? ',' . $this->anggotaId : ''),
            'nama_anggota'   => 'required|string|max:80',
            'email_anggota'  => 'nullable|email|unique:anggota,email_anggota' . ($this->editMode ? ',' . $this->anggotaId : ''),
            'telepon_anggota'=> 'nullable|string|max:20',
            'alamat_anggota' => 'nullable|string',
            'tanggal_masuk'  => 'required|date',
        ];
    }

    protected function generateKodeAnggota(): string
    {
        $prefix = 'KMPSB'; // 5 karakter
        $tanggal = date('dmy'); // 6 karakter (ddmmyy)
        
        // Ambil kode terakhir dengan prefix dan tanggal yang sama
        $lastAnggota = Anggota::where('kode_anggota', 'LIKE', $prefix . '%' . $tanggal)
            ->orderBy('kode_anggota', 'desc')
            ->first();

        if ($lastAnggota) {
            // Ambil nomor urut dari kode terakhir
            // Format: KMPSB + XXXX + ddmmyy
            $lastNumber = (int) substr($lastAnggota->kode_anggota, strlen($prefix), 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format nomor urut 4 digit (0001 - 9999)
        $nomorUrut = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . $nomorUrut . $tanggal;
        // Hasil: KMPSB0001080726 (15 karakter)
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetInput();
        $this->editMode = false;
        $this->kode_anggota = $this->generateKodeAnggota();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $anggota = Anggota::findOrFail($id);
        $this->anggotaId       = $id;
        $this->kode_anggota    = $anggota->kode_anggota;
        $this->nama_anggota    = $anggota->nama_anggota;
        $this->email_anggota   = $anggota->email_anggota;
        $this->telepon_anggota = $anggota->telepon_anggota;
        $this->alamat_anggota  = $anggota->alamat_anggota;
        $this->tanggal_masuk   = $anggota->tanggal_masuk;
        $this->editMode        = true;
        $this->showModal       = true;
    }

    public function refreshKode()
    {
        if (!$this->editMode) {
            $this->kode_anggota = $this->generateKodeAnggota();
        }
    }

    public function save()
    {
        $this->validate();

        if (!$this->editMode) {
            $exists = Anggota::where('kode_anggota', $this->kode_anggota)->exists();
            if ($exists) {
                $this->kode_anggota = $this->generateKodeAnggota();
                $this->addError('kode_anggota', 'Kode anggota sudah terpakai, dihasilkan kode baru: ' . $this->kode_anggota);
                return;
            }
        }

        $data = [
            'kode_anggota'    => $this->kode_anggota,
            'nama_anggota'    => $this->nama_anggota,
            'email_anggota'   => $this->email_anggota,
            'telepon_anggota' => $this->telepon_anggota,
            'alamat_anggota'  => $this->alamat_anggota,
            'tanggal_masuk'   => $this->tanggal_masuk,
        ];

        if ($this->editMode) {
            Anggota::where('id_anggota', $this->anggotaId)->update($data);
            session()->flash('success', 'Anggota diperbarui.');
        } else {
            Anggota::create($data);
            session()->flash('success', 'Anggota ditambahkan.');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    public function delete($id)
    {
        Anggota::destroy($id);
        session()->flash('success', 'Anggota dihapus.');
    }

    private function resetInput()
    {
        $this->reset(['anggotaId', 'nama_anggota', 'email_anggota', 'telepon_anggota', 'alamat_anggota', 'tanggal_masuk']);
    }

    public function getTotalSimpanan($anggotaId): float
    {
        return Simpan::where('id_anggota', $anggotaId)->sum('jumlah');
    }

    public function render()
    {
        $anggotas = Anggota::query()
            ->when($this->search, function ($q) {
                $q->where('nama_anggota', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_anggota', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama_anggota')
            ->paginate(10);

        return view('components.anggota-index', compact('anggotas'));
    }
}