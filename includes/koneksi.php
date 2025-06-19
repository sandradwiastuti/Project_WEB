<?php
// includes/koneksi.php

// Mulai session di semua halaman yang memerlukan autentikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi koneksi database
$host = 'localhost';
$db   = 'finance_app';
$user = 'root';      // ganti username MySQL Anda
$pass = '';          // ganti password MySQL Anda (jika ada)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Error dalam bentuk Exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch dalam bentuk associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Gunakan prepared statements asli
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, tampilkan error halaman 500
    header("Location: /500.php");
    exit;
}
