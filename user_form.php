<?php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard_user.php');
    exit;
}

require_once 'includes/sidebar.php';

$error = '';
$editMode = false;
$full_name = '';
$username = '';
$email = '';
$role = 'user';

if (isset($_GET['id'])) {
    $editMode = true;
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $u = $stmt->fetch();
    if (!$u) {
        header('Location: user_management.php');
        exit;
    }
    $full_name = $u['full_name'];
    $username  = $u['username'];
    $email     = $u['email'];
    $role      = $u['role'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    if (empty($full_name) || empty($username) || empty($email) || empty($role)) {
        $error = "Field bertanda * wajib diisi.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }
    elseif (!preg_match('/^[A-Za-z0-9_]{4,20}$/', $username)) {
        $error = "Username 4–20 karakter ( huruf/angka/underscore ).";
    }
    else {
        if ($editMode) {
            $id = (int) $_POST['id'];
            if ($password !== '' || $confirm !== '') {
                if ($password !== $confirm) {
                    $error = "Password dan konfirmasi tidak cocok.";
                }
                elseif (strlen($password) < 8) {
                    $error = "Password minimal 8 karakter.";
                }
            }
            if (!$error) {
                $stmt = $pdo->prepare("
                  SELECT id FROM users 
                  WHERE (username = :u OR email = :e) AND id != :id LIMIT 1
                ");
                $stmt->execute(['u' => $username, 'e' => $email, 'id' => $id]);
                if ($stmt->fetch()) {
                    $error = "Username atau email sudah terpakai.";
                }
            }
            if (!$error) {
                if ($password !== '' && $confirm !== '') {
                    $passHash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("
                      UPDATE users
                      SET full_name = :fn, username = :u, email = :e, role = :r, password_hash = :p
                      WHERE id = :id
                    ");
                    $stmt->execute([
                      'fn' => $full_name,
                      'u'  => $username,
                      'e'  => $email,
                      'r'  => $role,
                      'p'  => $passHash,
                      'id' => $id
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                      UPDATE users
                      SET full_name = :fn, username = :u, email = :e, role = :r
                      WHERE id = :id
                    ");
                    $stmt->execute([
                      'fn' => $full_name,
                      'u'  => $username,
                      'e'  => $email,
                      'r'  => $role,
                      'id' => $id
                    ]);
                }
                header('Location: user_management.php');
                exit;
            }
        } else {
            if (empty($password) || empty($confirm)) {
                $error = "Password dan konfirmasi wajib diisi.";
            }
            elseif ($password !== $confirm) {
                $error = "Password dan konfirmasi tidak cocok.";
            }
            elseif (strlen($password) < 8) {
                $error = "Password minimal 8 karakter.";
            }
            if (!$error) {
                $stmt = $pdo->prepare("
                  SELECT id FROM users
                  WHERE username = :u OR email = :e LIMIT 1
                ");
                $stmt->execute(['u' => $username, 'e' => $email]);
                if ($stmt->fetch()) {
                    $error = "Username atau email sudah terpakai.";
                }
            }
            if (!$error) {
                $passHash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("
                  INSERT INTO users (username, password_hash, full_name, email, role)
                  VALUES (:u, :p, :fn, :e, :r)
                ");
                $stmt->execute([
                  'u'  => $username,
                  'p'  => $passHash,
                  'fn' => $full_name,
                  'e'  => $email,
                  'r'  => $role
                ]);
                header('Location: user_management.php');
                exit;
            }
        }
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4><?= $editMode ? 'Edit User' : 'Tambah User Baru' ?></h4>
      <hr>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form id="userForm" action="" method="post" novalidate>
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>
        <div class="mb-3">
          <label for="full_name" class="form-label">Nama Lengkap</label>
          <input
            type="text"
            id="full_name"
            name="full_name"
            class="form-control"
            placeholder="Masukkan nama lengkap"
            value="<?= htmlspecialchars($full_name) ?>"
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
            value="<?= htmlspecialchars($username) ?>"
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
            value="<?= htmlspecialchars($email) ?>"
            required
          />
          <div class="invalid-feedback">
            Masukkan email yang valid.
          </div>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label"><?= $editMode ? 'Password Baru (opsional)' : 'Password' ?></label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            placeholder="<?= $editMode ? 'Biarkan kosong jika tidak diubah' : 'Minimal 8 karakter' ?>"
            <?= $editMode ? '' : 'required minlength="8"' ?>
          />
          <div class="invalid-feedback">
            <?= $editMode ? 'Minimal 8 karakter jika ganti password.' : 'Password wajib diisi (minimal 8 karakter).' ?>
          </div>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label"><?= $editMode ? 'Konfirmasi Password Baru' : 'Konfirmasi Password' ?></label>
          <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            class="form-control"
            placeholder="<?= $editMode ? 'Biarkan kosong jika tidak diubah' : 'Ketik ulang password' ?>"
            <?= $editMode ? '' : 'required minlength="8"' ?>
          />
          <div class="invalid-feedback">
            <?= $editMode ? 'Pastikan password baru sesuai.' : 'Konfirmasi password wajib diisi dan harus sama.' ?>
          </div>
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Role</label>
          <select id="role" name="role" class="form-select" required>
            <option value="user"  <?= $role === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
          <div class="invalid-feedback">
            Role wajib dipilih.
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="user_management.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
