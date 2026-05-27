<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua data client
$query_clients = $conn->query("SELECT u.id, u.nama_lengkap, u.username, u.created_at, d.url_undangan 
                               FROM users u 
                               LEFT JOIN detail_undangan d ON u.id = d.user_id 
                               WHERE u.role = 'client' ORDER BY u.created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - MyInvitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #F8F9FA; }
        .admin-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="#">Admin Panel</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-4 text-light">Halo, Administrator</span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm rounded-pill"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Manajemen Client</h3>
        <a href="tambah_client.php" class="btn btn-dark rounded-pill px-4 shadow-sm"><i class="bi bi-person-plus me-2"></i> Daftarkan Client Baru</a>
    </div>

    <!-- Menampilkan Pesan Notifikasi Sukses/Error -->
    <?php if(isset($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['pesan_sukses']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['pesan_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $_SESSION['pesan_error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['pesan_error']); ?>
    <?php endif; ?>

    <div class="card admin-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Client</th>
                        <th>Username Login</th>
                        <th>Status Undangan</th>
                        <th>Tanggal Daftar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if($query_clients->num_rows > 0):
                        while($row = $query_clients->fetch_assoc()): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                        <td>
                            <?php if($row['url_undangan']): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Aktif</span>
                                <a href="../?u=<?= $row['url_undangan'] ?>" target="_blank" class="ms-2 small text-decoration-none">Lihat Web <i class="bi bi-box-arrow-up-right"></i></a>
                            <?php else: ?>
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">Belum Dibuat</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td class="text-center">
                            <!-- Tombol Hapus dengan validasi Javascript -->
                            <a href="hapus_client.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Apakah Anda yakin ingin menghapus client \'<?= htmlspecialchars($row['nama_lengkap']) ?>\'? \n\nSemua data undangan dan ucapan RSVP milik client ini akan terhapus secara permanen!')">
                                <i class="bi bi-trash3"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada client yang terdaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tambahan JS Bootstrap untuk tombol close alert -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>