<?php
$host = "sql310.infinityfree.com";
$user = "if0_43472546";
$pass = "ISI_PASSWORD_MYSQL";
$db   = "if0_43472546_Recyclepoint";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error() . ". Pastikan MySQL di XAMPP menyala dan database 'db_recyclepoint' sudah diimpor.");
}

mysqli_set_charset($conn, "utf8mb4");

function formatRupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>