<?php

namespace App\Livewire;

use App\Models\Simpan;
use App\Models\Anggota;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SimpanIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    
    // Filter
    public $filterjenis_simpanan = '';
    public $dateFrom = '';
    public $dateTo = '';
    
    public $simpanId, $id_anggota, $jenis_simpanan, $jumlah, $payment_method, $tanggal;

    // Properti untuk Midtrans QRIS
    public $showQrisModal = false;
    public $qrisUrl = '';
    public $qrisOrderId = '';
    public $pendingSimpanan = null;

    // Konstanta nominal simpanan pokok
    const POKOK_NOMINAL = 100000;

    protected function rules()
    {
        return [
            'id_anggota'      => 'required|exists:anggota,id_anggota',
            'jenis_simpanan'  => 'required|in:pokok,wajib',
            'jumlah'          => 'required|numeric|min:1',
            'payment_method'  => 'required|in:tunai,non-tunai',
            'tanggal'         => 'required|date',
        ];
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filterjenis_simpanan', 'dateFrom', 'dateTo'])) {
            $this->resetPage();
        }
    }

    public function resetFilter()
    {
        $this->reset(['search', 'filterjenis_simpanan', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function updatedIdAnggota($value)
    {
        if ($value && !$this->editMode) {
            $this->reset(['jenis_simpanan', 'jumlah']);
            
            if ($this->hasPokok($value)) {
                $this->jenis_simpanan = 'wajib';
            }
        } elseif (!$value) {
            $this->reset(['jenis_simpanan', 'jumlah']);
        }
    }

    public function updatedJenisSimpanan($value)
    {
        if ($value === 'pokok' && !$this->editMode) {
            $this->jumlah = self::POKOK_NOMINAL;
        } elseif ($value === 'wajib') {
            $this->jumlah = null;
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
        $simpan = Simpan::findOrFail($id);
        $this->simpanId        = $id;
        $this->id_anggota      = $simpan->id_anggota;
        $this->jenis_simpanan  = $simpan->jenis_simpanan;
        $this->jumlah          = $simpan->jumlah;
        $this->payment_method  = $simpan->payment_method;
        $this->tanggal         = $simpan->tanggal;
        $this->editMode        = true;
        $this->showModal       = true;
    }

    /**
     * Method yang dipanggil saat tombol Bayar diklik
     */
    public function processPayment()
    {
        $this->validate();

        // Validasi: Simpanan pokok hanya boleh sekali per anggota
        if ($this->jenis_simpanan === 'pokok' && !$this->editMode) {
            $existingPokok = Simpan::where('id_anggota', $this->id_anggota)
                ->where('jenis_simpanan', 'pokok')
                ->exists();

            if ($existingPokok) {
                $this->dispatch('notify', 'Simpanan pokok untuk anggota ini sudah pernah dibayarkan!', 'error');
                return;
            }
        }

        // ========== EDIT MODE (UPDATE LANGSUNG) ==========
        if ($this->editMode) {
            $simpan = Simpan::find($this->simpanId);
            $simpan->update([
                'id_anggota'      => $this->id_anggota,
                'jenis_simpanan'  => $this->jenis_simpanan,
                'jumlah'          => $this->jumlah,
                'payment_method'  => $this->payment_method,
                'tanggal'         => $this->tanggal,
            ]);
            $this->dispatch('notify', 'Simpanan berhasil diperbarui.', 'success');
            $this->showModal = false;
            $this->resetInput();
            return;
        }

        // ========== PEMBAYARAN TUNAI ==========
        if ($this->payment_method === 'tunai') {
            Simpan::create([
                'id_anggota'      => $this->id_anggota,
                'jenis_simpanan'  => $this->jenis_simpanan,
                'jumlah'          => $this->jumlah,
                'payment_method'  => 'tunai',
                'tanggal'         => $this->tanggal,
            ]);
            
            $this->dispatch('notify', 'Simpanan berhasil ditambahkan (Tunai).', 'success');
            $this->showModal = false;
            $this->resetInput();
            return;
        }

        // ========== PEMBAYARAN NON-TUNAI (MIDTRANS API LANGSUNG) ==========
        $this->dispatch('notify', 'Memproses pembayaran...', 'info');
        $this->processNonTunaiPayment();
    }

    /**
     * Proses pembayaran non-tunai dengan Midtrans API langsung (Guzzle)
     */
    public function processNonTunaiPayment()
    {
        try {
            $orderId = 'SIMPAN-' . strtoupper(uniqid()) . '-' . time();
            $grossAmount = (int) $this->jumlah;

            $client = new Client(['verify' => false]);

            $baseUrl = config('services.midtrans.is_production') 
                ? 'https://api.midtrans.com' 
                : 'https://api.sandbox.midtrans.com';

            $serverKey = config('services.midtrans.server_key');
            $authString = base64_encode($serverKey . ':');

            $anggota = Anggota::find($this->id_anggota);

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
                        'id'       => 'SIMPANAN-' . $this->jenis_simpanan,
                        'price'    => $grossAmount,
                        'quantity' => 1,
                        'name'     => 'Simpanan ' . ucfirst($this->jenis_simpanan) . ' - ' . ($anggota ? $anggota->nama_anggota : ''),
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
            Log::info('Midtrans QRIS Response', $result);

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
                    $this->pendingSimpanan = [
                        'id_anggota'      => $this->id_anggota,
                        'jenis_simpanan'  => $this->jenis_simpanan,
                        'jumlah'          => $this->jumlah,
                        'payment_method'  => 'non-tunai',
                        'tanggal'         => $this->tanggal,
                        'order_id'        => $orderId,
                    ];

                    $this->showModal = false;
                    $this->qrisUrl = $qrUrl;
                    $this->qrisOrderId = $orderId;
                    $this->showQrisModal = true;

                    $this->dispatch('notify', 'QR Code berhasil dibuat. Silakan scan untuk membayar.', 'info');
                } else {
                    $this->dispatch('notify', 'Gagal mendapatkan QR code dari Midtrans.', 'error');
                }
            } else {
                $errorMessage = $result['status_message'] ?? 'Unknown error';
                Log::error('Midtrans Error: ' . $errorMessage, $result);
                $this->dispatch('notify', 'Gagal membuat pembayaran: ' . $errorMessage, 'error');
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $errorBody = json_decode($response->getBody(), true);
            $errorMessage = $errorBody['status_message'] ?? $e->getMessage();
            Log::error('Midtrans Client Error: ' . $errorMessage);
            $this->dispatch('notify', 'Error pembayaran: ' . $errorMessage, 'error');
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            $this->dispatch('notify', 'Gagal terhubung ke Midtrans. Silakan coba lagi.', 'error');
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
                    $this->pendingSimpanan = null;
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
        if ($this->pendingSimpanan) {
            Simpan::create([
                'id_anggota'      => $this->pendingSimpanan['id_anggota'],
                'jenis_simpanan'  => $this->pendingSimpanan['jenis_simpanan'],
                'jumlah'          => $this->pendingSimpanan['jumlah'],
                'payment_method'  => 'non-tunai',
                'tanggal'         => $this->pendingSimpanan['tanggal'],
            ]);

            $this->pendingSimpanan = null;
            $this->resetInput();
            $this->showQrisModal = false;
            $this->dispatch('notify', 'Pembayaran berhasil! Simpanan telah dicatat.', 'success');
        }
    }

    /**
     * Tutup modal QRIS
     */
    public function closeQrisModal()
    {
        $this->showQrisModal = false;
        $this->qrisUrl = '';
        $this->qrisOrderId = '';
        $this->pendingSimpanan = null;
        $this->dispatch('notify', 'Pembayaran QRIS dibatalkan.', 'warning');
    }

    public function getTotalSimpanan($anggotaId)
    {
        $lastRecord = Simpan::where('id_anggota', $anggotaId)
            ->orderBy('id', 'desc')
            ->first();

        return $lastRecord ? $lastRecord->total_simpanan : 0;
    }

    public function hasPokok($anggotaId): bool
    {
        return Simpan::where('id_anggota', $anggotaId)
            ->where('jenis_simpanan', 'pokok')
            ->exists();
    }

    public function delete($id)
    {
        $simpan = Simpan::findOrFail($id);
        $simpan->delete();
        $this->dispatch('notify', 'Simpanan berhasil dihapus.', 'success');
    }

    private function resetInput()
    {
        $this->reset([
            'simpanId',
            'id_anggota',
            'jenis_simpanan',
            'jumlah',
            'payment_method',
            'tanggal',
            'editMode',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        $simpanans = Simpan::with('anggota')
            ->when($this->search, function ($q) {
                $q->whereHas('anggota', function ($q) {
                    $q->where('nama_anggota', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_anggota', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterjenis_simpanan, function ($q) {
                $q->where('jenis_simpanan', $this->filterjenis_simpanan);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('tanggal', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('tanggal', '<=', $this->dateTo);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        $anggotas = Anggota::orderBy('nama_anggota')->get();

        $totalKeseluruhan = Simpan::sum('jumlah');
        $totalFiltered = Simpan::query()
            ->when($this->filterjenis_simpanan, fn($q) => $q->where('jenis_simpanan', $this->filterjenis_simpanan))
            ->when($this->dateFrom, fn($q) => $q->whereDate('tanggal', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('tanggal', '<=', $this->dateTo))
            ->sum('jumlah');

        return view('components.simpan-index', [
            'simpanans'         => $simpanans,
            'anggotas'          => $anggotas,
            'totalKeseluruhan'  => $totalKeseluruhan,
            'totalFiltered'     => $totalFiltered,
        ]);
    }
}