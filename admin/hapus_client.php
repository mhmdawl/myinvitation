<?php
session_start();
require_once '../config/koneksi.php';

// Proteksi: Hanya admin yang boleh mengeksekusi file ini
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek apakah ada ID yang dikirim
if (isset($_GET['id'])) {
    $id_client = (int)$_GET['id'];
    
    // Hapus data dari tabel users (Pastikan hanya role 'client' yang bisa dihapus)
    // Berkat relasi database ON DELETE CASCADE, data di tabel detail_undangan & rsvp otomatis ikut terhapus.
    $hapus = $conn->query("DELETE FROM users WHERE id = '$id_client' AND role = 'client'");
    
    if ($hapus) {
        $_SESSION['pesan_sukses'] = "Client beserta seluruh data undangannya berhasil dihapus secara permanen.";
    } else {
        $_SESSION['pesan_error'] = "Gagal menghapus client: " . $conn->error;
    }
}

// Kembalikan ke halaman index admin
header("Location: index.php");
exit;
?>