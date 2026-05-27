<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan client sudah login
if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';

// Proteksi: Jika user sudah punya undangan, kembalikan ke dashboard
$cek_undangan = $conn->query("SELECT * FROM detail_undangan WHERE user_id = '$user_id'");
if ($cek_undangan->num_rows > 0) {
    header("Location: index.php");
    exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pria = $conn->real_escape_string($_POST['mempelai_pria']);
    $wanita = $conn->real_escape_string($_POST['mempelai_wanita']);
    $tanggal = $conn->real_escape_string($_POST['tanggal_acara']);
    $lokasi = $conn->real_escape_string($_POST['lokasi_acara']);
    $map = $conn->real_escape_string($_POST['map_link']);
    $rekening = $conn->real_escape_string($_POST['rekening_bank']);
    $tema = $conn->real_escape_string($_POST['tema_aktif']);

    // Membuat URL unik secara otomatis (Contoh hasil: ade-istri-834)
    $url_bersih_pria = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($pria));
    $url_bersih_wanita = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($wanita));
    $url_undangan = $url_bersih_pria . '-' . $url_bersih_wanita . '-' . rand(100, 999);

    // Simpan ke database
    $sql = "INSERT INTO detail_undangan 
            (user_id, url_undangan, tema_aktif, mempelai_pria, mempelai_wanita, tanggal_acara, lokasi_acara, map_link, rekening_bank) 
            VALUES ('$user_id', '$url_undangan', '$tema', '$pria', '$wanita', '$tanggal', '$lokasi', '$map', '$rekening')";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, arahkan kembali ke dashboard
        header("Location: index.php");
        exit;
    } else {
        $pesan = '<div class="alert alert-danger">Gagal menyimpan undangan: ' . $conn->error . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Undangan - Undangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .form-card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 fw-bold">Buat Undangan Baru</h3>
                <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <?= $pesan; ?>

            <div class="card form-card">
                <div class="card-body p-4">
                    <form action="" method="POST">
                        
                        <h5 class="mb-3 text-primary border-bottom pb-2">1. Data Mempelai</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Panggilan Mempelai Pria</label>
                                <input type="text" name="mempelai_pria" class="form-control" required placeholder="Contoh: Romeo">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Panggilan Mempelai Wanita</label>
                                <input type="text" name="mempelai_wanita" class="form-control" required placeholder="Contoh: Juliet">
                            </div>
                        </div>

                        <h5 class="mb-3 text-primary border-bottom pb-2">2. Detail Acara</h5>
                        <div class="mb-3">
                            <label class="form-label">Tanggal & Waktu Acara</label>
                            <input type="datetime-local" name="tanggal_acara" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi / Gedung Acara</label>
                            <textarea name="lokasi_acara" class="form-control" rows="2" required placeholder="Contoh: Gedung Serbaguna, Jl. Mawar No. 123..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Link Google Maps (Opsional)</label>
                            <input type="url" name="map_link" class="form-control" placeholder="https://maps.google.com/...">
                        </div>

                        <h5 class="mb-3 text-primary border-bottom pb-2">3. Amplop Digital</h5>
                        <div class="mb-4">
                            <label class="form-label">Informasi Rekening Bank / E-Wallet</label>
                            <textarea name="rekening_bank" class="form-control" rows="2" placeholder="Contoh: BCA 1234567890 a.n Romeo"></textarea>
                        </div>

                        <h5 class="mb-3 text-primary border-bottom pb-2">4. Pilih Tema Undangan</h5>
                        <div class="mb-4">
                            <select name="tema_aktif" class="form-select form-select-lg" required>
                                <option value="" selected disabled>-- Silakan Pilih Tema --</option>
                                <option value="elegant_gold">👑 Elegant Gold (Premium)</option>
                                <option value="floral_pastel">🌸 Floral Pastel (Romantis)</option>
                                <option value="minimalist">⚪ Minimalist White (Clean)</option>
                                <option value="islamic_green">🕌 Islamic Green (Elegan)</option>
                                <option value="dark_luxury">🌃 Dark Luxury (Eksklusif)</option>
                            </select>
                            <small class="text-muted">Tenang, Anda bisa mengubah tema ini nanti.</small>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">Simpan & Buat Undangan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>