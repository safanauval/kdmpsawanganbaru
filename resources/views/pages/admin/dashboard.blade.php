<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col rounded-xl">
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
            <!-- Overview Card -->
            <div
                class="flex flex-wrap min-w-[200px] justify-between border-neutral-200 dark:border-neutral-700 p-2 gap-4">
                <!-- Card Transaksi -->
                <flux:card class="flex-1 rounded-xl border border-b p-2">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.banknotes variant="solid" class="h-4 w-4 text-purple-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Transaksi</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalTransaction }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.archive-box variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Produk</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Card Total Kategori -->
                <flux:card color="blue"
                    class="flex-1 rounded-xl border bg-black-300 hover:bg-zinc-400 dark:hover:bg-zinc-700 p-2">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.building-storefront variant="solid" class="h-4 w-4 text-red-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Kategori</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalCategories }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.inbox-stack variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Kategori</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Card Total Member -->
                <flux:card color="blue" class="flex-1 rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700 p-2">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.users variant="solid" class="h-4 w-4 text-blue-600" />
                            <span class="text-base font-medium text-black dark:text-white">Total Anggota</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">{{ $totalAnggota }}</flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.user-plus variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Anggota aktif</span>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Stok Produk & Statistik per minggu -->
            <div class="flex border-neutral-200 dark:border-neutral-700 p-2 gap-4">
                <!-- Statistik per minggu -->
                <flux:card class="space-y-4 h-full dark:text-white" style="width: 66%; height: 330px;" align="center">
                    {{-- Header card --}}
                    <div class="flex-1 rounded-xl p-1">
                        <div class="m-10 bg-white dark:bg-white-800 dark:text-white rounded shadow"
                            style="text-color:white; height: 250px; align-items: center;">
                            {!! $chart->container() !!}
                        </div>
                    </div>
                    <script src="{{ $chart->cdn() }}"></script>
                    {{ $chart->script() }}
                </flux:card>

                <!-- Stok Produk -->
                <flux:card class="space-y-4" style="width: 34%;" align="end">
                    {{-- Header card --}}
                    <div class="flex items-center justify-between">
                        <flux:heading class="dark:text-white" size="lg" variant="strong">Status Stok
                            Barang
                        </flux:heading>
                        <flux:badge color="blue" variant="solid" rounded>Total
                            {{ $inStock + $lowStock + $outOfStock }} item
                        </flux:badge>
                    </div>
                    <div class="space-y-3">
                        {{-- Stok Tersedia --}}
                        <flux:card class="flex items-center justify-between p-3 rounded-lg border"
                            style="background:#e6ffe6;">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                    <flux:icon.check class="h-5 w-5 text-white" />
                                </span>
                                <div class="text-left">
                                    <flux:text class="text-sm font-medium text-green-800">Stok Tersedia</flux:text>
                                    <flux:text class="text-xs text-green-600 dark:text-green-400">Siap dijual
                                    </flux:text>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-black">{{ $inStock }}</span>
                        </flux:card>

                        {{-- Stok Menipis --}}
                        <flux:card class="flex items-center justify-between p-3 rounded-lg border"
                            style="background: #ffffe6;">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center">
                                    <flux:icon.exclamation-triangle class="h-5 w-5 text-white" />
                                </span>
                                <div class="text-left">
                                    <flux:text class="text-sm font-medium text-yellow-800">Stok Menipis</flux:text>
                                    <flux:text class="text-xs text-yellow-600 dark:text-yellow-400">Perlu restock
                                    </flux:text>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-black">{{ $lowStock }}</span>
                        </flux:card>

                        {{-- Stok Habis --}}
                        <flux:card class="flex items-center justify-between p-3 rounded-lg border"
                            style="background: #ffe6e6;">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-red-500 flex items-center justify-center">
                                    <flux:icon.x-mark class="h-5 w-5 text-white" />
                                </span>
                                <div class="text-left">
                                    <flux:text class="text-sm font-medium text-red-800">Stok Habis</flux:text>
                                    <flux:text class="text-xs text-red-600 dark:text-red-400">Segera restock</flux:text>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $outOfStock }}</span>
                        </flux:card>
                    </div>
                </flux:card>
            </div>


            <!-- Pengeluaran dan Pemasukkan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 p-2 gap-4">
                <!-- Card Pemasukkan -->
                <flux:card class="rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700 ">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.currency-dollar variant="solid" class="h-4 w-4 text-green-600" />
                            <span class="text-base font-medium text-black dark:text-white">Pemasukan</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.arrow-trending-up variant="micro" class="text-green-600 dark:text-green-500" />
                            <span class="text-sm text-green-600 dark:text-green-500">Total Pendapatan</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Card Pengeluaran -->
                <flux:card class="rounded-xl border hover:bg-zinc-400 dark:hover:bg-zinc-700">
                    <div>
                        <flux:text class="flex items-center gap-2 text-black">
                            <flux:icon.arrow-down-circle variant="solid" class="h-4 w-4 text-red-600" />
                            <span class="text-base font-medium text-black dark:text-white">Pengeluaran</span>
                        </flux:text>
                        <flux:heading size="xl" class="mb-1 p-4">Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                        </flux:heading>
                        <div class="flex items-center gap-2">
                            <flux:icon.arrow-trending-down variant="micro" class="text-red-600 dark:text-red-500" />
                            <span class="text-sm text-red-600 dark:text-red-500">Total Biaya</span>
                        </div>
                    </div>
                </flux:card>
            </div>

            <!-- Produk Terbaru & User Terbaru (2 kolom) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 p-2 gap-4">
                <!-- Produk Terbaru -->
                <flux:card>
                    <div class="flex items-center justify-between p-4">
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
                <flux:card>
                    <div class="flex items-center justify-between p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Terbaru</h3>
                        <flux:button href="{{ route('users.index') }}" size="sm" icon="user-plus" wire:navigate>
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
                                    <flux:badge size="sm" color="{{ $user->role === 'admin' ? 'blue' : 'yellow' }}">
                                        {{ ucfirst($user->role) }}
                                    </flux:badge>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-6">Tidak ada user baru</p>
                        @endforelse
                    </div>
                    <div class="mt-4 pt-2 border-t border-gray-200 dark:border-neutral-700">
                        <flux:button href="{{ route('users.index') }}" variant="primary" color="blue" size="sm"
                            icon="arrow-right" wire:navigate class="w-full justify-between">
                            Lihat Semua User
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts::app>