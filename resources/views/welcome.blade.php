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
            <h1>Sistem Informasi <br><span class="hero-highlight">Manajemen Koperasi</span> Desa</h1>
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

    <div class="container service">
        <!-- Layanan Utama -->
        <div id="layanan" class="section-title reveal">
            <h2>Layanan Kami</h2>
            <p>Fitur unggulan untuk mendukung operasional koperasi modern</p>
        </div>
        <div class="services-grid">
            <div class="service-card reveal">
                <div class="service-icon">
                    <flux:icon.user-group color="white" />
                </div>
                <h3>Manajemen Anggota</h3>
                <p>Database anggota, simpanan, dan riwayat transaksi terintegrasi.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">
                    <flux:icon.wallet color="white" />
                </div>
                <h3>Simpan Pinjam</h3>
                <p>Proses pengajuan pinjaman dan pencatatan simpanan secara digital.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">
                    <flux:icon.shopping-cart color="white" />
                </div>
                <h3>Gerai Sembako</h3>
                <p>Transaksi penjualan cepat dengan manajemen stok otomatis.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">
                    <flux:icon.chart-bar color="white" />
                </div>
                <h3>Laporan & Analitik</h3>
                <p>Pantau kinerja keuangan dan SHU secara real-time.</p>
            </div>
        </div>
    </div>

    <div class="container news">
        <!-- Berita & Informasi -->
        <div id="informasi" class="info-grid">
            <div class="news-card reveal">
                <div class="news-title">
                    <flux:icon.newspaper style="width: 24px; height: 24px;" />
                    Berita Terkini
                </div>
                <ul class="news-list">
                    <li>
                        <span class="news-date">21 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/6" style="text-align: left;">Presiden
                            Prabowo Resmikan Kopdes Merah
                            Putih, Momentum Kembalikan Sistem Ekonomi Ke Pasal 33 UUD 45</a>
                    </li>
                    <li>
                        <span class="news-date">21 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/7" style="text-align: left;">Prabowo
                            Launching 80.000 Kopdes Merah
                            Putih di Klaten Hari Ini</a>
                    </li>
                    <li>
                        <span class="news-date">20 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/8" style="text-align: left;">Hari Ini
                            Presiden RI Resmikan 80.000
                            Kopdes Merah Putih</a>
                    </li>
                </ul>
            </div>
            <div class="news-card reveal">
                <div class="news-title">
                    <flux:icon.identification style="width: 24px; height: 24px;" />
                    Kontak & Informasi
                </div>
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.envelope color="white" />
                    </div>
                    <div style="text-align: left;">
                        <strong>Email</strong><br>
                        <small><a href="mailto:korwil@merahputih.kop.id"
                                style="text-decoration: none; color: #475569;">korwil@merahputih.kop.id</a></small>
                    </div>
                </div>
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.phone color="white" />
                    </div>
                    <div style="text-align: left;">
                        <strong>Telepon</strong><br>
                        <small><a href="tel:1500587" style="text-decoration: none; color: #475569;">1500587</a></small>
                    </div>
                </div>
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.map-pin color="white" />
                    </div>
                    <div style="text-align: left;">
                        <strong>Alamat</strong><br>
                        <small>Graha Mandiri Lt.3, Jl. Imam Bonjol No.61,<br>Menteng, Jakarta Pusat 10310</small>
                    </div>
                </div>
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.flag color="white" />
                    </div>
                    <div style="text-align: left;">
                        <strong>Pengaduan</strong><br>
                        <small><a href="https://lapor.go.id" target="_blank"
                                style="text-decoration: none; color: #475569;">Simwas Koperasi | Lapor.go.id</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-copy">
                &copy; 2026 SIMKOPDES. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- Flux UI Scripts (wajib) -->
    @fluxScripts
    <script src="{{ asset('js/welcome.js') }}" crossorigin="anonymous"></script>
</body>

</html>