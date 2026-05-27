<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan yang login adalah client
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';

// Ambil ID Undangan milik client ini
$query_undangan = $conn->query("SELECT * FROM detail_undangan WHERE user_id = '$user_id'");
if ($query_undangan->num_rows == 0) {
    header("Location: index.php");
    exit;
}
$data_undangan = $query_undangan->fetch_assoc();
$undangan_id = $data_undangan['id'];
$url_undangan = $data_undangan['url_undangan'];

// PENTING: Ganti dengan domain utama Anda
$domain_utama = "http://muhamadawal.my.id/"; 

// Logic Tambah Tamu Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_tamu'])) {
    $nama_tamu = $conn->real_escape_string($_POST['nama_tamu']);
    $insert = $conn->query("INSERT INTO daftar_tamu (undangan_id, nama_tamu) VALUES ('$undangan_id', '$nama_tamu')");
    if ($insert) {
        $pesan = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="bi bi-check-circle me-2"></i> Nama tamu berhasil ditambahkan!</div>';
    }
}

// Logic Hapus Tamu
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $conn->query("DELETE FROM daftar_tamu WHERE id = '$id_hapus' AND undangan_id = '$undangan_id'");
    header("Location: kelola_tamu.php");
    exit;
}

// Ambil List Tamu dari database
$list_tamu = $conn->query("SELECT * FROM daftar_tamu WHERE undangan_id = '$undangan_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tamu - Premium Invitation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #FDFBF7; color: #2C2A29; }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .dashboard-card { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); padding: 30px; }
        .btn-gold { background-color: #C5A880; color: white; border-radius: 30px; padding: 10px 25px; border: none; transition: 0.3s; }
        .btn-gold:hover { background-color: #9B825E; color: white; }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-serif fw-bold mb-0">Kelola Daftar Tamu</h3>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
            </div>

            <?= $pesan; ?>

            <div class="row g-4">
                <!-- Box Kiri: Form Input Tamu -->
                <div class="col-md-4">
                    <div class="dashboard-card h-100">
                        <h5 class="fw-bold mb-3">Tambah Tamu</h5>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nama Tamu / Gelar</label>
                                <input type="text" name="nama_tamu" class="form-control" required placeholder="Cth: Bpk. Andi & Keluarga">
                            </div>
                            <button type="submit" name="tambah_tamu" class="btn btn-gold w-100"><i class="bi bi-plus-circle"></i> Tambah ke Daftar</button>
                        </form>
                    </div>
                </div>

                <!-- Box Kanan: Tabel Daftar Tamu & Copy Link -->
                <div class="col-md-8">
                    <div class="dashboard-card h-100">
                        <h5 class="fw-bold mb-3">Link Undangan Khusus</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Tamu</th>
                                        <th class="text-center">Link Khusus</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($list_tamu->num_rows > 0): ?>
                                        <?php while($row = $list_tamu->fetch_assoc()): 
                                            // Membentuk link dinamis: ?u=url-undangan&to=Nama+Tamu
                                            $link_khusus = $domain_utama . "?u=" . $url_undangan . "&to=" . urlencode($row['nama_tamu']);
                                        ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($row['nama_tamu']) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-dark rounded-pill px-3" onclick="copyToClipboard('<?= $link_khusus ?>')">
                                                    <i class="bi bi-link-45deg"></i> Salin Link
                                                </button>
                                            </td>
                                            <td class="text-end">
                                                <!-- Tombol Hapus -->
                                                <a href="kelola_tamu.php?hapus=<?= $row['id'] ?>" class="text-danger" onclick="return confirm('Hapus nama ini dari daftar?')"><i class="bi bi-trash3"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada daftar tamu.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        var dummy = document.createElement("textarea");
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand("copy");
        document.body.removeChild(dummy);
        alert("Link khusus berhasil disalin!\n" + text);
    }
</script>

</body>
</html>