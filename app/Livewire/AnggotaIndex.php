<?php

namespace App\Livewire;

use App\Models\Anggota;
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
            'kode_anggota' => 'required|unique:anggota,kode_anggota' . ($this->editMode ? ',' . $this->anggotaId : ''),
            'nama_anggota' => 'required|string|max:80',
            'email_anggota' => 'nullable|email|unique:anggota,email_anggota' . ($this->editMode ? ',' . $this->anggotaId : ''),
            'telepon_anggota' => 'nullable|string|max:20',
            'alamat_anggota' => 'nullable|string',
            'tanggal_masuk' => 'required|date',
        ];
    }

    /**
     * Generate kode anggota otomatis
     * Format: AGT + nomor urut 4 digit (misal AGT0001)
     */
    protected function generateKodeAnggota(): string
    {
        $prefix = 'KMPSB';
        $lastAnggota = Anggota::where('kode_anggota', 'LIKE', $prefix . '%')
            ->orderBy('kode_anggota', 'desc')
            ->first();

        if ($lastAnggota) {
            // Ambil nomor urut dari kode terakhir, misal AGT0123 -> 123
            $lastNumber = (int) substr($lastAnggota->kode_anggota, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format 5 digit dengan leading zero
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT) . date('dmy'); // Tambahkan tahun untuk memastikan keunikan setiap tahun
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetInput();
        $this->editMode = false;
        // Generate kode anggota otomatis
        $this->kode_anggota = $this->generateKodeAnggota();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $anggota = Anggota::findOrFail($id);
        $this->anggotaId = $id;
        $this->kode_anggota = $anggota->kode_anggota;
        $this->nama_anggota = $anggota->nama_anggota;
        $this->email_anggota = $anggota->email_anggota;
        $this->telepon_anggota = $anggota->telepon_anggota;
        $this->alamat_anggota = $anggota->alamat_anggota;
        $this->tanggal_masuk = $anggota->tanggal_masuk;
        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Reload/generate ulang kode anggota (misalnya jika user ingin refresh)
     * Bisa dipanggil dari tombol "Refresh Kode" di modal
     */
    public function refreshKode()
    {
        if (!$this->editMode) {
            $this->kode_anggota = $this->generateKodeAnggota();
        }
    }

    public function save()
    {
        $this->validate();

        // Pastikan kode anggota unik (jika ada perubahan manual)
        if (!$this->editMode) {
            $exists = Anggota::where('kode_anggota', $this->kode_anggota)->exists();
            if ($exists) {
                $this->kode_anggota = $this->generateKodeAnggota(); // regenerate jika bentrok
                $this->addError('kode_anggota', 'Kode anggota sudah terpakai, dihasilkan kode baru: ' . $this->kode_anggota);
                return;
            }
        }

        $data = [
            'kode_anggota' => $this->kode_anggota,
            'nama_anggota' => $this->nama_anggota,
            'email_anggota' => $this->email_anggota,
            'telepon_anggota' => $this->telepon_anggota,
            'alamat_anggota' => $this->alamat_anggota,
            'tanggal_masuk' => $this->tanggal_masuk,
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
        // Jangan reset kode_anggota di sini karena akan digenerate ulang saat openCreate
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