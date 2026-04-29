<?php
// ============================================================
// Konfigurasi Database
// Sesuaikan dengan setting MySQL kamu
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai user MySQL kamu
define('DB_PASS', '');           // ganti sesuai password MySQL kamu
define('DB_NAME', 'sqli_demo');

function getConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}
