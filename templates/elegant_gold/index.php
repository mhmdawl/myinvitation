<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Undangan Pernikahan | <?= htmlspecialchars($data_undangan['mempelai_pria']) ?> & <?= htmlspecialchars($data_undangan['mempelai_wanita']) ?></title>
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- GLightbox (Premium Image Pop-up) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="templates/elegant_gold/style.css">
</head>
<body style="overflow: hidden;">

    <!-- Loading Screen -->
    <div id="loading-screen"><div class="loader"></div></div>

    <!-- Background Music -->
    <?php
        // Cek apakah klien punya lagu custom, jika tidak pakai lagu default
        $file_musik = !empty($data_undangan['musik_bg']) ? 'uploads/musik/' . $data_undangan['musik_bg'] : 'assets/audio/bgm.mp3';
    ?>
    <audio id="bg-music" loop><source src="<?= $file_musik ?>" type="audio/mpeg"></audio>
    <button id="music-control" style="display:none;" onclick="toggleMusic()"><i class="bi bi-music-note-beamed"></i></button>

    <!-- Opening Screen -->
    <div id="opening-screen">
        <p class="text-uppercase tracking-widest mb-2" data-aos="fade-down">The Wedding Of</p>
        <h1 class="font-serif display-1 mb-4"><?= htmlspecialchars($data_undangan['mempelai_pria']) ?> & <?= htmlspecialchars($data_undangan['mempelai_wanita']) ?></h1>
        
        <!-- BAGIAN NAMA TAMU YANG DIUBAH -->
        <p class="mb-1 text-light opacity-75" style="font-size: 0.95rem;">Kepada Yth. Bapak/Ibu/Saudara/i</p>
        
        <h2 class="font-serif mb-2 fw-bold px-3 text-center" style="color: var(--gold-primary);">
            <?= $nama_tamu_undangan ?>
        </h2>
        
        <p class="small text-light mb-4 opacity-50" style="font-size: 0.75rem;">
            *Mohon maaf apabila ada kesalahan penulisan nama/gelar
        </p>
        <!-- AKHIR BAGIAN NAMA TAMU -->

        <button class="btn-premium mt-3" onclick="bukaUndangan()">
            <i class="bi bi-envelope-heart me-2"></i> Buka Undangan
        </button>
    </div>

    <!-- Hero Section -->
    <section class="hero pt-5">
        <div data-aos="zoom-out" data-aos-duration="2000">
            <h1 class="font-accent mt-5"><?= htmlspecialchars($data_undangan['mempelai_pria']) ?> & <?= htmlspecialchars($data_undangan['mempelai_wanita']) ?></h1>
            <p class="font-serif fs-4 mt-3 text-dark"><?= $tanggal_format ?></p>
        </div>
        
        <!-- Modern Countdown -->
        <div class="container mt-4" data-aos="fade-up" data-aos-delay="500">
            <div class="d-flex justify-content-center gap-3">
                <div class="countdown-box text-center"><h3 id="days">00</h3><small class="text-uppercase" style="font-size:10px;">Hari</small></div>
                <div class="countdown-box text-center"><h3 id="hours">00</h3><small class="text-uppercase" style="font-size:10px;">Jam</small></div>
                <div class="countdown-box text-center"><h3 id="mins">00</h3><small class="text-uppercase" style="font-size:10px;">Menit</small></div>
                <div class="countdown-box text-center"><h3 id="secs">00</h3><small class="text-uppercase" style="font-size:10px;">Detik</small></div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="position-absolute bottom-0 mb-4 animate-bounce text-dark">
            <small>Scroll ke bawah</small><br>
            <i class="bi bi-chevron-down fs-4"></i>
        </div>
    </section>

    <!-- Quote Section -->
    <section class="py-5 bg-beige text-center">
        <div class="container px-4" data-aos="fade-up">
            <i class="bi bi-quote fs-1 text-gold"></i>
            <p class="font-serif fs-4 mt-2" style="color: var(--text-muted);">"Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu isteri-isteri dari jenismu sendiri, supaya kamu cenderung dan merasa tenteram kepadanya, dan dijadikan-Nya diantaramu rasa kasih dan sayang."</p>
            <p class="text-uppercase mt-3" style="letter-spacing: 2px; font-size: 0.8rem;">(QS. Ar-Rum: 21)</p>
        </div>
    </section>

    <!-- Event Detail (Glassmorphism) -->
    <section class="py-5" style="background: url('https://images.unsplash.com/photo-1520854221256-17451cc331bf?q=80&w=2000&auto=format&fit=crop') center/cover fixed;">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-serif text-white display-5">Rangkaian Acara</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8" data-aos="zoom-in">
                    <div class="glass-card p-5 text-center">
                        <h3 class="font-serif text-gold mb-4">Resepsi Pernikahan</h3>
                        <div class="d-flex justify-content-center align-items-center gap-4 mb-4">
                            <div class="text-end">
                                <h4 class="mb-0"><?= date('l', strtotime($data_undangan['tanggal_acara'])) ?></h4>
                            </div>
                            <div class="border-start border-end border-dark px-4 py-2">
                                <h1 class="mb-0 font-serif"><?= date('d', strtotime($data_undangan['tanggal_acara'])) ?></h1>
                            </div>
                            <div class="text-start">
                                <h4 class="mb-0"><?= date('M Y', strtotime($data_undangan['tanggal_acara'])) ?></h4>
                            </div>
                        </div>
                        <p class="mb-4"><i class="bi bi-clock"></i> Sesuai Waktu Undangan</p>
                        <p class="mb-4 fw-light"><strong>Lokasi:</strong><br><?= nl2br(htmlspecialchars($data_undangan['lokasi_acara'])) ?></p>
                        <?php if(!empty($data_undangan['map_link'])): ?>
                            <a href="<?= $data_undangan['map_link'] ?>" target="_blank" class="btn-premium"><i class="bi bi-geo-alt"></i> Buka Google Maps</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Masonry Gallery -->
    <section class="py-5 bg-cream">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-serif display-5">Our Moments</h2>
                <p class="text-muted">Kenangan indah yang kami bagikan.</p>
            </div>
            <div class="masonry-grid" data-aos="fade-up" data-aos-delay="200">
                <?php if(isset($galeri_query) && $galeri_query->num_rows > 0): ?>
                    <!-- Menampilkan foto dari database yang diupload client -->
                    <?php while($foto = $galeri_query->fetch_assoc()): ?>
                        <a href="uploads/galeri/<?= $foto['nama_file'] ?>" class="glightbox">
                            <img src="uploads/galeri/<?= $foto['nama_file'] ?>" alt="Gallery" loading="lazy">
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Fallback Dummy jika client belum upload foto sama sekali -->
                    <a href="https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800" class="glightbox"><img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=400" alt="Gallery"></a>
                    <a href="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800" class="glightbox"><img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400" alt="Gallery"></a>
                    <a href="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=800" class="glightbox"><img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=400" alt="Gallery"></a>
                    <a href="https://images.unsplash.com/photo-1519741497674-611481863552?w=800" class="glightbox"><img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=400" alt="Gallery"></a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- RSVP & Guestbook (Floating Labels) -->
    <!-- Wedding Gift / Amplop Digital -->
    <section class="py-5 bg-white text-center">
        <div class="container py-4">
            <div class="text-center mb-5" data-aos="fade-up">
                <i class="bi bi-gift fs-1 text-gold mb-3"></i>
                <h2 class="font-serif display-5">Wedding Gift</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Doa restu Anda merupakan karunia yang sangat berarti bagi kami. Namun jika Anda bermaksud memberikan tanda kasih, Anda dapat mengirimkannya melalui:</p>
            </div>
            
            <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="col-md-6 col-lg-5">
                    <div class="glass-card p-4 shadow-sm" style="background: #FDFBF7;">
                        <?php if(!empty($data_undangan['qris_image'])): ?>
                            <img src="uploads/qris/<?= $data_undangan['qris_image'] ?>" alt="QRIS" class="img-fluid rounded mb-4 shadow-sm" style="max-width: 250px;">
                        <?php endif; ?>
                        
                        <?php if(!empty($data_undangan['rekening_bank'])): ?>
                            <div class="p-3 border rounded bg-white">
                                <h6 class="font-serif fw-bold text-dark mb-2">Transfer Bank / E-Wallet</h6>
                                <p class="mb-0 text-muted" style="white-space: pre-wrap; font-size: 0.95rem;"><?= htmlspecialchars($data_undangan['rekening_bank']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <p class="small text-muted mt-4 mb-0 opacity-75">Terima kasih atas doa dan tanda kasih yang diberikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-beige">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-6" data-aos="fade-right">
                    <h2 class="font-serif mb-4">Ucapan</h2>
                    <p class="text-muted mb-4">Mohon konfirmasi kehadiran Anda untuk memudahkan kami menyiapkan yang terbaik.</p>
                    <div class="glass-card p-4">
                        <form id="rsvpForm">
                            <input type="hidden" name="undangan_id" value="<?= $undangan_id ?>">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="nama" id="floatingNama" placeholder="Nama Anda" required>
                                <label for="floatingNama">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" name="kehadiran" id="floatingKehadiran" required>
                                    <option value="" selected disabled>Pilih status kehadiran</option>
                                    <option value="Hadir">Ya, Saya Hadir</option>
                                    <option value="Tidak Hadir">Maaf, Tidak Bisa Hadir</option>
                                </select>
                                <label for="floatingKehadiran">Konfirmasi Kehadiran</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" name="jumlah" id="floatingJumlah" placeholder="Jumlah Tamu" min="1" max="5">
                                <label for="floatingJumlah">Jumlah Tamu</label>
                            </div>
                            <div class="form-floating mb-4">
                                <textarea class="form-control" name="ucapan" placeholder="Ucapan" id="floatingUcapan" style="height: 100px" required></textarea>
                                <label for="floatingUcapan">Berikan ucapan untuk kami...</label>
                            </div>
                            <button type="submit" class="btn-premium w-100">Kirim Konfirmasi</button>
                        </form>
                        <div id="rsvpAlert" class="mt-3"></div>
                    </div>
                </div>

                <div class="col-md-6" data-aos="fade-left">
                    <h2 class="font-serif mb-4">Guestbook</h2>
                    <div class="glass-card p-4 h-100">
                        <div class="guestbook-container" style="max-height: 400px; overflow-y: auto; padding-right:15px;">
                            <?php if($ucapan_query->num_rows > 0): ?>
                                <?php while($row = $ucapan_query->fetch_assoc()): ?>
                                    <div class="border-bottom border-light pb-3 mb-3">
                                        <h6 class="mb-1 fw-bold"><?= htmlspecialchars($row['nama_tamu']) ?> 
                                            <span class="badge rounded-pill <?= $row['kehadiran']=='Hadir' ? 'bg-success' : 'bg-secondary' ?> fw-normal ms-2" style="font-size:0.7rem;">
                                                <i class="bi <?= $row['kehadiran']=='Hadir' ? 'bi-check-circle' : 'bi-x-circle' ?>"></i> <?= $row['kehadiran'] ?>
                                            </span>
                                        </h6>
                                        <small class="text-muted" style="font-size:0.8rem;"><?= date('d M Y', strtotime($row['created_at'])) ?></small>
                                        <p class="mt-2 mb-0" style="font-size:0.95rem; color: var(--text-dark);">"<?= htmlspecialchars($row['ucapan']) ?>"</p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-muted mt-5">Jadilah yang pertama memberikan ucapan bahagia!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 text-center bg-dark text-white">
        <div class="container" data-aos="fade-up">
            <h2 class="font-accent mt-3"><?= htmlspecialchars($data_undangan['mempelai_pria']) ?> & <?= htmlspecialchars($data_undangan['mempelai_wanita']) ?></h2>
            <p class="text-muted mt-3 mb-0">Created with ❤️ by MyInvitation</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    
    <script>
        // Init Loading Screen
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loading-screen').style.opacity = '0';
                setTimeout(() => { document.getElementById('loading-screen').style.display = 'none'; }, 800);
            }, 1000);
        });

        // Init Animations
        AOS.init({ once: true, offset: 100 });
        const lightbox = GLightbox({ touchNavigation: true, loop: true, autoplayVideos: true });

        // Logic Buka Undangan & Music
        const music = document.getElementById("bg-music");
        const musicBtn = document.getElementById("music-control");
        let isPlaying = false;

        function bukaUndangan() {
            document.getElementById('opening-screen').classList.add('slide-up');
            document.body.style.overflow = 'auto'; // Enable scrolling
            musicBtn.style.display = 'flex';
            music.play();
            isPlaying = true;
            musicBtn.querySelector('i').classList.add('spin-slow');
        }

        function toggleMusic() {
            if(isPlaying) {
                music.pause();
                musicBtn.querySelector('i').classList.remove('spin-slow');
                musicBtn.innerHTML = '<i class="bi bi-music-note"></i>';
            } else {
                music.play();
                musicBtn.innerHTML = '<i class="bi bi-music-note-beamed spin-slow"></i>';
            }
            isPlaying = !isPlaying;
        }

        // Countdown Timer
        const countDownDate = new Date("<?= $tanggal_countdown ?>").getTime();
        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            if (distance < 0) { clearInterval(x); return; }
            document.getElementById("days").innerHTML = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
            document.getElementById("hours").innerHTML = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
            document.getElementById("mins").innerHTML = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
            document.getElementById("secs").innerHTML = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
        }, 1000);

        // AJAX RSVP Submit
        document.getElementById('rsvpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
            btn.disabled = true;

            let formData = new FormData(this);
            fetch('api/submit_rsvp.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                let alertDiv = document.getElementById('rsvpAlert');
                if(data.status === 'success') {
                    alertDiv.innerHTML = `<div class="alert alert-success border-0 shadow-sm">${data.message}</div>`;
                    this.reset();
                    setTimeout(() => location.reload(), 1500); 
                } else {
                    alertDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    btn.innerHTML = 'Kirim Konfirmasi'; btn.disabled = false;
                }
            })
            .catch(error => { console.error('Error:', error); btn.disabled = false; });
        });
    </script>
</body>
</html>