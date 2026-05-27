<?php
// api/submit_rsvp.php
require_once '../config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $undangan_id = (int)$_POST['undangan_id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $kehadiran = $conn->real_escape_string($_POST['kehadiran']);
    $jumlah = empty($_POST['jumlah']) ? 1 : (int)$_POST['jumlah'];
    $ucapan = $conn->real_escape_string($_POST['ucapan']);

    $sql = "INSERT INTO rsvp (undangan_id, nama_tamu, kehadiran, jumlah_tamu, ucapan) 
            VALUES ('$undangan_id', '$nama', '$kehadiran', '$jumlah', '$ucapan')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Terima kasih atas ucapan dan konfirmasinya!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $conn->error]);
    }
}
?>