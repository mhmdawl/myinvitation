<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi akses
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_lengkap = $_SESSION['nama_lengkap'];

// Cek apakah punya undangan
$query_undangan = $conn->query("SELECT * FROM detail_undangan WHERE user_id = '$user_id'");
$punya_undangan = $query_undangan->num_rows > 0;

if ($punya_undangan) {
    $data_undangan = $query_undangan->fetch_assoc();
    $undangan_id = $data_undangan['id'];
   $link_undangan = "http://muhamadawal.my.id/?u=" . $data_undangan['url_undangan'];

    // Hitung Statistik RSVP
    $stat_query = $conn->query("SELECT 
        COUNT(id) as total_ucapan,
        SUM(CASE WHEN kehadiran = 'Hadir' THEN jumlah_tamu ELSE 0 END) as estimasi_hadir,
        SUM(CASE WHEN kehadiran = 'Tidak Hadir' THEN 1 ELSE 0 END) as total_absen
        FROM rsvp WHERE undangan_id = '$undangan_id'");
    $stat = $stat_query->fetch_assoc();
    
    // Ambil detail list tamu
    $rsvp_list = $conn->query("SELECT * FROM rsvp WHERE undangan_id = '$undangan_id' ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Premium Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #FDFBF7; color: #2C2A29; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .navbar { background-color: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(10px); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .navbar-brand { font-family: 'Cormorant Garamond', serif; color: #C5A880 !important; font-size: 1.8rem; }
        .dashboard-card { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); padding: 40px 30px; }
        .stat-card { border-radius: 15px; border: 1px solid rgba(197, 168, 128, 0.2); background: #FDFBF7; padding: 20px; text-align: center; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(197, 168, 128, 0.15); }
        .btn-gold { background-color: #C5A880; color: white; border-radius: 30px; padding: 10px 25px; border: none; transition: 0.3s ease; font-weight: 500; }
        .btn-gold:hover { background-color: #9B825E; color: white; transform: translateY(-2px); }
        .btn-outline-gold { border: 1px solid #C5A880; color: #C5A880; border-radius: 30px; padding: 10px 25px; transition: 0.3s ease; background: transparent; }
        .btn-outline-gold:hover { background: #C5A880; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">MyInvitation</a>
        <div class="d-flex align-items-center">
            <span class="me-4 text-muted d-none d-md-inline">Halo, <span class="text-dark fw-semibold"><?= htmlspecialchars($nama_lengkap) ?></span></span>
            <a href="../auth/logout.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-3 pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="text-center mb-5">
                <h2 class="font-serif display-6">Dashboard Panel</h2>
                <p class="text-muted">Kelola undangan dan pantau kehadiran tamu Anda.</p>
            </div>

            <?php if (!$punya_undangan): ?>
                <!-- JIKA BELUM BUAT UNDANGAN -->
                <div class="dashboard-card text-center">
                    <i class="bi bi-envelope-paper font-serif" style="font-size: 5rem; color: #E5D5C1;"></i>
                    <h3 class="font-serif mt-4">Belum Ada Undangan</h3>
                    <p class="text-muted mb-4 px-md-5">Mulai perjalanan indah Anda dengan membuat undangan digital premium.</p>
                    <a href="buat_undangan.php" class="btn btn-gold btn-lg"><i class="bi bi-plus-lg me-2"></i> Buat Undangan Sekarang</a>
                </div>
            <?php else: ?>
                <!-- JIKA SUDAH PUNYA UNDANGAN -->
                <div class="dashboard-card mb-5">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-4 mb-md-0">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 mb-3 border border-success border-opacity-25">Status: Aktif</span>
                            <h3 class="font-serif mb-2">Undangan Anda Siap!</h3>
                            <p class="text-muted mb-4">Salin tautan di bawah ini dan kirimkan kepada tamu Anda.</p>
                            <div class="input-group shadow-sm rounded-pill">
                                <input type="text" class="form-control bg-light border-0 px-4" id="linkUndangan" value="<?= $link_undangan ?>" readonly style="border-radius: 30px 0 0 30px;">
                                <button class="btn btn-dark px-4" type="button" onclick="copyLink()" style="border-radius: 0 30px 30px 0;">
                                    Salin Link
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5 text-center text-md-end border-start border-light ps-md-4">
                            <div class="d-grid gap-3">
                                <a href="<?= $link_undangan ?>" target="_blank" class="btn btn-gold"><i class="bi bi-eye me-2"></i> Pratinjau Undangan</a>
                                <a href="edit_undangan.php" class="btn btn-outline-gold"><i class="bi bi-pencil me-2"></i> Edit Data & Tema</a>
                                <a href="kelola_tamu.php" class="btn btn-dark"><i class="bi bi-people me-2"></i> Buat Link Tamu Khusus</a>
                                
                                <!-- TOMBOL BARU: KELOLA GALERI -->
                                <a href="kelola_galeri.php" class="btn btn-outline-dark"><i class="bi bi-images me-2"></i> Kelola Foto Galeri</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN BARU: STATISTIK & REKAP RSVP -->
                <h3 class="font-serif fw-bold mb-4">Rekap Kehadiran</h3>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h1 class="font-serif text-gold display-5 mb-0"><?= $stat['total_ucapan'] ?></h1>
                            <p class="text-muted mb-0 mt-2 small text-uppercase tracking-widest">Total Respon</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="border-color: rgba(25, 135, 84, 0.3);">
                            <h1 class="font-serif text-success display-5 mb-0"><?= (int)$stat['estimasi_hadir'] ?></h1>
                            <p class="text-muted mb-0 mt-2 small text-uppercase tracking-widest">Estimasi Tamu Hadir</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card" style="border-color: rgba(220, 53, 69, 0.3);">
                            <h1 class="font-serif text-danger display-5 mb-0"><?= (int)$stat['total_absen'] ?></h1>
                            <p class="text-muted mb-0 mt-2 small text-uppercase tracking-widest">Tidak Bisa Hadir</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">Nama Tamu</th>
                                    <th class="py-3">Kehadiran</th>
                                    <th class="py-3 text-center">Jumlah</th>
                                    <th class="py-3">Ucapan & Doa</th>
                                    <th class="pe-4 py-3 text-end">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($rsvp_list->num_rows > 0): ?>
                                    <?php while($row = $rsvp_list->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($row['nama_tamu']) ?></td>
                                        <td>
                                            <?php if($row['kehadiran'] == 'Hadir'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-check-circle"></i> Hadir</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-x-circle"></i> Tidak Hadir</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center fw-bold text-muted"><?= $row['jumlah_tamu'] ?></td>
                                        <td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= htmlspecialchars($row['ucapan']) ?>">
                                            <small class="text-muted">"<?= htmlspecialchars($row['ucapan']) ?>"</small>
                                        </td>
                                        <td class="pe-4 text-end small text-muted"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-5">Belum ada tamu yang mengisi buku tamu</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function copyLink() {
        var copyText = document.getElementById("linkUndangan");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Link berhasil disalin!");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>