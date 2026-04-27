<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl sm:p-1">
        <!-- Header Selamat Datang -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between sm:p-8 p-2">
            <div>
                <flux:heading size="xl">Selamat datang, {{ auth()->user()->name }}!</flux:heading>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    {{ now()->isoFormat('dddd, D MMMM Y') }} • {{ now()->format('H:i') }}
                </p>
            </div>
            <flux:button icon="arrow-path" variant="ghost" wire:navigate href="{{ route('dashboard') }}">
                Refresh
            </flux:button>
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 overflow-y-auto">
            {{-- Overview Cards --}}
            <div
                class="flex flex-wrap min-w-[200px] justify-between border-neutral-200 dark:border-neutral-700 p-2 gap-2">
                {{-- Card Total Produk --}}
                <flux:card class="flex-1 rounded-xl border border-b p-4">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.building-storefront variant="solid" class="h-4 w-4 text-purple-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Produk</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalProducts }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.archive-box variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">produk</span>
                        </div>
                    </div>
                </flux:card>

                {{-- Card Total Kategori --}}
                <flux:card color="blue"
                    class="flex-1 rounded-xl border bg-black-300 hover:bg-zinc-400 dark:hover:bg-zinc-700 p-4">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.tag variant="solid" class="h-4 w-4 text-red-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Kategori</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalCategories }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.inbox-stack variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Kategori</span>
                        </div>
                    </div>
                </flux:card>

                {{-- Card Total User --}}
                <flux:card color="blue" class="flex-1 rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700 p-4">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.users variant="solid" class="h-4 w-4 text-blue-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Pengguna</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalUsers }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.user-plus variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Pengguna aktif</span>
                        </div>
                    </div>
                </flux:card>

                {{-- Card Stok Barang --}}
                <flux:card color="blue" class="flex-1 rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700 p-4">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.clipboard-document-list variant="solid" class="h-4 w-4 text-amber-500" />
                            <span class="text-base font-medium text-black dark:text-white">Stok Barang</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $lowStock }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.sparkles variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Stok Menipis</span>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Produk Terbaru & User Terbaru (2 kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 p-2 gap-2">
                <!-- Produk Terbaru -->
                <flux:card>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Produk Terbaru</h3>
                        <flux:button href="{{ route('stok-barang.create') }}" size="sm" icon="plus" wire:navigate>
                            Tambah
                        </flux:button>
                    </div>
                    <div class="space-y-2">
                        @forelse($latestProducts as $product)
                            <div
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    @if($product->gambar_url)
                                        <img src="{{ $product->gambar_url }}" alt="{{ $product->nama_barang }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <flux:icon.photo class="w-5 h-5 text-gray-400" />
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $product->nama_barang }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Rp {{ number_format($product->harga_jual, 0, ',', '.') }} • Stok
                                        {{ $product->stok }}
                                    </p>
                                </div>
                                <div>
                                    <flux:badge size="sm" color="{{ $product->stok > 0 ? 'green' : 'red' }}">
                                        {{ $product->stok > 0 ? 'Tersedia' : 'Habis' }}
                                    </flux:badge>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-6">Belum ada produk</p>
                        @endforelse
                    </div>
                    <div class="mt-4 pt-2 border border-gray-200 dark:border-neutral-700">
                        <flux:button href="{{ route('stok-barang.index') }}" variant="primary" color="blue" size="sm"
                            wire:navigate class="w-full justify-between">
                            Lihat Semua Produk
                            <flux:icon.chevron-right class="w-4 h-4" />
                        </flux:button>
                    </div>
                </flux:card>

                <!-- User Terbaru (Admin only) -->
                @can('admin')
                    <flux:card>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">👥 User Terbaru</h3>
                            <flux:button href="{{ route('users.index') }}" variant="ghost" size="sm" icon="user-plus"
                                wire:navigate>
                                Kelola
                            </flux:button>
                        </div>
                        <div class="space-y-2">
                            @forelse($latestUsers as $user)
                                <div
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                                    <flux:avatar :name="$user->name" size="sm" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                    </div>
                                    <div>
                                        <flux:badge size="sm" color="{{ $user->role === 'admin' ? 'blue' : 'zinc' }}">
                                            {{ ucfirst($user->role) }}
                                        </flux:badge>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-6">Tidak ada user baru</p>
                            @endforelse
                        </div>
                        <div class="mt-4 pt-2 border-t border-gray-200 dark:border-neutral-700">
                            <flux:button href="{{ route('users.index') }}" variant="ghost" size="sm" icon="arrow-right"
                                wire:navigate class="w-full justify-between">
                                Lihat Semua User
                                <flux:icon.chevron-right class="w-4 h-4" />
                            </flux:button>
                        </div>
                    </flux:card>
                @endcan
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    const salesData = @json($salesChartData);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: salesData.labels,
                            datasets: [{
                                label: 'Pendapatan (Rp)',
                                data: salesData.data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    },
                                    grid: { color: 'rgba(156, 163, 175, 0.2)' }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                });
            </script>
        @endpush
</x-layouts::app>