<?php
// profile.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT full_name, email, username FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $uid]);
$user = $stmt->fetch();
$full_name = $user['full_name'];
$email     = $user['email'];
$username  = $user['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');

    if (empty($full_name) || empty($email)) {
        $error = "Nama lengkap dan email wajib diisi.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }
    else {
        // Cek duplikat email kecuali diri sendiri
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :e AND id != :id LIMIT 1");
        $stmt->execute(['e' => $email, 'id' => $uid]);
        if ($stmt->fetch()) {
            $error = "Email sudah terpakai oleh user lain.";
        } else {
            $stmt = $pdo->prepare("
              UPDATE users
              SET full_name = :fn, email = :e
              WHERE id = :id
            ");
            $stmt->execute(['fn' => $full_name, 'e' => $email, 'id' => $uid]);
            $success = "Profil berhasil diperbarui.";
            $_SESSION['full_name'] = $full_name;
        }
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4>Profil Saya</h4>
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
      <form id="profileForm" action="" method="post" novalidate>
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input
            type="text"
            id="full_name"
            name="full_name"
            class="form-control"
            value="<?= htmlspecialchars($full_name) ?>"
            required
          />
          <div class="invalid-feedback">
            Nama lengkap wajib diisi.
          </div>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="<?= htmlspecialchars($email) ?>"
            required
          />
          <div class="invalid-feedback">
            Masukkan email yang valid.
          </div>
        </div>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input
            type="text"
            id="username"
            class="form-control"
            value="<?= htmlspecialchars($username) ?>"
            disabled
          />
        </div>
        <div class="mb-3">
          <a href="change_password.php" class="btn btn-link ps-0">Ubah Password</a>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
