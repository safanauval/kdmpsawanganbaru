<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMKOPDES - Sistem Informasi Manajemen Koperasi Desa</title>
    <link rel="icon" href="/favicon.ico">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Saira:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "Instrument Sans", sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
            animation: fadeSlideIn 0.8s ease-out;
        }

        /* Keyframes untuk transisi masuk halaman */
        @keyframes fadeSlideIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Reveal on scroll - default state */
        .reveal {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        .container {
            margin: 0 auto;
            padding: 0 34px;
        }

        /* Header Sticky */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #000000;
            background-color: #f8fafc57;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: box-shadow 0.3s;
        }

        .top-nav.sticky-active {
            box-shadow: 0 0px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 55px;
            height: 50px;
            background-color: #d30000;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img {
            width: 45px;
        }

        .logo-text {
            font-weight: 700;
            font-size: 40px;
            font-family: "Saira", sans-serif;
            font-style: italic;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #890000;
            text-decoration: none
        }

        .btn-login {
            background-color: transparent;
            border: 1px solid #890000;
            color: #d30000;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none
        }

        .btn-login:hover {
            background-color: #d30000;
            color: white;
        }

        .btn-register {
            background-color: #d30000;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s;
            text-decoration: none
        }

        .btn-register:hover {
            background-color: #890000;
            color: white;
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            padding: 30px 0;
            gap: 40px;
        }

        .hero-content {
            flex: 1;
        }

        .hero-badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #b45309;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .hero-content h1 {
            font-size: 2.8rem;
            font-weight: 700;
            line-height: 1.2;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .hero-highlight {
            color: #d30000;
        }

        .hero-desc {
            color: #475569;
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 500px;
        }

        .hero-image {
            flex: 1;
            text-align: right;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin: 40px 0 60px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d30000;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Section Title */
        .section-title {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .section-title p {
            color: #64748b;
            margin-top: 8px;
        }

        /* Layanan Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 60px;
        }

        .service-card {
            background: white;
            padding: 28px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background-color: #e0e7ff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #d30000;
            font-size: 28px;
        }

        .service-card h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .service-card p {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* News / Info */
        .info-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin: 40px 0 60px;
        }

        .news-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
        }

        .news-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #d30000;
        }

        .news-list {
            list-style: none;
        }

        .news-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            gap: 12px;
        }

        .news-date {
            color: #94a3b8;
            font-size: 0.8rem;
            min-width: 70px;
        }

        .news-list a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
        }

        .news-list a:hover {
            color: #d30000;
        }

        .kontak-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .kontak-logo {
            background: #d30000;
            color: white;
            padding: 8px 10px;
            border-radius: 8px;
            items-align: center;
            text-align: center;
            justify-cintent: center;
            min-width: 50px;
            font-weight: 700;
        }

        /* Footer */
        .footer {
            background: #0f172a;
            color: #cbd5e1;
            padding: 40px 0;
            margin-top: 60px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-copy {
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            margin-left: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .nav-links .hide-mobile {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header / Navigation (Sticky) -->
        <header class="top-nav" id="mainHeader">
            <div class="logo-area">
                <div class="logo-icon">
                    <img src="/img/logo/kopdes-logo-putih.png" class="logo-img"></img>
                </div>
                <span class="logo-text italic tracking-tight">Simkopdes</span>
            </div>
            <div class="nav-links">
                <a href="#hero" class="hide-mobile">Beranda</a>
                <a href="#layanan" class="hide-mobile">Layanan</a>
                <a href="#informasi" class="hide-mobile">Informasi</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-login">Dashboard</a>
                @else
                    <flux:button href="{{ route('login') }}" variant="outline" class="btn-login" wire:navigate>
                        Masuk
                    </flux:button>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register" style="color: white;">Daftar</a>
                    @endif
                @endauth
            </div>
        </header>

        <!-- Hero Section -->
        <section id="hero" class="hero reveal">
            <div class="hero-content">
                <span class="hero-badge">#KoperasiDigital</span>
                <h1>Sistem Informasi <br><span class="hero-highlight">Manajemen Koperasi</span> Desa</h1>
                <p class="hero-desc">Mendorong transparansi dan efisiensi pengelolaan koperasi melalui platform digital
                    terintegrasi.</p>
                <div style="display: flex; gap: 16px;">
                    <a href="https://simkopdes.go.id/daftar/anggota" class="btn-register"
                        style="padding: 12px 28px;">Bergabung
                        Sekarang</a>
                    <a href="https://simkopdes.go.id/#about" class="btn-login" style="padding: 12px 28px;">Pelajari</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.pexels.com/photos/3184418/pexels-photo-3184418.jpeg?auto=compress&cs=tinysrgb&w=600"
                    alt="Ilustrasi Koperasi" style="width:100%; max-width:500px;">
            </div>
        </section>

        <!-- Statistik -->
        <div class="stats-grid reveal">
            <div class="stat-item">
                <div class="stat-number">12.450+</div>
                <div class="stat-label">Anggota Aktif</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">156</div>
                <div class="stat-label">Koperasi Desa</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">Rp 82M</div>
                <div class="stat-label">Total Aset</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Layanan Online</div>
            </div>
        </div>

        <!-- Layanan Utama -->
        <div id="layanan" class="section-title reveal">
            <h2>Layanan Kami</h2>
            <p>Fitur unggulan untuk mendukung operasional koperasi modern</p>
        </div>
        <div class="services-grid">
            <div class="service-card reveal">
                <div class="service-icon">👥</div>
                <h3>Manajemen Anggota</h3>
                <p>Database anggota, simpanan, dan riwayat transaksi terintegrasi.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">💰</div>
                <h3>Simpan Pinjam</h3>
                <p>Proses pengajuan pinjaman dan pencatatan simpanan secara digital.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">🛒</div>
                <h3>Gerai Sembako</h3>
                <p>Transaksi penjualan cepat dengan manajemen stok otomatis.</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon">📊</div>
                <h3>Laporan & Analitik</h3>
                <p>Pantau kinerja keuangan dan SHU secara real-time.</p>
            </div>
        </div>

        <!-- Berita & Informasi -->
        <div id="informasi" class="info-grid">
            <div class="news-card reveal">
                <div class="news-title">
                    <span>📰</span> Berita
                </div>
                <ul class="news-list">
                    <li>
                        <span class="news-date">21 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/6">Presiden Prabowo Resmikan Kopdes/ Kel
                            Merah Putih, Momentum Kembalikan Sistem Ekonomi Ke Pasal 33 UUD 45</a>
                    </li>
                    <li>
                        <span class="news-date">21 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/7">Prabowo Launching 80.000 Kopdes Merah
                            Putih di Klaten Hari Ini</a>
                    </li>
                    <li>
                        <span class="news-date">21 Juli 2025</span>
                        <a href="https://simkopdes.go.id/pers/berita/detail/8">Hari Ini Presiden RI Resmikan 80.000
                            Kopdes Merah Putih</a>
                    </li>
                </ul>
            </div>
            <div class="news-card reveal">
                <div class="news-title">
                    <span>📞</span> Kontak
                </div>

                <!-- Email -->
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.envelope variant="solid" />
                    </div>
                    <div>
                        <strong>Email</strong><br>
                        <small>
                            <a href="mailto:korwil@merahputih.kop.id">
                                korwil@merahputih.kop.id
                            </a>
                        </small>
                    </div>
                </div>

                <!-- Telepon -->
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.phone variant="solid" />
                    </div>
                    <div>
                        <strong>Telepon</strong><br>
                        <small>
                            <a href="tel:1500587">1500587</a>
                        </small>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.map-pin variant="solid" />
                    </div>
                    <div>
                        <strong>Alamat</strong><br>
                        <small>
                            Graha Mandiri Lt.3, Jl. Imam Bonjol No.61,<br>
                            Menteng, Jakarta Pusat 10310
                        </small>
                    </div>
                </div>

                <!-- Pengaduan -->
                <div class="kontak-item">
                    <div class="kontak-logo">
                        <flux:icon.flag variant="solid" />
                    </div>
                    <div>
                        <strong>Pengaduan</strong><br>
                        <small>
                            <a href="https://lapor.go.id" target="_blank">
                                Simwas Koperasi | Lapor.go.id
                            </a>
                        </small>
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
            <div class="footer-links">
                <a href="#">Tentang</a>
                <a href="#">Kontak</a>
                <a href="#">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>

    <!-- Script: Sticky shadow + Reveal on scroll -->
    <script>
        // Shadow pada header saat scroll
        window.addEventListener('scroll', function () {
            const header = document.getElementById('mainHeader');
            if (window.scrollY > 10) {
                header.classList.add('sticky-active');
            } else {
                header.classList.remove('sticky-active');
            }
        });

        // Intersection Observer untuk efek reveal (selalu aktif)
        const revealElements = document.querySelectorAll('.reveal');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                } else {
                    entry.target.classList.remove('revealed');
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(el => observer.observe(el));

        // Fallback
        if (!window.IntersectionObserver) {
            revealElements.forEach(el => el.classList.add('revealed'));
        }
    </script>
</body>

</html>