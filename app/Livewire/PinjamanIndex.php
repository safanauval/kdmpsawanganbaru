<?php

namespace App\Livewire;

use App\Models\Pinjaman;
use App\Models\Anggota;
use App\Models\Angsuran;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class PinjamanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $showBayarModal = false;
    
    // Filter
    public $filterStatus = '';
    public $dateFrom = '';
    public $dateTo = '';
    
    // Form properti
    public $pinjamanId, $id_anggota, $jumlah_pinjaman, $bunga_persen = 2, $tenor = 12;
    public $angsuran_per_bulan, $total_pengembalian, $sisa_pinjaman;
    public $tanggal_pinjaman, $tanggal_jatuh_tempo, $keterangan;
    public $payment_method = 'tunai';

    // Bayar angsuran
    public $selectedPinjaman;
    public $jumlah_bayar;

    // Midtrans QRIS
    public $showQrisModal = false;
    public $qrisUrl = '';
    public $qrisOrderId = '';
    public $pendingAngsuran = null;

    protected function rules()
    {
        return [
            'id_anggota'        => 'required|exists:anggota,id_anggota',
            'jumlah_pinjaman'   => 'required|numeric|min:1',
            'bunga_persen'      => 'required|numeric|min:0|max:100',
            'tenor'             => 'required|integer|min:1|max:60',
            'tanggal_pinjaman'  => 'required|date',
            'keterangan'        => 'nullable|string',
        ];
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filterStatus', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function resetFilter()
    {
        $this->reset(['search', 'filterStatus', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updated($property)
    {
        if (in_array($property, ['jumlah_pinjaman', 'bunga_persen', 'tenor'])) {
            $this->hitungPinjaman();
        }
    }

    /**
     * Hitung total pengembalian dan angsuran per bulan
     */
    public function hitungPinjaman()
    {
        if ($this->jumlah_pinjaman > 0 && $this->tenor > 0) {
            $pokok = (float) $this->jumlah_pinjaman;
            $bungaPersen = (float) $this->bunga_persen;
            $tenor = (int) $this->tenor;

            $totalBunga = $pokok * ($bungaPersen / 100) * $tenor;
            $this->total_pengembalian = $pokok + $totalBunga;
            $this->angsuran_per_bulan = round($this->total_pengembalian / $tenor);
            $this->sisa_pinjaman = $this->total_pengembalian;
            $this->tanggal_jatuh_tempo = now()->addMonths($tenor)->format('Y-m-d');
        }
    }

    public function openCreate()
    {
        $this->resetInput();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $this->pinjamanId        = $id;
        $this->id_anggota        = $pinjaman->id_anggota;
        $this->jumlah_pinjaman   = $pinjaman->jumlah_pinjaman;
        $this->bunga_persen      = $pinjaman->bunga_persen;
        $this->tenor             = $pinjaman->tenor;
        $this->angsuran_per_bulan = $pinjaman->angsuran_per_bulan;
        $this->total_pengembalian = $pinjaman->total_pengembalian;
        $this->sisa_pinjaman      = $pinjaman->sisa_pinjaman;
        $this->tanggal_pinjaman   = $pinjaman->tanggal_pinjaman->format('Y-m-d');
        $this->tanggal_jatuh_tempo = $pinjaman->tanggal_jatuh_tempo?->format('Y-m-d');
        $this->keterangan         = $pinjaman->keterangan;
        $this->editMode           = true;
        $this->showModal          = true;
    }

    private function generateKodePinjaman(): string
    {
        $prefix = 'PNJ';
        $tanggal = date('dmy');
        $lastPinjaman = Pinjaman::where('kode_pinjaman', 'LIKE', $prefix . '%' . $tanggal)
            ->orderBy('kode_pinjaman', 'desc')
            ->first();

        if ($lastPinjaman) {
            $lastNumber = (int) substr($lastPinjaman->kode_pinjaman, 3, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT) . $tanggal;
    }

     public function save()
    {
        $this->validate();
        $this->hitungPinjaman();

        $data = [
            'kode_pinjaman'       => $this->editMode ? Pinjaman::find($this->pinjamanId)->kode_pinjaman : $this->generateKodePinjaman(),
            'id_anggota'          => $this->id_anggota,
            'jumlah_pinjaman'     => $this->jumlah_pinjaman,
            'bunga_persen'        => $this->bunga_persen,
            'tenor'               => $this->tenor,
            'angsuran_per_bulan'  => $this->angsuran_per_bulan,
            'total_pengembalian'  => $this->total_pengembalian,
            'sisa_pinjaman'       => $this->sisa_pinjaman,
            'tanggal_pinjaman'    => $this->tanggal_pinjaman,
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo,
            'keterangan'          => $this->keterangan,
        ];

        if ($this->editMode) {
            Pinjaman::where('id', $this->pinjamanId)->update($data);
            $this->dispatch('notify', 'Pinjaman berhasil diperbarui.', 'success');
        } else {
            Pinjaman::create($data);
            $this->dispatch('notify', 'Pinjaman berhasil ditambahkan.', 'success');
        }

        $this->showModal = false;
        $this->resetInput();
    }

    /**
     * Bayar angsuran
     */
    public function bayarAngsuran()
    {
        if (!$this->selectedPinjaman) {
            $this->dispatch('notify', 'Data pinjaman tidak ditemukan.', 'error');
            $this->showBayarModal = false;
            return;
        }

        $this->validate([
            'jumlah_bayar' => 'required|numeric|min:1|max:' . $this->selectedPinjaman->sisa_pinjaman,
        ]);

        // ========== PEMBAYARAN TUNAI ==========
        if ($this->payment_method === 'tunai') {
            $this->prosesBayarTunai();
            return;
        }

        // ========== PEMBAYARAN NON-TUNAI (MIDTRANS API) ==========
        $this->dispatch('notify', 'Memproses pembayaran...', 'info');
        $this->prosesBayarNonTunai();
    }

    /**
     * Proses bayar tunai
     */
    private function prosesBayarTunai()
    {
        $pinjaman = $this->selectedPinjaman;
        $angsuranKe = $pinjaman->angsurans()->count() + 1;
        $sisaBaru = $pinjaman->sisa_pinjaman - $this->jumlah_bayar;

        Angsuran::create([
            'pinjaman_id'    => $pinjaman->id,
            'angsuran_ke'    => $angsuranKe,
            'jumlah_bayar'   => $this->jumlah_bayar,
            'sisa_pinjaman'  => $sisaBaru,
            'payment_method' => 'tunai',
            'tanggal_bayar'  => now()->format('Y-m-d'),
        ]);

        $pinjaman->update([
            'sisa_pinjaman' => $sisaBaru,
            'status'        => $sisaBaru <= 0 ? 'lunas' : 'aktif',
        ]);

        $this->showBayarModal = false;
        $this->selectedPinjaman = null;
        $this->dispatch('notify', 'Angsuran ke-' . $angsuranKe . ' berhasil dibayarkan (Tunai).', 'success');
    }

    /**
     * Proses bayar non-tunai dengan Midtrans API langsung
     */
    private function prosesBayarNonTunai()
    {
        try {
            $pinjaman = $this->selectedPinjaman;
            $orderId = 'ANGS-' . strtoupper(uniqid()) . '-' . time();
            $grossAmount = (int) $this->jumlah_bayar;

            $client = new Client(['verify' => false]);

            $baseUrl = config('services.midtrans.is_production') 
                ? 'https://api.midtrans.com' 
                : 'https://api.sandbox.midtrans.com';

            $serverKey = config('services.midtrans.server_key');
            $authString = base64_encode($serverKey . ':');

            $anggota = $pinjaman->anggota;

            $requestBody = [
                'payment_type'        => 'qris',
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $anggota ? $anggota->nama_anggota : 'Anggota',
                    'phone'      => $anggota ? $anggota->telepon_anggota : '',
                ],
                'item_details' => [
                    [
                        'id'       => 'ANGSURAN-' . $pinjaman->kode_pinjaman,
                        'price'    => $grossAmount,
                        'quantity' => 1,
                        'name'     => 'Angsuran ' . $pinjaman->kode_pinjaman . ' - ' . ($anggota ? $anggota->nama_anggota : ''),
                    ],
                ],
            ];

            $response = $client->request('POST', $baseUrl . '/v2/charge', [
                'json'    => $requestBody,
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $authString,
                ],
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('Midtrans Angsuran Response', $result);

            if (isset($result['status_code']) && in_array($result['status_code'], ['200', '201'])) {
                $qrUrl = null;
                if (isset($result['actions'])) {
                    foreach ($result['actions'] as $action) {
                        if ($action['name'] === 'generate-qr-code') {
                            $qrUrl = $action['url'];
                            break;
                        }
                    }
                }

                if ($qrUrl) {
                    $angsuranKe = $pinjaman->angsurans()->count() + 1;

                    $this->pendingAngsuran = [
                        'pinjaman_id'    => $pinjaman->id,
                        'angsuran_ke'    => $angsuranKe,
                        'jumlah_bayar'   => $this->jumlah_bayar,
                        'sisa_pinjaman'  => $pinjaman->sisa_pinjaman - $this->jumlah_bayar,
                        'payment_method' => 'non-tunai',
                        'tanggal_bayar'  => now()->format('Y-m-d'),
                    ];

                    $this->showBayarModal = false;
                    $this->qrisUrl = $qrUrl;
                    $this->qrisOrderId = $orderId;
                    $this->showQrisModal = true;

                    $this->dispatch('notify', 'QR Code berhasil dibuat. Silakan scan untuk membayar.', 'info');
                } else {
                    $this->dispatch('notify', 'Gagal mendapatkan QR code.', 'error');
                }
            } else {
                $errorMessage = $result['status_message'] ?? 'Unknown error';
                Log::error('Midtrans Error Angsuran: ' . $errorMessage, $result);
                $this->dispatch('notify', 'Gagal: ' . $errorMessage, 'error');
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $errorBody = json_decode($response->getBody(), true);
            $errorMessage = $errorBody['status_message'] ?? $e->getMessage();
            Log::error('Midtrans Client Error Angsuran: ' . $errorMessage);
            $this->dispatch('notify', 'Error: ' . $errorMessage, 'error');
        } catch (\Exception $e) {
            Log::error('Midtrans Exception Angsuran: ' . $e->getMessage());
            $this->dispatch('notify', 'Gagal terhubung ke server pembayaran.', 'error');
        }
    }

    /**
     * Cek status pembayaran QRIS
     */
    public function checkPaymentStatus()
    {
        if (!$this->qrisOrderId) return;

        $this->dispatch('notify', 'Memeriksa status pembayaran...', 'info');

        try {
            $client = new Client(['verify' => false]);
            $baseUrl = config('services.midtrans.is_production') 
                ? 'https://api.midtrans.com' 
                : 'https://api.sandbox.midtrans.com';
            $serverKey = config('services.midtrans.server_key');
            $authString = base64_encode($serverKey . ':');

            $response = $client->request('GET', $baseUrl . '/v2/' . $this->qrisOrderId . '/status', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . $authString,
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            if (isset($result['transaction_status'])) {
                $status = $result['transaction_status'];
                
                if ($status === 'settlement' || $status === 'capture') {
                    $this->handlePaymentSuccess($result);
                } elseif ($status === 'pending') {
                    $this->dispatch('notify', 'Pembayaran masih menunggu konfirmasi.', 'warning');
                } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure'])) {
                    $this->dispatch('notify', 'Pembayaran gagal atau dibatalkan.', 'error');
                    $this->showQrisModal = false;
                    $this->pendingAngsuran = null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Check Payment Status Error: ' . $e->getMessage());
            $this->dispatch('notify', 'Gagal memeriksa status pembayaran.', 'error');
        }
    }

    /**
     * Callback sukses pembayaran
     */
    public function handlePaymentSuccess($result)
    {
        if ($this->pendingAngsuran) {
            Angsuran::create($this->pendingAngsuran);

            $pinjaman = Pinjaman::find($this->pendingAngsuran['pinjaman_id']);
            if ($pinjaman) {
                $pinjaman->update([
                    'sisa_pinjaman' => $this->pendingAngsuran['sisa_pinjaman'],
                    'status'        => $this->pendingAngsuran['sisa_pinjaman'] <= 0 ? 'lunas' : 'aktif',
                ]);
            }

            $angsuranKe = $this->pendingAngsuran['angsuran_ke'];
            $this->pendingAngsuran = null;
            $this->showQrisModal = false;
            $this->selectedPinjaman = null;
            $this->dispatch('notify', 'Pembayaran berhasil! Angsuran ke-' . $angsuranKe . ' telah dicatat.', 'success');
        }
    }

    public function openBayar($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'angsurans'])->find($id);
        
        if (!$pinjaman) {
            $this->dispatch('notify', 'Data pinjaman tidak ditemukan.', 'error');
            return;
        }

        if ($pinjaman->status === 'lunas') {
            $this->dispatch('notify', 'Pinjaman sudah lunas.', 'warning');
            return;
        }

        $this->selectedPinjaman = $pinjaman;
        $this->jumlah_bayar = $pinjaman->angsuran_per_bulan;
        $this->payment_method = 'tunai';
        $this->showBayarModal = true;
    }
    /**
     * Tutup modal QRIS
     */
    public function closeQrisModal()
    {
        $this->showQrisModal = false;
        $this->qrisUrl = '';
        $this->qrisOrderId = '';
        $this->pendingAngsuran = null;
        $this->dispatch('notify', 'Pembayaran QRIS dibatalkan.', 'warning');
    }

    /**
     * Hapus pinjaman
     */
    public function delete($id)
    {
        Pinjaman::delete($id);
        $this->dispatch('notify', 'Pinjaman berhasil dihapus.', 'success');
    }

    private function resetInput()
    {
        $this->reset([
            'pinjamanId', 'id_anggota', 'jumlah_pinjaman', 'bunga_persen', 'tenor',
            'angsuran_per_bulan', 'total_pengembalian', 'sisa_pinjaman',
            'tanggal_pinjaman', 'tanggal_jatuh_tempo', 'keterangan', 'editMode',
        ]);
        $this->resetValidation();
        $this->bunga_persen = 2;
        $this->tenor = 12;
    }

    public function render()
    {
        $pinjamans = Pinjaman::with(['anggota', 'angsurans'])
            ->when($this->search, function ($q) {
                $q->whereHas('anggota', function ($q) {
                    $q->where('nama_anggota', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_anggota', 'like', '%' . $this->search . '%');
                })->orWhere('kode_pinjaman', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->dateFrom, fn($q) => $q->whereDate('tanggal_pinjaman', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('tanggal_pinjaman', '<=', $this->dateTo))
            ->orderBy('tanggal_pinjaman', 'desc')
            ->paginate(10);

        $anggotas = Anggota::orderBy('nama_anggota')->get();

        return view('components.pinjaman-index', [
            'pinjamans' => $pinjamans,
            'anggotas'  => $anggotas,
        ]);
    }
}