<?php
$host = "sql208.infinityfree.com";
$user = "if0_41792578";
$pass = "awal220102";
$db   = "if0_41792578_myinvitation";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>