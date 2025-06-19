<?php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$editMode = false;

$type = '';
$account_id = '';
$category_id = '';
$transaction_date = date('Y-m-d');
$amount = '';
$description = '';

$stmt = $pdo->prepare("SELECT id, name FROM accounts WHERE user_id = :u");
$stmt->execute(['u' => $uid]);
$akunList = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT id, name, type FROM categories WHERE user_id = :u");
$stmt->execute(['u' => $uid]);
$catList = $stmt->fetchAll();

if (isset($_GET['id'])) {
    $editMode = true;
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id AND user_id = :u LIMIT 1");
    $stmt->execute(['id' => $id, 'u' => $uid]);
    $t = $stmt->fetch();
    if (!$t) {
        header('Location: transactions_list.php');
        exit;
    }
    $type = $t['type'];
    $account_id = $t['account_id'];
    $category_id = $t['category_id'];
    $transaction_date = $t['transaction_date'];
    $amount = $t['amount'];
    $description = $t['description'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $account_id = $_POST['account_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $transaction_date = $_POST['transaction_date'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (empty($type) || empty($account_id) || empty($category_id) || empty($transaction_date) || empty($amount)) {
        $error = "Semua field bertanda * wajib diisi.";
    }
    elseif (!in_array($type, ['income','expense'])) {
        $error = "Tipe transaksi tidak valid.";
    }
    elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $amount) || $amount <= 0) {
        $error = "Jumlah harus angka > 0 (boleh 2 desimal).";
    }
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transaction_date)) {
        $error = "Format tanggal tidak valid.";
    }
    else {
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $stmt = $pdo->prepare("
              UPDATE transactions
              SET type = :t, account_id = :a, category_id = :c, transaction_date = :d, amount = :amt, description = :desc
              WHERE id = :id AND user_id = :u
            ");
            $stmt->execute([
              't'    => $type,
              'a'    => $account_id,
              'c'    => $category_id,
              'd'    => $transaction_date,
              'amt'  => $amount,
              'desc' => $description,
              'id'   => $id,
              'u'    => $uid
            ]);
        } else {
            $stmt = $pdo->prepare("
              INSERT INTO transactions (user_id, account_id, category_id, type, transaction_date, amount, description)
              VALUES (:u, :a, :c, :t, :d, :amt, :desc)
            ");
            $stmt->execute([
              'u'    => $uid,
              'a'    => $account_id,
              'c'    => $category_id,
              't'    => $type,
              'd'    => $transaction_date,
              'amt'  => $amount,
              'desc' => $description
            ]);
        }
        header('Location: transactions_list.php');
        exit;
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4><?= $editMode ? 'Edit Transaksi' : 'Form Transaksi Baru' ?></h4>
      <hr />
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form id="transactionForm" action="" method="post" novalidate>
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>
        <div class="mb-3">
          <label for="type" class="form-label">Tipe Transaksi*</label>
          <select id="type" name="type" class="form-select" required>
            <option value="" disabled <?= $type === '' ? 'selected' : '' ?>>-- Pilih Tipe --</option>
            <option value="income"  <?= $type === 'income' ? 'selected' : '' ?>>Pemasukan</option>
            <option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
          </select>
          <div class="invalid-feedback">Tipe transaksi wajib dipilih.</div>
        </div>
        <div class="mb-3">
          <label for="account_id" class="form-label">Pilih Akun*</label>
          <select id="account_id" name="account_id" class="form-select" required>
            <option value="" disabled <?= $account_id === '' ? 'selected' : '' ?>>-- Pilih Akun --</option>
            <?php foreach ($akunList as $a): ?>
              <option value="<?= $a['id'] ?>" <?= ($account_id == $a['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Akun wajib dipilih.</div>
        </div>
        <div class="mb-3">
          <label for="category_id" class="form-label">Pilih Kategori*</label>
          <select id="category_id" name="category_id" class="form-select" required>
            <option value="" disabled <?= $category_id === '' ? 'selected' : '' ?>>-- Pilih Kategori --</option>
            <?php foreach ($catList as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?> (<?= $c['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Kategori wajib dipilih.</div>
        </div>
        <div class="mb-3">
          <label for="transaction_date" class="form-label">Tanggal Transaksi*</label>
          <input
            type="date"
            id="transaction_date"
            name="transaction_date"
            class="form-control"
            value="<?= htmlspecialchars($transaction_date) ?>"
            required
          />
          <div class="invalid-feedback">Tanggal transaksi wajib diisi.</div>
        </div>
        <div class="mb-3">
          <label for="amount" class="form-label">Jumlah (Rp)*</label>
          <input
            type="number"
            id="amount"
            name="amount"
            class="form-control"
            placeholder="Masukkan nominal"
            step="0.01"
            value="<?= htmlspecialchars($amount) ?>"
            required
            min="0.01"
          />
          <div class="invalid-feedback">Jumlah wajib diisi (angka > 0).</div>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Deskripsi (opsional)</label>
          <textarea
            id="description"
            name="description"
            class="form-control"
            rows="3"
            placeholder="Keterangan tambahan"
          ><?= htmlspecialchars($description) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        <a href="transactions_list.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
