<?php
// auth/login.php
require_once __DIR__ . '/../includes/koneksi.php';

$error = '';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') header('Location: ../dashboard_admin.php');
    else header('Location: ../dashboard_user.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        // Perhatikan: kita pakai dua placeholder (:u dan :e)
        $stmt = $pdo->prepare("
            SELECT id, username, password_hash, full_name, role
            FROM users
            WHERE username = :u OR email = :e
            LIMIT 1
        ");
        // Bind :u dan :e ke nilai $username
        $stmt->execute([
            'u' => $username,
            'e' => $username
        ]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Berhasil login
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            if ($user['role'] === 'admin') {
                header('Location: ../dashboard_admin.php');
            } else {
                header('Location: ../dashboard_user.php');
            }
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Finance App</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center vh-100">
      <div class="col-md-5">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Login</h5>
          </div>
          <div class="card-body">
            <?php if ($error): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form id="loginForm" action="" method="post" novalidate>
              <div class="mb-3">
                <label for="username" class="form-label">Username atau Email</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  class="form-control"
                  placeholder="Masukkan username atau email"
                  required
                />
                <div class="invalid-feedback">
                  Username atau Email wajib diisi.
                </div>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  class="form-control"
                  placeholder="Masukkan password"
                  required
                  minlength="8"
                />
                <div class="invalid-feedback">
                  Password wajib diisi (minimal 8 karakter).
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <a href="register.php">Belum punya akun? Daftar</a>
                <button type="submit" class="btn btn-primary">Masuk</button>
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
