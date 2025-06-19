<?php
// auth/register.php
require_once __DIR__ . '/../includes/koneksi.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header('Location: ../dashboard_admin.php');
    else header('Location: ../dashboard_user.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & trim data
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $role      = 'user'; // <<--- FIXED ROLE

    // Validasi sisi server
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Semua field wajib diisi.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }
    elseif (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
        $error = "Username 4–20 karakter (huruf/angka/underscore).";
    }
    elseif ($password !== $confirm) {
        $error = "Password dan Konfirmasi tidak cocok.";
    }
    elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";
    }
    else {
        // Cek duplikat username atau email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute(['u' => $username, 'e' => $email]);
        if ($stmt->fetch()) {
            $error = "Username atau email sudah terdaftar.";
        } else {
            // Insert user baru
            $passHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("
              INSERT INTO users (username, password_hash, full_name, email, role)
              VALUES (:u, :p, :f, :e, :r)
            ");
            $stmt->execute([
              'u' => $username,
              'p' => $passHash,
              'f' => $full_name,
              'e' => $email,
              'r' => $role
            ]);
            // Redirect ke login
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Finance App</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center vh-100">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0">Daftar Akun Baru</h5>
          </div>
          <div class="card-body">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form id="registerForm" action="" method="post" novalidate>
              <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input
                  type="text"
                  id="full_name"
                  name="full_name"
                  class="form-control"
                  placeholder="Masukkan nama lengkap"
                  required
                />
                <div class="invalid-feedback">
                  Nama lengkap wajib diisi.
                </div>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  class="form-control"
                  placeholder="Pilih username"
                  required
                  pattern="^[A-Za-z0-9_]{4,20}$"
                />
                <div class="invalid-feedback">
                  Username wajib diisi (4–20 karakter).
                </div>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  class="form-control"
                  placeholder="Masukkan email"
                  required
                />
                <div class="invalid-feedback">
                  Masukkan email yang valid.
                </div>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control"
                  placeholder="Minimal 8 karakter"
                  required
                  minlength="8"
                />
                <div class="invalid-feedback">
                  Password wajib diisi (minimal 8 karakter).
                </div>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input
                  type="password"
                  id="confirm_password"
                  name="confirm_password"
                  class="form-control"
                  placeholder="Ketik ulang password"
                  required
                  minlength="8"
                />
                <div class="invalid-feedback">
                  Konfirmasi password wajib diisi dan harus sesuai.
                </div>
              </div>

              <!-- HAPUS INPUT ROLE AGAR TIDAK BISA DIUBAH MANUAL -->
              <!-- <input type="hidden" name="role" value="user" /> -->

              <div class="d-flex justify-content-between align-items-center">
                <a href="login.php">Sudah punya akun? Masuk</a>
                <button type="submit" class="btn btn-success">Daftar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/scripts.js"></script>
</body>
</html>
