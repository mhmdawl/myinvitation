<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';

$query_undangan = $conn->query("SELECT id FROM detail_undangan WHERE user_id = '$user_id'");
if ($query_undangan->num_rows == 0) {
    header("Location: index.php");
    exit;
}
$data_undangan = $query_undangan->fetch_assoc();
$undangan_id = $data_undangan['id'];

// Logika Upload Foto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $nama_file_asli = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];
    $ukuran_file = $_FILES['foto']['size'];
    $ext = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    
    // Validasi Ekstensi & Ukuran (Maks 3MB)
    $ext_diizinkan = ['jpg', 'jpeg', 'png'];
    if (in_array($ext, $ext_diizinkan)) {
        if ($ukuran_file <= 3000000) {
            $nama_file_baru = uniqid('galeri_') . '.' . $ext;
            $path_tujuan = '../uploads/galeri/' . $nama_file_baru;
            
            if (move_uploaded_file($tmp_file, $path_tujuan)) {
                $conn->query("INSERT INTO galeri (undangan_id, nama_file) VALUES ('$undangan_id', '$nama_file_baru')");
                $pesan = '<div class="alert alert-success">Foto berhasil diunggah!</div>';
            } else {
                $pesan = '<div class="alert alert-danger">Gagal memindahkan file foto.</div>';
            }
        } else {
            $pesan = '<div class="alert alert-danger">Ukuran foto terlalu besar (Maks 3MB).</div>';
        }
    } else {
        $pesan = '<div class="alert alert-danger">Format file tidak diizinkan. Hanya JPG, JPEG, PNG.</div>';
    }
}

// Logika Hapus Foto
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $cek_foto = $conn->query("SELECT nama_file FROM galeri WHERE id = '$id_hapus' AND undangan_id = '$undangan_id'");
    if ($cek_foto->num_rows > 0) {
        $data_foto = $cek_foto->fetch_assoc();
        $path_file = '../uploads/galeri/' . $data_foto['nama_file'];
        if (file_exists($path_file)) { unlink($path_file); } // Hapus dari server
        $conn->query("DELETE FROM galeri WHERE id = '$id_hapus'"); // Hapus dari database
        header("Location: kelola_galeri.php");
        exit;
    }
}

$galeri = $conn->query("SELECT * FROM galeri WHERE undangan_id = '$undangan_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Galeri - Premium Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #FDFBF7; }
        .dashboard-card { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); padding: 30px; }
        .foto-thumbnail { width: 100%; height: 200px; object-fit: cover; border-radius: 10px; }
        .btn-gold { background-color: #C5A880; color: white; border-radius: 30px; padding: 10px 25px; transition: 0.3s; }
        .btn-gold:hover { background-color: #9B825E; color: white; }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0" style="font-family: 'Cormorant Garamond', serif;">Galeri Pre-Wedding</h3>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <?= $pesan; ?>

            <div class="dashboard-card mb-4">
                <h5 class="fw-bold mb-3">Unggah Foto Baru</h5>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="file" name="foto" class="form-control" accept="image/jpeg, image/png, image/jpg" required>
                        <button type="submit" class="btn btn-gold">Unggah <i class="bi bi-cloud-arrow-up"></i></button>
                    </div>
                    <small class="text-muted mt-2 d-block">Maksimal 3MB per foto. Format: JPG, JPEG, PNG.</small>
                </form>
            </div>

            <div class="row g-4">
                <?php if($galeri->num_rows > 0): ?>
                    <?php while($row = $galeri->fetch_assoc()): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="position-relative">
                            <img src="../uploads/galeri/<?= $row['nama_file'] ?>" class="foto-thumbnail shadow-sm">
                            <a href="kelola_galeri.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" onclick="return confirm('Hapus foto ini?')" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Belum ada foto yang diunggah.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

</body>
</html>