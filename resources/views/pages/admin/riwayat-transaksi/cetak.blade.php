<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Riwayat Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-white dark:bg-zinc-800 p-6">
    @php
        // Ambil filter dari session yang disimpan oleh Livewire
        $filter = session('riwayat_filter', [
            'dateFrom' => now()->startOfMonth()->toDateString(),
            'dateTo' => now()->endOfMonth()->toDateString(),
            'filterStatus' => '',
            'search' => '',
        ]);

        $orders = \App\Models\Order::query()
            ->with('user')
            ->when($filter['search'] ?? '', function ($q) use ($filter) {
                $q->where('order_id', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('customer_name', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('nama_anggota', 'like', '%' . $filter['search'] . '%')
                    ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', '%' . $filter['search'] . '%'));
            })
            ->when($filter['filterStatus'] ?? '', fn($q) => $q->where('payment_status', $filter['filterStatus']))
            ->when($filter['dateFrom'] ?? '', fn($q) => $q->whereDate('created_at', '>=', $filter['dateFrom']))
            ->when($filter['dateTo'] ?? '', fn($q) => $q->whereDate('created_at', '<=', $filter['dateTo']))
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        // Informasi toko
        $companyName = \App\Models\Setting::getValue('company_name', config('app.name', 'Koperasi'));
        $address = \App\Models\Setting::getValue('address', 'Alamat belum diatur');
        $phone = \App\Models\Setting::getValue('phone', '-');
    @endphp

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $companyName }}</h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $address }}</p>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Telp: {{ $phone }}</p>
            <h3 class="text-lg font-semibold mt-4 text-zinc-900 dark:text-white">Riwayat Transaksi</h3>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                Periode: {{ \Carbon\Carbon::parse($filter['dateFrom'])->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($filter['dateTo'])->format('d M Y') }}
            </p>
            @if($filter['filterStatus'])
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Status: {{ ucfirst($filter['filterStatus']) }}</p>
            @endif
            @if($filter['search'])
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Pencarian: "{{ $filter['search'] }}"</p>
            @endif
        </div>

        <!-- Ringkasan -->
        <div class="flex justify-between mb-4 text-sm text-zinc-700 dark:text-zinc-300">
            <div>Total Transaksi: <strong>{{ $totalOrders }}</strong></div>
            <div>Total Pendapatan: <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></div>
        </div>

        <!-- Tabel ala Flux UI -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Order ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Metode</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm font-mono font-medium text-zinc-900 dark:text-white">
                                {{ $order->order_id }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $order->customer_name ?: '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($order->payment_status === 'paid')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Lunas</span>
                                @elseif($order->payment_status === 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Gagal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-700 dark:text-zinc-300">
                                {{ ucfirst($order->payment_method) }}
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-zinc-900 dark:text-white">
                                Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">Tidak ada data
                                penjualan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tombol Cetak & Kembali -->
        <div class="mt-6 text-center no-print">
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <flux:icon name="printer" class="w-5 h-5" />
                Cetak Halaman
            </button>
            <button onclick="window.location.href='{{ route('riwayat-transaksi.index') }}'"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors ml-2">
                <flux:icon name="x-mark" class="w-5 h-5" />
                Tutup
            </button>
        </div>
    </div>
</body>

</html>