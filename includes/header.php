<?php
// includes/header.php
// Pastikan sudah include koneksi.php sebelum memanggil header.php

// Jika halaman butuh autentikasi, cek session:
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Ambil data user dari session:
$user_id   = $_SESSION['user_id'];
$username  = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$role      = $_SESSION['role']; // 'admin' atau 'user'
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Finance App</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
  <!-- Navbar global -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        FinanceApp
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarMenu"
        aria-controls="navbarMenu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              id="userDropdown"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              Hi, <?= htmlspecialchars($full_name) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="profile.php">Profil Saya</a></li>
              <li><a class="dropdown-item" href="settings.php">Pengaturan</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container-fluid">
    <div class="row">
