<?php
// index.php (Core Engine & Landing Page)
session_start();
require_once 'config/koneksi.php';

// 1. CEK APAKAH DIAKSES MENGGUNAKAN LINK UNDANGAN (?u=...)
if (isset($_GET['u'])) {
    $url_undangan = $conn->real_escape_string($_GET['u']);
    $query = "SELECT * FROM detail_undangan WHERE url_undangan = '$url_undangan'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data_undangan = $result->fetch_assoc();
        $undangan_id = $data_undangan['id'];
        $tema_aktif = $data_undangan['tema_aktif'];

        $tanggal_format = date('d F Y', strtotime($data_undangan['tanggal_acara']));
        $tanggal_countdown = date('M d, Y H:i:s', strtotime($data_undangan['tanggal_acara']));
        $ucapan_query = $conn->query("SELECT * FROM rsvp WHERE undangan_id = '$undangan_id' ORDER BY created_at DESC");

        // AMBIL DATA FOTO GALERI
        $galeri_query = $conn->query("SELECT * FROM galeri WHERE undangan_id = '$undangan_id' ORDER BY id DESC");

        // LOGIKA PENANGKAP NAMA TAMU
        $nama_tamu_undangan = "Tamu Undangan"; // Nama default jika link dibuka tanpa nama
        if (isset($_GET['to'])) {
            // Tangkap nama dari URL, bersihkan dari karakter berbahaya, dan decode URL (mengubah %20 jadi spasi)
            $nama_tamu_undangan = htmlspecialchars(urldecode($_GET['to']));
        }

        $theme_path = "templates/" . $tema_aktif . "/index.php";

        if (file_exists($theme_path)) {
            include $theme_path; 
        } else {
            echo "<h1>Error: Tema '" . $tema_aktif . "' sedang dalam perbaikan.</h1>";
        }
    } else {
        echo "<h1>Maaf, Undangan tidak ditemukan.</h1>";
    }
    exit; // Hentikan script disini agar landing page di bawah tidak ikut ter-load
}

// 2. JIKA TIDAK ADA LINK UNDANGAN, TAMPILKAN LANDING PAGE PREMIUM
$is_logged_in = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyInvitation - Platform Undangan Digital Premium</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-cream: #FDFBF7;
            --gold-primary: #C5A880;
            --gold-dark: #9B825E;
            --text-dark: #2C2A29;
        }
        body { font-family: 'Montserrat', sans-serif; background-color: var(--bg-cream); color: var(--text-dark); overflow-x: hidden; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        
        /* Navbar Glassmorphism */
        .navbar { background: rgba(253, 251, 247, 0.85) !important; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: all 0.3s ease; }
        .navbar-brand { font-family: 'Cormorant Garamond', serif; color: var(--gold-primary) !important; font-size: 1.8rem; }
        
        /* Buttons */
        .btn-gold { background-color: var(--gold-primary); color: white; border-radius: 30px; padding: 10px 25px; border: none; font-weight: 500; transition: all 0.3s ease; }
        .btn-gold:hover { background-color: var(--gold-dark); color: white; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(197, 168, 128, 0.3); }
        .btn-outline-gold { border: 1px solid var(--gold-primary); color: var(--gold-primary); border-radius: 30px; padding: 10px 25px; transition: 0.3s ease; background: transparent; }
        .btn-outline-gold:hover { background: var(--gold-primary); color: white; }

        /* Hero Section */
        .hero-section { min-height: 100vh; display: flex; align-items: center; position: relative; padding-top: 80px; }
        .hero-title { font-size: 3.5rem; line-height: 1.2; color: var(--text-dark); }
        .hero-image-wrapper { position: relative; z-index: 1; }
        .hero-image-wrapper::before { content: ''; position: absolute; top: -20px; right: -20px; width: 100%; height: 100%; border: 2px solid var(--gold-primary); border-radius: 20px; z-index: -1; }
        .hero-img { border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); width: 100%; }

        /* Features */
        .feature-icon { font-size: 2.5rem; color: var(--gold-primary); margin-bottom: 15px; }
        .feature-card { background: white; border-radius: 15px; padding: 30px; transition: transform 0.3s ease; border: 1px solid rgba(0,0,0,0.03); }
        .feature-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }

        /* Themes */
        .theme-card { border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .theme-card img { height: 250px; object-fit: cover; transition: transform 0.5s ease; }
        .theme-card:hover img { transform: scale(1.05); }
        
        /* CTA Section */
        .cta-section { background: linear-gradient(rgba(44, 42, 41, 0.8), rgba(44, 42, 41, 0.8)), url('https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop') center/cover fixed; color: white; padding: 100px 0; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">MyInvitation</a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link text-dark" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link text-dark" href="#tema">Tema Desain</a></li>
                    <li class="nav-item"><a class="nav-link text-dark" href="#cara-kerja">Cara Kerja</a></li>
                </ul>
                <div class="d-flex gap-2 mt-3 mt-lg-0">
                    <?php if($is_logged_in): ?>
                        <a href="client/index.php" class="btn btn-gold px-4">Dashboard Saya</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="btn btn-outline-gold px-4">Masuk</a>
                        <a href="auth/register.php" class="btn btn-gold px-4">Buat Undangan</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <span class="badge bg-gold bg-opacity-10 text-gold rounded-pill px-3 py-2 mb-3 border border-warning border-opacity-25" style="color: var(--gold-dark);">Platform Undangan Digital Modern</span>
                    <h1 class="font-serif hero-title fw-bold mb-4">Bagikan Momen Bahagia Anda dengan Elegan.</h1>
                    <p class="text-muted fs-5 mb-5 pe-lg-5">Buat website undangan pernikahan eksklusif Anda dalam hitungan menit. Desain premium, tanpa ribet, dan tinggalkan kesan mendalam bagi tamu undangan Anda.</p>
                    <div class="d-flex gap-3">
                        <a href="<?= $is_logged_in ? 'client/buat_undangan.php' : 'auth/register.php' ?>" class="btn btn-gold btn-lg px-4 fs-6">Mulai Sekarang - Gratis</a>
                        <a href="#tema" class="btn btn-outline-gold btn-lg px-4 fs-6 d-none d-sm-block">Lihat Demo</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="hero-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=1000&auto=format&fit=crop" alt="Wedding Interface" class="hero-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-5 my-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <p class="text-uppercase tracking-widest text-muted" style="letter-spacing: 2px; font-size: 0.8rem;">Mengapa Memilih Kami</p>
                <h2 class="font-serif display-5 fw-bold">Fitur Eksklusif MyInvitation</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card h-100 text-center">
                        <i class="bi bi-palette feature-icon"></i>
                        <h4 class="font-serif fw-bold">Desain Premium</h4>
                        <p class="text-muted">Pilihan template mewah dan elegan yang dapat disesuaikan dengan tema pernikahan Anda.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card h-100 text-center">
                        <i class="bi bi-envelope-paper-heart feature-icon"></i>
                        <h4 class="font-serif fw-bold">RSVP Real-time</h4>
                        <p class="text-muted">Pantau daftar kehadiran tamu dan terima ucapan doa secara langsung di dashboard Anda.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card h-100 text-center">
                        <i class="bi bi-phone feature-icon"></i>
                        <h4 class="font-serif fw-bold">Mobile Responsive</h4>
                        <p class="text-muted">Undangan Anda akan tampil sempurna di semua perangkat, baik smartphone maupun desktop.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Themes Showcase -->
    <section id="tema" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-serif display-5 fw-bold">Koleksi Tema Kami</h2>
                <p class="text-muted">Pilih desain yang merepresentasikan kisah cinta Anda.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Theme 1 -->
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="card theme-card">
                        <img src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop" alt="Elegant Gold">
                        <div class="card-body text-center p-4">
                            <h5 class="font-serif fw-bold">Elegant Gold</h5>
                            <p class="text-muted small">Kombinasi klasik hitam dan emas yang memancarkan kemewahan.</p>
                        </div>
                    </div>
                </div>
                <!-- Theme 2 -->
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card theme-card">
                        <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?q=80&w=800&auto=format&fit=crop" alt="Floral Pastel">
                        <div class="card-body text-center p-4">
                            <h5 class="font-serif fw-bold">Floral Pastel</h5>
                            <p class="text-muted small">Sentuhan warna lembut dan elemen bunga yang romantis.</p>
                        </div>
                    </div>
                </div>
                <!-- Theme 3 -->
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card theme-card">
                        <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=800&auto=format&fit=crop" alt="Minimalist White">
                        <div class="card-body text-center p-4">
                            <h5 class="font-serif fw-bold">Minimalist White</h5>
                            <p class="text-muted small">Desain bersih dan modern dengan fokus pada tipografi yang indah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section text-center">
        <div class="container" data-aos="fade-up">
            <h2 class="font-serif display-4 fw-bold mb-4">Mulai Tulis Kisah Anda Hari Ini.</h2>
            <p class="fs-5 mb-5 opacity-75">Bergabunglah bersama ratusan pasangan lainnya yang telah mempercayakan undangan digital mereka kepada kami.</p>
            <a href="<?= $is_logged_in ? 'client/buat_undangan.php' : 'auth/register.php' ?>" class="btn btn-gold btn-lg px-5 py-3 fs-5 rounded-pill">Buat Undangan Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 text-center" style="background-color: var(--text-dark); color: white;">
        <div class="container">
            <h4 class="font-serif text-gold mb-3" style="color: var(--gold-primary);">MyInvitation</h4>
            <p class="text-muted mb-0 small">&copy; <?= date('Y') ?> MyInvitation Platform. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, offset: 50 });
        
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').style.boxShadow = '0 4px 15px rgba(0,0,0,0.05)';
            } else {
                document.querySelector('.navbar').style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>