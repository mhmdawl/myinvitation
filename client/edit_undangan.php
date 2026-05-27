<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'client') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$pesan = '';

$query = $conn->query("SELECT * FROM detail_undangan WHERE user_id = '$user_id'");
if ($query->num_rows == 0) {
    header("Location: buat_undangan.php");
    exit;
}
$data = $query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pria = $conn->real_escape_string($_POST['mempelai_pria']);
    $wanita = $conn->real_escape_string($_POST['mempelai_wanita']);
    $tanggal = $conn->real_escape_string($_POST['tanggal_acara']);
    $lokasi = $conn->real_escape_string($_POST['lokasi_acara']);
    $map = $conn->real_escape_string($_POST['map_link']);
    $rekening = $conn->real_escape_string($_POST['rekening_bank']);
    $tema = $conn->real_escape_string($_POST['tema_aktif']);

   // Logika Upload QRIS
    $qris_query_part = "";
    if (!empty($_FILES['qris_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['qris_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['qris_image']['size'] <= 2000000) {
            $nama_file_baru = uniqid('qris_') . '.' . $ext;
            $path_tujuan = '../uploads/qris/' . $nama_file_baru;
            
            if (move_uploaded_file($_FILES['qris_image']['tmp_name'], $path_tujuan)) {
                if (!empty($data['qris_image']) && file_exists('../uploads/qris/' . $data['qris_image'])) {
                    unlink('../uploads/qris/' . $data['qris_image']);
                }
                $qris_query_part = ", qris_image = '$nama_file_baru'";
                $data['qris_image'] = $nama_file_baru; 
            }
        } else {
            $pesan = '<div class="alert alert-danger">Gagal upload QRIS. Pastikan format JPG/PNG dan maks 2MB.</div>';
        }
    }

    // LOGIKA UPLOAD MUSIK MP3
    $musik_query_part = "";
    if (!empty($_FILES['musik_bg']['name'])) {
        $ext_musik = strtolower(pathinfo($_FILES['musik_bg']['name'], PATHINFO_EXTENSION));
        if ($ext_musik == 'mp3' && $_FILES['musik_bg']['size'] <= 5000000) { // Maks 5MB
            $nama_musik_baru = uniqid('musik_') . '.mp3';
            $path_tujuan_musik = '../uploads/musik/' . $nama_musik_baru;
            
            if (move_uploaded_file($_FILES['musik_bg']['tmp_name'], $path_tujuan_musik)) {
                if (!empty($data['musik_bg']) && file_exists('../uploads/musik/' . $data['musik_bg'])) {
                    unlink('../uploads/musik/' . $data['musik_bg']);
                }
                $musik_query_part = ", musik_bg = '$nama_musik_baru'";
                $data['musik_bg'] = $nama_musik_baru;
            }
        } else {
            $pesan = '<div class="alert alert-danger">Gagal upload musik. Pastikan format MP3 dan maks 5MB.</div>';
        }
    }

    // UPDATE QUERY (Menambahkan $musik_query_part)
    $sql = "UPDATE detail_undangan SET 
            tema_aktif = '$tema', mempelai_pria = '$pria', mempelai_wanita = '$wanita', 
            tanggal_acara = '$tanggal', lokasi_acara = '$lokasi', map_link = '$map', 
            rekening_bank = '$rekening' $qris_query_part $musik_query_part 
            WHERE user_id = '$user_id'";

    if (empty($pesan)) {
        if ($conn->query($sql) === TRUE) {
            $pesan = '<div class="alert alert-success border-0 shadow-sm rounded-4"><i class="bi bi-check-circle me-2"></i> Data undangan berhasil diperbarui!</div>';
            $data['mempelai_pria'] = $pria; $data['mempelai_wanita'] = $wanita;
            $data['tanggal_acara'] = $tanggal; $data['lokasi_acara'] = $lokasi;
            $data['map_link'] = $map; $data['rekening_bank'] = $rekening; $data['tema_aktif'] = $tema;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal mengupdate data: ' . $conn->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Undangan - Premium Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #FDFBF7; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); }
        .btn-gold { background-color: #C5A880; color: white; border-radius: 30px; padding: 12px; transition: 0.3s; font-weight: 500;}
        .btn-gold:hover { background-color: #9B825E; color: white; }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 fw-bold" style="font-family: serif;">Edit Data Undangan</h3>
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>

            <?= $pesan; ?>

            <div class="card form-card">
                <div class="card-body p-4 p-md-5">
                    <!-- Tambahkan enctype agar bisa upload gambar -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <h5 class="mb-3 text-dark border-bottom pb-2 fw-bold">1. Data Mempelai</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Nama Pria</label>
                                <input type="text" name="mempelai_pria" class="form-control" required value="<?= htmlspecialchars($data['mempelai_pria']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Nama Wanita</label>
                                <input type="text" name="mempelai_wanita" class="form-control" required value="<?= htmlspecialchars($data['mempelai_wanita']) ?>">
                            </div>
                        </div>

                        <h5 class="mb-3 text-dark border-bottom pb-2 fw-bold">2. Detail Acara</h5>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Waktu Acara</label>
                            <input type="datetime-local" name="tanggal_acara" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_acara'])) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Lokasi</label>
                            <textarea name="lokasi_acara" class="form-control" rows="2" required><?= htmlspecialchars($data['lokasi_acara']) ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Link Google Maps</label>
                            <input type="url" name="map_link" class="form-control" value="<?= htmlspecialchars($data['map_link']) ?>">
                        </div>

                        <h5 class="mb-3 text-dark border-bottom pb-2 fw-bold">3. Amplop Digital (Wedding Gift)</h5>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Info Rekening Bank (No. Rekening & Atas Nama)</label>
                            <textarea name="rekening_bank" class="form-control" rows="2" placeholder="BCA 123456789 a.n Romeo"><?= htmlspecialchars($data['rekening_bank']) ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small">Upload Barcode QRIS (Opsional)</label>
                            <?php if(!empty($data['qris_image'])): ?>
                                <div class="mb-2">
                                    <img src="../uploads/qris/<?= $data['qris_image'] ?>" alt="QRIS" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="qris_image" class="form-control" accept="image/jpeg, image/png, image/jpg">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah QRIS.</small>
                        </div>

                        <!-- KOLOM INPUT MUSIK MP3 -->
                        <div class="mb-4 pt-3 border-top">
                            <label class="form-label text-muted small fw-bold">Upload Background Music (MP3)</label>
                            <?php if(!empty($data['musik_bg'])): ?>
                                <div class="mb-2">
                                    <audio controls class="w-100 mt-2" style="height: 40px;">
                                        <source src="../uploads/musik/<?= $data['musik_bg'] ?>" type="audio/mpeg">
                                    </audio>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="musik_bg" class="form-control" accept=".mp3">
                            <small class="text-muted d-block mt-1">Format wajib MP3. Maksimal 5MB. Biarkan kosong jika tidak ingin mengubah lagu.</small>
                        </div>

                        <h5 class="mb-3 text-dark border-bottom pb-2 fw-bold">4. Tema Undangan</h5>
                        <div class="mb-5">
                            <select name="tema_aktif" class="form-select form-select-lg" required>
                                <option value="elegant_gold" <?= $data['tema_aktif'] == 'elegant_gold' ? 'selected' : '' ?>>👑 Elegant Gold (Premium)</option>
                                <option value="floral_pastel" <?= $data['tema_aktif'] == 'floral_pastel' ? 'selected' : '' ?>>🌸 Floral Pastel (Romantis)</option>
                                <option value="minimalist" <?= $data['tema_aktif'] == 'minimalist' ? 'selected' : '' ?>>⚪ Minimalist White (Clean)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-3 rounded-pill">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>