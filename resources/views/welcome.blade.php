<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMKOPDES - Sistem Informasi Manajemen Koperasi Desa</title>
    <link rel="icon" href="/favicon.ico">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <!-- Flux UI Appearance (wajib untuk komponen Flux) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    @fluxAppearance
</head>

<body>
    <!-- Header -->
    <header class="top-nav" id="mainHeader">
        <div class="logo-area">
            <div class="logo-icon">
                <img src="/img/logo/kopdes-logo-putih.png" class="logo-img" alt="Logo">
            </div>
            <span class="logo-text">Simkopdes</span>
        </div>
        <div class="nav-center">
            <a href="#hero" class="nav-link">Beranda</a>
            <a href="#layanan" class="nav-link">Layanan</a>
            <a href="#informasi" class="nav-link">Informasi</a>
        </div>
        <div class="nav-links">
            <a href="{{ __('login') }}" class="btn-login nav-link">Masuk</a>
        </div>
    </header>

    <!-- Hero Section - Full Screen Background -->
    <section id="hero" class="hero-fullscreen reveal">
        <div class="hero-overlay"></div>
        <div class="hero-content-center">
            <span class="hero-badge">#KoperasiDigital</span>
            <h1>
                {{ \App\Models\Setting::getValue('hero_title_line1', 'Manajemen') }} <br>
                <span class="hero-highlight">{{ \App\Models\Setting::getValue('company_name', 'Koperasi Desa Merah Putih') }}</span>
            </h1>
            <p class="hero-desc">Meningkatkan efisiensi dan transparansi operasional koperasi desa dengan teknologi
                modern</p>
            <!-- Statistik -->
            <div class="stats-grid reveal">
                <div class="stat-item">
                    <div class="stat-number">2.440.216+</div>
                    <div class="stat-label">Anggota Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">79.781</div>
                    <div class="stat-label">Koperasi Desa</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">Rp 24,3M</div>
                    <div class="stat-label">Total Aset</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Layanan Online</div>
                </div>
            </div>
            <div class="hero-buttons">
                <a href="https://simkopdes.go.id/daftar/anggota" class="btn-hero-primary">Bergabung Sekarang</a>
                <a href="https://simkopdes.go.id/#about" class="btn-hero-secondary">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </section>

    <!-- ========== LAYANAN =========== -->
     <section class="container service">
        <!-- Layanan Utama -->
        <div class="text-center mb-16" id="layanan">
            <h2 class="text-3xl sm:text-4xl font-bold text-black mb-4">Layanan Kami</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Fitur unggulan untuk mendukung operasional koperasi modern</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Card 1: Manajemen Anggota --}}
            <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                    <flux:icon.user-group color="white" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Manajemen Anggota</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Database anggota, simpanan, dan riwayat transaksi terintegrasi.</p>
            </div>

            {{-- Card 2: Simpanan --}}
            <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                    <flux:icon.wallet color="white" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Simpanan</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Simpanan sebagai modal maupun tabungan anggota.</p>
            </div>

            {{-- Card 3: Gerai Sembako --}}
            <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                    <flux:icon.shopping-cart color="white" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Gerai Sembako</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Transaksi penjualan cepat dengan manajemen stok otomatis.</p>
            </div>

            {{-- Card 4: Laporan & Analitik --}}
            <div class="group bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg group-hover:scale-110 transition-transform">
                    <flux:icon.chart-bar color="white" class="w-8 h-8" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Laporan & Analitik</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Pantau kinerja keuangan dan SHU secara real-time.</p>
            </div>
        </div>
     </section>

    <!-- ========== BERITA & KONTAK ========== -->
    <section id="informasi" class="min-h-screen flex items-center justify-center bg-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            
            {{-- Judul Section --}}
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Informasi & Kontak</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Berita terkini dan kontak resmi koperasi</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                
                {{-- ========== BERITA ========== --}}
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <flux:icon.newspaper class="w-6 h-6 text-blue-600" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Berita Terkini</h3>
                    </div>

                    {{-- List Berita --}}
                    <div class="space-y-4">
                        <!-- Berita 1 -->
                        <a href="https://simkopdes.go.id/pers/berita/detail/6" class="group block bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">21 Juli 2025</span>
                            <h4 class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors leading-relaxed">
                                Presiden Prabowo Resmikan Kopdes Merah Putih, Momentum Kembalikan Sistem Ekonomi Ke Pasal 33 UUD 45
                            </h4>
                        </a>

                        <!-- Berita 2 -->
                        <a href="https://simkopdes.go.id/pers/berita/detail/7" class="group block bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">21 Juli 2025</span>
                            <h4 class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors leading-relaxed">
                                Prabowo Launching 80.000 Kopdes Merah Putih di Klaten Hari Ini
                            </h4>
                        </a>

                        <!-- Berita 3 -->
                        <a href="https://simkopdes.go.id/pers/berita/detail/8" class="group block bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">20 Juli 2025</span>
                            <h4 class="mt-2 text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors leading-relaxed">
                                Hari Ini Presiden RI Resmikan 80.000 Kopdes Merah Putih
                            </h4>
                        </a>
                    </div>
                </div>

                {{-- ========== KONTAK ========== --}}
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <flux:icon.identification class="w-6 h-6 text-green-600" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Kontak & Informasi</h3>
                    </div>

                    {{-- List Kontak --}}
                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="w-11 h-11 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <flux:icon.envelope class="w-5 h-5 text-red-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</p>
                                <a href="mailto:{{ \App\Models\Setting::getValue('email', 'kopkelmerahputihsawanganbaru@gmail.com') }}" 
                                class="text-sm text-gray-900 hover:text-blue-600 transition-colors break-all">
                                    {{ \App\Models\Setting::getValue('email', 'kopkelmerahputihsawanganbaru@gmail.com') }}
                                </a>
                            </div>
                        </div>

                        <!-- Telepon -->
                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="w-11 h-11 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <flux:icon.phone class="w-5 h-5 text-blue-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Telepon</p>
                                <a href="tel:{{ \App\Models\Setting::getValue('phone', '081219999922') }}" 
                                class="text-sm text-gray-900 hover:text-blue-600 transition-colors">
                                    {{ \App\Models\Setting::getValue('phone', '0812-1999-9922') }}
                                </a>
                                @if(\App\Models\Setting::getValue('phone2'))
                                    <br>
                                    <a href="tel:{{ \App\Models\Setting::getValue('phone2') }}" 
                                    class="text-sm text-gray-900 hover:text-blue-600 transition-colors">
                                        {{ \App\Models\Setting::getValue('phone2') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="w-11 h-11 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <flux:icon.map-pin class="w-5 h-5 text-green-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alamat</p>
                                <p class="text-sm text-gray-900 leading-relaxed">
                                    {{ \App\Models\Setting::getValue('address', 'Jl. H. Maksum No.4/3, Sawangan Baru, Kec. Sawangan, Kota Depok, Jawa Barat 16511') }}
                                </p>
                            </div>
                        </div>

                        <!-- Pengaduan -->
                        <div class="flex items-start gap-4 bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <div class="w-11 h-11 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <flux:icon.flag class="w-5 h-5 text-orange-500" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pengaduan</p>
                                <a href="https://lapor.go.id" target="_blank" 
                                class="text-sm text-gray-900 hover:text-blue-600 transition-colors">
                                    {{ \App\Models\Setting::getValue('complaint_contact', 'korwil@merahputih.kop.id | 08111-451-587') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-copy">
                &copy; 2026 SIMKOPDES SAWANGAN BARU. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- Flux UI Scripts (wajib) -->
    @fluxScripts
    <script src="{{ asset('js/welcome.js') }}" crossorigin="anonymous"></script>
</body>

</html>