<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, cek role-nya lalu arahkan ke dashboard yang tepat
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../client/index.php");
    }
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $cek = $conn->query("SELECT * FROM users WHERE username = '$username'");
    
    if ($cek->num_rows > 0) {
        $user = $cek->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role']; // Simpan role ke session
            
            // Pengecekan Role (Pembagi Jalur Admin & Client)
            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../client/index.php");
            }
            exit;
        } else {
            $pesan = '<div class="alert alert-danger shadow-sm border-0" style="border-radius: 10px;">Password salah!</div>';
        }
    } else {
        $pesan = '<div class="alert alert-danger shadow-sm border-0" style="border-radius: 10px;">Username tidak ditemukan!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Premium Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(rgba(44, 42, 41, 0.4), rgba(44, 42, 41, 0.4)), url('https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=2000&auto=format&fit=crop') center/cover fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .font-serif { font-family: 'Cormorant Garamond', serif; }
        .glass-card {
            background: rgba(253, 251, 247, 0.85);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .btn-gold {
            background-color: #C5A880;
            color: white;
            border-radius: 30px;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 1px;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-gold:hover { background-color: #9B825E; color: white; transform: translateY(-2px); }
        .form-floating input:focus { border-color: #C5A880; box-shadow: 0 0 0 0.25rem rgba(197, 168, 128, 0.25); }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="text-center mb-4">
        <h2 class="font-serif" style="color: #9B825E;">MyInvitation</h2>
        <p class="text-muted" style="font-size: 0.9rem;">Masuk ke dashboard panel Anda</p>
    </div>
    
    <?= $pesan; ?>

    <form action="" method="POST">
        <div class="form-floating mb-3">
            <input type="text" name="username" class="form-control" id="floatingUsername" placeholder="Username" required style="border-radius: 10px;">
            <label for="floatingUsername">Username</label>
        </div>
        <div class="form-floating mb-4">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required style="border-radius: 10px;">
            <label for="floatingPassword">Password</label>
        </div>
        <button type="submit" class="btn btn-gold w-100 mb-3">Login Securely</button>
    </form>
    
    <div class="text-center mt-3">
        <small class="text-muted">Tertarik membuat undangan? <a href="register.php" class="text-decoration-none fw-bold" style="color: #C5A880;">Pesan Disini</a></small>
    </div>
</div>

</body>
</html>