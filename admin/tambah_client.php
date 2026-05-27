<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama_lengkap']);
    $username = $conn->real_escape_string($_POST['username']);
    $password_raw = $_POST['password'];
    $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

    $cek = $conn->query("SELECT * FROM users WHERE username = '$username'");
    
    if ($cek->num_rows > 0) {
        $pesan = '<div class="alert alert-danger">Username sudah digunakan!</div>';
    } else {
        $insert = $conn->query("INSERT INTO users (nama_lengkap, username, password, role) VALUES ('$nama', '$username', '$password_hash', 'client')");
        if ($insert) {
            $pesan = "<div class='alert alert-success'>Berhasil! Berikan akses ini ke client: <br><b>Username:</b> $username <br><b>Password:</b> $password_raw</div>";
        } else {
            $pesan = '<div class="alert alert-danger">Gagal mendaftar!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Client - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Montserrat', sans-serif; background-color: #F8F9FA; }</style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Buat Akun Client</h4>
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill">Kembali</a>
            </div>

            <?= $pesan; ?>

            <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap Client</label>
                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Romeo & Juliet">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username Login</label>
                        <input type="text" name="username" class="form-control" required placeholder="Contoh: romeo2026">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password Sementara</label>
                        <input type="text" name="password" class="form-control" required placeholder="Contoh: romjul123">
                        <small class="text-muted">Password ini akan diberikan ke client agar mereka bisa login.</small>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill">Daftarkan Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>