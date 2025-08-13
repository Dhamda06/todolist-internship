<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di List'in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ====================================================================== */
        /* Definisi Variabel CSS & Tema Warna */
        /* ====================================================================== */
        :root {
            --primary-olive: #6B8E23;
            --secondary-olive: #556B2F;
            --bg-light: #f8f9fa;
            --bg-dark: #1f2937;
            --text-dark: #212529;
            --text-light: #f5f5f5;
            --card-light: #ffffff;
            --card-dark: #2e3a47;
            --gradient-main: linear-gradient(135deg, #A7D129 0%, #6B8E23 100%);
            --shadow-light: 0 12px 25px rgba(0,0,0,0.1);
            --shadow-dark: 0 12px 25px rgba(0,0,0,0.3);
            --body-bg-light: #e0f2f1;
            --body-bg-dark: #263238;
        }

        /* ====================================================================== */
        /* Gaya Dasar & Tema */
        /* ====================================================================== */
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg-light);
            color: var(--text-dark);
            transition: all 0.4s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        .dark-mode {
            background-color: var(--body-bg-dark);
            color: var(--text-light);
        }

        /* ====================================================================== */
        /* Latar Belakang Interaktif */
        /* ====================================================================== */
        .background-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.3'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 0v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM30 0L0 30L30 60L60 30L30 0z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .dark-mode .background-animated {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236B8E23' fill-opacity='0.3'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0 0v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM30 0L0 30L30 60L60 30L30 0z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        /* ====================================================================== */
        /* Navigasi Header */
        /* ====================================================================== */
        .navbar {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .dark-mode .navbar {
            background-color: rgba(33, 37, 41, 0.85);
        }

        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dark-mode .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
        }

        .navbar-brand h1 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0;
            color: transparent;
            background-image: var(--gradient-main);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-dark);
            transition: color 0.3s ease;
        }
        
        .dark-mode .nav-link {
            color: var(--text-light);
        }

        .nav-link:hover {
            color: var(--primary-olive);
        }

        .dark-mode .nav-link:hover {
            color: #a7d129;
        }

        /* Toggle Dark Mode */
        .dark-mode-toggle {
            cursor: pointer;
            font-size: 1.5rem;
            transition: color 0.3s ease;
            color: var(--text-dark);
        }

        .dark-mode .dark-mode-toggle {
            color: var(--text-light);
        }
        
        /* ====================================================================== */
        /* Konten Utama & Hero Section (Full-width) */
        /* ====================================================================== */
        .hero-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 10rem 2rem 5rem;
            text-align: center;
            animation: fadeIn 1s ease-out;
            background-color: var(--body-bg-light);
            transition: background-color 0.4s ease-in-out;
        }
        
        .dark-mode .hero-section {
            background-color: var(--body-bg-dark);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1.main-title {
            font-size: 4rem;
            font-weight: 800;
            color: transparent;
            background-image: var(--gradient-main);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            animation: bounceIn 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            75% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        p.lead {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 1.5rem;
            transition: color 0.4s ease-in-out;
        }

        .dark-mode p.lead {
            color: #adb5bd;
        }
        
        /* ====================================================================== */
        /* Tombol Aksi */
        /* ====================================================================== */
        .btn-action {
            font-size: 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-register {
            background-image: var(--gradient-main);
            border: none;
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 4px 10px rgba(107, 142, 35, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
        }
        
        .btn-login {
            background-color: transparent;
            padding: 1rem 2rem;
            border: 2px solid var(--primary-olive);
            color: var(--primary-olive);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-image: var(--gradient-main);
            border: none;
            color: white;
            padding: 1rem 2rem;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(107, 142, 35, 0.4);
        }

        .dark-mode .btn-login {
            border-color: #a7d129;
            color: #a7d129;
        }
        
        .dark-mode .btn-login:hover {
            background-image: var(--gradient-main);
            border: none;
            color: var(--text-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(167, 209, 41, 0.4);
        }
        
        /* ====================================================================== */
        /* Gaya Carousel Kustom */
        /* ====================================================================== */
        .hero-carousel {
            max-width: 750px; /* Lebar lebih besar */
            margin-left: auto;
            margin-right: auto;
        }
        
        .carousel-inner img {
            height: 300px; /* Tinggi konsisten */
            
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 30px; /* Ukuran tombol diperkecil */
            height: 30px; /* Ukuran tombol diperkecil */
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.8);
            color: var(--primary-olive);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.7;
        }
        
        .carousel-control-prev {
            left: -15px; /* Sesuaikan posisi panah kiri (setengah dari lebar) */
        }

        .carousel-control-next {
            right: -15px; /* Sesuaikan posisi panah kanan (setengah dari lebar) */
        }

        /* Hover effect for carousel controls */
        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background-color: var(--primary-olive);
            color: var(--text-light);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            transform: translateY(-50%) scale(1.1);
            opacity: 1;
        }

        /* Perubahan warna panah */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(45%) sepia(35%) saturate(300%) hue-rotate(30deg) brightness(85%) contrast(80%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem; /* Ukuran ikon diperkecil */
        }
        
        .carousel-control-prev:hover .carousel-control-prev-icon,
        .carousel-control-next:hover .carousel-control-next-icon {
            filter: invert(100%) sepia(0%) saturate(0%) hue-rotate(251deg) brightness(103%) contrast(100%);
        }

        /* Dark Mode styles for carousel controls */
        .dark-mode .carousel-control-prev,
        .dark-mode .carousel-control-next {
            background-color: rgba(46, 58, 71, 0.8);
            color: var(--text-light);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .dark-mode .carousel-control-prev:hover,
        .dark-mode .carousel-control-next:hover {
            background-color: #a7d129;
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        /* Filter untuk ikon di dark mode */
        .dark-mode .carousel-control-prev-icon,
        .dark-mode .carousel-control-next-icon {
            filter: invert(80%) sepia(100%) saturate(1000%) hue-rotate(75deg) brightness(120%) contrast(100%);
        }

        .dark-mode .carousel-control-prev:hover .carousel-control-prev-icon,
        .dark-mode .carousel-control-next:hover .carousel-control-next-icon {
            filter: none;
        }


        /* ====================================================================== */
        /* Bagian Fitur (Dengan Scroll Horizontal dan Tombol Panah) */
        /* ====================================================================== */
        .features-section {
            padding: 4rem 2rem;
            background-color: rgba(255, 255, 255, 0.4);
            transition: background-color 0.4s ease-in-out;
            backdrop-filter: blur(2px);
        }

        .dark-mode .features-section {
            background-color: rgba(46, 58, 71, 0.4);
            backdrop-filter: blur(2px);
        }
        
        .features-container-wrapper {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .features-scroll-container {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 1rem;
            scroll-behavior: smooth;
        }
        
        .features-scroll-container::-webkit-scrollbar {
            display: none;
        }
        
        .feature-card-wrapper {
            flex-shrink: 0;
            width: 100%;
            scroll-snap-align: start;
        }

        @media (min-width: 768px) {
            .feature-card-wrapper {
                width: calc(100% / 2 - 0.75rem);
            }
        }

        @media (min-width: 992px) {
            .feature-card-wrapper {
                width: calc(100% / 3 - 1rem);
            }
        }

        /* Gaya tombol panah */
        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
            color: var(--primary-olive);
        }
        
        .scroll-arrow:hover {
            background-color: rgba(107, 142, 35, 0.9);
            color: white;
            transform: translateY(-50%) scale(1.1);
        }

        .dark-mode .scroll-arrow {
            background-color: rgba(46, 58, 71, 0.9);
            color: #a7d129;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .scroll-arrow:hover {
            background-color: #a7d129;
            color: var(--text-dark);
            transform: translateY(-50%) scale(1.1);
        }

        #scroll-left {
            left: -20px;
        }
        
        #scroll-right {
            right: -20px;
        }
        
        /* Sembunyikan tombol di mobile */
        @media (max-width: 991px) {
            .scroll-arrow {
                display: none;
            }
        }
        
        .feature-card {
            background-color: var(--card-light);
            border-radius: 1.2rem;
            padding: 2.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(107, 142, 35, 0.1);
            height: 100%;
        }
        
        .dark-mode .feature-card {
            background-color: var(--card-dark);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .dark-mode .feature-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }

        .feature-card i {
            color: var(--primary-olive);
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .feature-card h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            transition: color 0.4s ease-in-out;
        }
        
        .dark-mode .feature-card h3 {
            color: var(--text-light);
        }

        .feature-card p {
            font-size: 1rem;
            color: #495057;
            margin-bottom: 0;
            transition: color 0.4s ease-in-out;
        }
        
        .dark-mode .feature-card p {
            color: #adb5bd;
        }
        
        /* ====================================================================== */
        /* Bagian CTA Baru */
        /* ====================================================================== */
        .cta-section {
            background-image: var(--gradient-main);
            color: white;
            padding: 5rem 2rem;
        }

        /* ====================================================================== */
        /* Footer */
        /* ====================================================================== */
        .footer {
            background-color: #f1f8f1;
            color: var(--text-dark);
            padding: 3rem 0;
            border-top: 1px solid rgba(0,0,0,0.1);
            transition: all 0.4s ease-in-out;
        }
        
        /* PERBAIKAN DARK MODE UNTUK FOOTER */
        .dark-mode .footer {
            background-color: var(--card-dark);
            color: var(--text-light);
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .footer .nav-link, .footer p, .footer a {
            color: var(--text-dark);
            transition: color 0.3s ease;
            text-decoration: none;
        }
        
        /* PERBAIKAN DARK MODE UNTUK LINK FOOTER */
        .dark-mode .footer .nav-link, 
        .dark-mode .footer p, 
        .dark-mode .footer a {
            color: var(--text-light);
        }

        .footer .nav-link:hover, .footer a:hover {
            color: var(--primary-olive);
        }
        
        .dark-mode .footer .nav-link:hover, .dark-mode .footer a:hover {
            color: #a7d129;
        }

        .social-icons a {
            font-size: 1.5rem;
            margin-right: 1.5rem;
            color: var(--primary-olive);
            transition: transform 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <div class="background-animated"></div>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <h1 class="m-0">List'in</h1>
                </a>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center gap-3">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Fitur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Tentang</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="hero-section d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
                    <h1 class="main-title">List'in</h1>
                    <h2 class="h3 fw-bold">Bekerja Lebih Teratur, Capai Lebih Banyak</h2>
                    <p class="lead text-muted">Kelola tugas harian dengan aplikasi manajemen yang dirancang untuk meningkatkan produktivitas dan menjaga fokus pada prioritas utama.</p>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3 mt-4">
                        <a href="<?= site_url('todo/register') ?>" class="btn btn-register btn-action"><i class="bi bi-person-add me-2"></i>Daftar Sekarang</a>
                        <a href="<?= site_url('todo/login') ?>" class="btn btn-login btn-action"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</a>
                    </div>
                </div>
                <div class="col-lg-6 d-flex justify-content-center">
                    <div id="hero-carousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner rounded-4 shadow-lg">
                            <div class="carousel-item active">
                                <img src="<?= base_url('asset/images/dftr.jpg') ?>" class="d-block w-100" alt="Ilustrasi pertama">
                            </div>
                            <div class="carousel-item">
                                <img src="<?= base_url('asset/images/tgs.jpg') ?>" class="d-block w-100" alt="Ilustrasi kedua">
                            </div>
                            <div class="carousel-item">
                                <img src="<?= base_url('asset/images/hm.jpg') ?>" class="d-block w-100" alt="Ilustrasi ketiga">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <section class="features-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2>Fitur Unggulan</h2>
            </div>
            <div class="features-container-wrapper">
                <button id="scroll-left" class="scroll-arrow"><i class="bi bi-chevron-left"></i></button>
                <div class="features-scroll-container" id="features-scroll-container">
                    <div class="feature-card-wrapper">
                        <div class="feature-card text-center">
                            <i class="bi bi-list-check"></i>
                            <h3 class="mt-3">Organisasi Mudah</h3>
                            <p>Atur daftar tugas dengan prioritas dan deadline yang jelas.</p>
                        </div>
                    </div>
                    <div class="feature-card-wrapper">
                        <div class="feature-card text-center">
                            <i class="bi bi-graph-up"></i>
                            <h3 class="mt-3">Analisis Statistik</h3>
                            <p>Dapatkan wawasan berharga tentang kebiasaan dan kemajuan Anda.</p>
                        </div>
                    </div>
                    <div class="feature-card-wrapper">
                        <div class="feature-card text-center">
                            <i class="bi bi-lightning-charge"></i>
                            <h3 class="mt-3">Tingkatkan Fokus</h3>
                            <p>Selesaikan lebih banyak hal dengan antarmuka yang bersih dan bebas gangguan.</p>
                        </div>
                    </div>
                    <div class="feature-card-wrapper">
                        <div class="feature-card text-center">
                            <i class="bi bi-bell"></i>
                            <h3 class="mt-3">Pengingat Cerdas </h3>
                            <p>Dapatkan notifikasi dan pengingat yang disesuaikan agar tidak ada tugas yang terlewat.</p>
                        </div>
                    </div>
                    <div class="feature-card-wrapper">
                        <div class="feature-card text-center">
                            <i class="bi bi-person-workspace"></i>
                            <h3 class="mt-3">Kolaborasi Tim</h3>
                            <p>Bagikan daftar tugas dan proyek dengan rekan tim untuk bekerja sama lebih efektif.</p>
                        </div>
                    </div>
                </div>
                <button id="scroll-right" class="scroll-arrow"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Tentang List'in</h5>
                    <p>Aplikasi manajemen tugas yang membantu mengorganisir pekerjaan, melacak kemajuan, dan meningkatkan produktivitas setiap hari.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Navigasi</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="#" class="nav-link p-0">Beranda</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0">Fitur</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0">Tentang</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Hubungi Kami</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a href="mailto:info@listin.com" class="nav-link p-0"><i class="bi bi-envelope me-2"></i>listin.com</a></li>
                        <li class="nav-item"><a href="#" class="nav-link p-0"><i class="bi bi-geo-alt me-2"></i>Bandung</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5>Ikuti Kami</h5>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col text-center">
                    <p class="mb-0">&copy; 2025 List'in. Semua hak cipta dilindungi.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const toggleMobile = document.getElementById('dark-mode-toggle');
            const toggleDesktop = document.getElementById('dark-mode-toggle-desktop');
            const navbar = document.querySelector('.navbar');

            const applyDarkMode = () => {
                const isDarkMode = localStorage.getItem('dark-mode') === 'true';
                if (isDarkMode) {
                    body.classList.add('dark-mode');
                    if (toggleMobile) {
                        toggleMobile.classList.remove('bi-moon-fill');
                        toggleMobile.classList.add('bi-sun-fill');
                    }
                    if (toggleDesktop) {
                        toggleDesktop.classList.remove('bi-moon-fill');
                        toggleDesktop.classList.add('bi-sun-fill');
                    }
                } else {
                    body.classList.remove('dark-mode');
                    if (toggleMobile) {
                        toggleMobile.classList.remove('bi-sun-fill');
                        toggleMobile.classList.add('bi-moon-fill');
                    }
                    if (toggleDesktop) {
                        toggleDesktop.classList.remove('bi-sun-fill');
                        toggleDesktop.classList.add('bi-moon-fill');
                    }
                }
            };
            
            applyDarkMode();

            if (toggleMobile) {
                toggleMobile.addEventListener('click', () => {
                    const isNowDarkMode = !body.classList.contains('dark-mode');
                    localStorage.setItem('dark-mode', isNowDarkMode);
                    applyDarkMode();
                });
            }
            if (toggleDesktop) {
                toggleDesktop.addEventListener('click', () => {
                    const isNowDarkMode = !body.classList.contains('dark-mode');
                    localStorage.setItem('dark-mode', isNowDarkMode);
                    applyDarkMode();
                });
            }

            const handleScroll = () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            };
            window.addEventListener('scroll', handleScroll);
            
            const scrollContainer = document.getElementById('features-scroll-container');
            const scrollLeftBtn = document.getElementById('scroll-left');
            const scrollRightBtn = document.getElementById('scroll-right');
            const scrollStep = 300;

            const updateArrows = () => {
                if (window.innerWidth >= 992 && scrollContainer.scrollWidth > scrollContainer.clientWidth) {
                    scrollLeftBtn.style.display = 'flex';
                    scrollRightBtn.style.display = 'flex';

                    const isAtLeft = scrollContainer.scrollLeft === 0;
                    const isAtRight = Math.round(scrollContainer.scrollLeft + scrollContainer.clientWidth) >= scrollContainer.scrollWidth;

                    scrollLeftBtn.style.opacity = isAtLeft ? '0.4' : '1';
                    scrollRightBtn.style.opacity = isAtRight ? '0.4' : '1';
                    scrollLeftBtn.disabled = isAtLeft;
                    scrollRightBtn.disabled = isAtRight;
                } else {
                    scrollLeftBtn.style.display = 'none';
                    scrollRightBtn.style.display = 'none';
                }
            };

            scrollLeftBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: -scrollStep, behavior: 'smooth' });
            });

            scrollRightBtn.addEventListener('click', () => {
                scrollContainer.scrollBy({ left: scrollStep, behavior: 'smooth' });
            });

            scrollContainer.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);
            
            updateArrows();
        });
    </script>
</body>
</html>