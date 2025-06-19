<?php
// change_password.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($old_password) || empty($new_password) || empty($confirm)) {
        $error = "Semua field wajib diisi.";
    }
    elseif (strlen($new_password) < 8) {
        $error = "Password baru minimal 8 karakter.";
    }
    elseif ($new_password !== $confirm) {
        $error = "Password baru dan konfirmasi tidak cocok.";
    }
    else {
        // Ambil hash password lama
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $uid]);
        $hash = $stmt->fetchColumn();
        if (!password_verify($old_password, $hash)) {
            $error = "Password lama salah.";
        } else {
            $newHash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = :p WHERE id = :id");
            $stmt->execute(['p' => $newHash, 'id' => $uid]);
            $success = "Password berhasil diubah.";
        }
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4>Ubah Password</h4>
      <hr />
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <form id="changePasswordForm" action="" method="post" novalidate>
        <div class="mb-3">
          <label for="old_password" class="form-label">Password Lama</label>
          <input
            type="password"
            id="old_password"
            name="old_password"
            class="form-control"
            required
            minlength="8"
          />
          <div class="invalid-feedback">
            Password lama wajib diisi.
          </div>
        </div>
        <div class="mb-3">
          <label for="new_password" class="form-label">Password Baru</label>
          <input
            type="password"
            id="new_password"
            name="new_password"
            class="form-control"
            placeholder="Minimal 8 karakter"
            required
            minlength="8"
          />
          <div class="invalid-feedback">
            Password baru wajib diisi (minimal 8 karakter).
          </div>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
          <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            class="form-control"
            required
            minlength="8"
          />
          <div class="invalid-feedback">
            Konfirmasi password wajib diisi dan sama.
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Ubah Password</button>
        <a href="profile.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
