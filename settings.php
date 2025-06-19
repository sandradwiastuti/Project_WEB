<?php
// settings.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$success = '';

// Misalnya kita menyimpan pengaturan di tabel terpisah (misalnya table settings) –
// Namun, untuk contoh ini, kita simulasikan dengan SESSION saja.
// Tentu di implementasi nyata sebaiknya disimpan di DB.

// Default nilai
$theme = $_SESSION['settings']['theme'] ?? 'light';
$notif_email = $_SESSION['settings']['notif_email'] ?? 1; // 1=on,0=off
$currency_default = $_SESSION['settings']['currency_default'] ?? 'IDR';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'] ?? 'light';
    $notif_email = isset($_POST['notif_email']) ? 1 : 0;
    $currency_default = $_POST['currency_default'] ?? 'IDR';

    // Validasi: hanya boleh 'light' atau 'dark' untuk theme
    if (!in_array($theme, ['light','dark'])) {
        $error = "Tema tidak valid.";
    }
    elseif (!in_array($currency_default, ['IDR','USD','EUR'])) {
        $error = "Mata uang default tidak valid.";
    }
    else {
        // Simpan di SESSION (contoh)
        $_SESSION['settings'] = [
            'theme'            => $theme,
            'notif_email'      => $notif_email,
            'currency_default' => $currency_default
        ];
        $success = "Pengaturan berhasil disimpan.";
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4>Pengaturan Akun & Aplikasi</h4>
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
      <form id="settingsForm" action="" method="post" novalidate>
        <div class="mb-3">
          <label for="theme" class="form-label">Tema Tampilan</label>
          <select id="theme" name="theme" class="form-select">
            <option value="light"  <?= $theme === 'light' ? 'selected' : '' ?>>Light</option>
            <option value="dark"   <?= $theme === 'dark'  ? 'selected' : '' ?>>Dark</option>
          </select>
        </div>
        <div class="mb-3 form-check form-switch">
          <input
            class="form-check-input"
            type="checkbox"
            id="notif_email"
            name="notif_email"
            <?= $notif_email ? 'checked' : '' ?>
          />
          <label class="form-check-label" for="notif_email">Notifikasi via Email</label>
        </div>
        <div class="mb-3">
          <label for="currency_default" class="form-label">Mata Uang Default</label>
          <select id="currency_default" name="currency_default" class="form-select" required>
            <option value="IDR" <?= $currency_default === 'IDR'? 'selected' : '' ?>>IDR</option>
            <option value="USD" <?= $currency_default === 'USD'? 'selected' : '' ?>>USD</option>
            <option value="EUR" <?= $currency_default === 'EUR'? 'selected' : '' ?>>EUR</option>
          </select>
          <div class="invalid-feedback">Mata uang default wajib dipilih.</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
