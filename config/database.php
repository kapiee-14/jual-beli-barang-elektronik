<?php
$host = "localhost";
$db   = "electrostore";
$user = "root";
$pass = ""; // Default XAMPP. Isi jika MySQL Anda memakai password.
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi database gagal. Pastikan MySQL aktif dan database electrostore sudah dibuat. Detail: " . htmlspecialchars($e->getMessage()));
}
