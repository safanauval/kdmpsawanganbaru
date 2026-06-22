<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatTransaksi extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Modal detail
    public $showDetailModal = false;
    public $selectedOrder = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getTodayRevenueProperty()
    {
        return Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getMostStatusProperty()
    {
        return Order::selectRaw('payment_status, COUNT(*) as total')
            ->groupBy('payment_status')
            ->orderByDesc('total')
            ->first()?->payment_status ?? '-';
    }

    public function openDetail($orderId)
    {
        $this->selectedOrder = Order::with('user')->find($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function printReport()
    {
        session()->put('riwayat_filter', [
            'dateFrom' => $this->dateFrom ?? now()->startOfMonth()->toDateString(),
            'dateTo' => $this->dateTo ?? now()->endOfMonth()->toDateString(),
            'filterStatus' => $this->filterStatus ?? '',
            'search' => $this->search ?? '',
        ]);

        return redirect()->route('riwayat-transaksi.cetak');
    }

    public function render()
    {
        $orders = Order::query()
            ->with('user')
            ->when($this->search, function ($q) {
                $q->where('order_id', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_anggota', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('payment_status', $this->filterStatus);
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('components.riwayat-transaksi', [
            'orders' => $orders,
            'todayRevenue' => $this->todayRevenue,
            'mostStatus' => $this->mostStatus,
        ]);
    }
}