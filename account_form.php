<?php
// account_form.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$editMode = false;
$name = '';
$initial_balance = '';
$currency = 'IDR';

// Jika ada edit (via param ?id=)
if (isset($_GET['id'])) {
    $editMode = true;
    $id = (int) $_GET['id'];
    // Ambil data akun
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE id = :id AND user_id = :u LIMIT 1");
    $stmt->execute(['id' => $id, 'u' => $uid]);
    $acc = $stmt->fetch();
    if (!$acc) {
        // Jika akun tidak ditemukan atau bukan milik user, redirect ke daftar
        header('Location: accounts_list.php');
        exit;
    }
    $name = $acc['name'];
    $initial_balance = $acc['initial_balance'];
    $currency = $acc['currency'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data POST
    $name = trim($_POST['name'] ?? '');
    $initial_balance = trim($_POST['initial_balance'] ?? '');
    $currency = $_POST['currency'] ?? '';

    // Validasi server
    if (empty($name) || $initial_balance === '' || empty($currency)) {
        $error = "Semua field wajib diisi.";
    }
    elseif (!is_numeric($initial_balance) || $initial_balance < 0) {
        $error = "Saldo awal harus angka ≥ 0.";
    }
    else {
        if (isset($_POST['id'])) {
            // Update
            $id = (int) $_POST['id'];
            $stmt = $pdo->prepare("
              UPDATE accounts
              SET name = :n, initial_balance = :b, currency = :c
              WHERE id = :id AND user_id = :u
            ");
            $stmt->execute([
              'n'   => $name,
              'b'   => $initial_balance,
              'c'   => $currency,
              'id'  => $id,
              'u'   => $uid
            ]);
        } else {
            // Insert baru
            $stmt = $pdo->prepare("
              INSERT INTO accounts (user_id, name, initial_balance, currency)
              VALUES (:u, :n, :b, :c)
            ");
            $stmt->execute([
              'u' => $uid,
              'n' => $name,
              'b' => $initial_balance,
              'c' => $currency
            ]);
        }
        header('Location: accounts_list.php');
        exit;
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4><?= $editMode ? 'Edit Akun' : 'Form Akun Baru' ?></h4>
      <hr />
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form id="accountForm" action="" method="post" novalidate>
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>
        <div class="mb-3">
          <label for="name" class="form-label">Nama Akun</label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-control"
            placeholder="Misal: Kas Harian"
            value="<?= htmlspecialchars($name) ?>"
            required
          />
          <div class="invalid-feedback">
            Nama akun wajib diisi.
          </div>
        </div>
        <div class="mb-3">
          <label for="initial_balance" class="form-label">Saldo Awal</label>
          <input
            type="number"
            id="initial_balance"
            name="initial_balance"
            class="form-control"
            placeholder="Masukkan saldo awal"
            step="0.01"
            value="<?= htmlspecialchars($initial_balance) ?>"
            required
            min="0"
          />
          <div class="invalid-feedback">
            Saldo awal wajib diisi (angka ≥ 0).
          </div>
        </div>
        <div class="mb-3">
          <label for="currency" class="form-label">Mata Uang</label>
          <select id="currency" name="currency" class="form-select" required>
            <option value="IDR" <?= ($currency === 'IDR') ? 'selected' : '' ?>>IDR</option>
            <option value="USD" <?= ($currency === 'USD') ? 'selected' : '' ?>>USD</option>
            <option value="EUR" <?= ($currency === 'EUR') ? 'selected' : '' ?>>EUR</option>
          </select>
          <div class="invalid-feedback">
            Mata uang wajib dipilih.
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Akun</button>
        <a href="accounts_list.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
