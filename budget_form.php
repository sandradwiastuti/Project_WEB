<?php
// budget_form.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$editMode = false;

// Default form values
$category_id = '';
$month = date('Y-m');
$amount_limit = '';

$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE user_id = :u AND type = 'expense'");
$stmt->execute(['u' => $uid]);
$catExpense = $stmt->fetchAll();

if (isset($_GET['id'])) {
    $editMode = true;
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM budgets WHERE id = :id AND user_id = :u LIMIT 1");
    $stmt->execute(['id' => $id, 'u' => $uid]);
    $b = $stmt->fetch();
    if (!$b) {
        header('Location: budgets_list.php');
        exit;
    }
    $category_id = $b['category_id'];
    $month = $b['month'];
    $amount_limit = $b['amount_limit'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';
    $month = $_POST['month'] ?? '';
    $amount_limit = $_POST['amount_limit'] ?? '';

    if (empty($category_id) || empty($month) || empty($amount_limit)) {
        $error = "Semua field bertanda * wajib diisi.";
    }
    elseif (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        $error = "Format bulan tidak valid.";
    }
    elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $amount_limit) || $amount_limit <= 0) {
        $error = "Batas anggaran harus angka > 0.";
    }
    else {
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $stmt = $pdo->prepare("
              UPDATE budgets
              SET category_id = :c, month = :m, amount_limit = :l
              WHERE id = :id AND user_id = :u
            ");
            $stmt->execute([
              'c'   => $category_id,
              'm'   => $month,
              'l'   => $amount_limit,
              'id'  => $id,
              'u'   => $uid
            ]);
        } else {
            // Cek duplikat anggaran untuk kategori+bulan
            $stmt = $pdo->prepare("
              SELECT id FROM budgets
              WHERE user_id = :u AND category_id = :c AND month = :m
              LIMIT 1
            ");
            $stmt->execute(['u' => $uid, 'c' => $category_id, 'm' => $month]);
            if ($stmt->fetch()) {
                $error = "Anggaran untuk kategori & bulan tersebut sudah ada.";
            } else {
                $stmt = $pdo->prepare("
                  INSERT INTO budgets (user_id, category_id, month, amount_limit)
                  VALUES (:u, :c, :m, :l)
                ");
                $stmt->execute([
                  'u' => $uid,
                  'c' => $category_id,
                  'm' => $month,
                  'l' => $amount_limit
                ]);
            }
        }
        if (!$error) {
            header('Location: budgets_list.php');
            exit;
        }
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4><?= $editMode ? 'Edit Anggaran' : 'Form Anggaran Baru' ?></h4>
      <hr />
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form id="budgetForm" action="" method="post" novalidate>
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>
        <div class="mb-3">
          <label for="category_id" class="form-label">Kategori (Pengeluaran)*</label>
          <select id="category_id" name="category_id" class="form-select" required>
            <option value="" disabled <?= $category_id === '' ? 'selected' : '' ?>>-- Pilih Kategori --</option>
            <?php foreach ($catExpense as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($category_id == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Kategori wajib dipilih.</div>
        </div>
        <div class="mb-3">
          <label for="month" class="form-label">Bulan*</label>
          <input
            type="month"
            id="month"
            name="month"
            class="form-control"
            value="<?= htmlspecialchars($month) ?>"
            required
          />
          <div class="invalid-feedback">Bulan wajib diisi.</div>
        </div>
        <div class="mb-3">
          <label for="amount_limit" class="form-label">Batas Anggaran*</label>
          <input
            type="number"
            id="amount_limit"
            name="amount_limit"
            class="form-control"
            placeholder="Masukkan batas anggaran"
            step="0.01"
            value="<?= htmlspecialchars($amount_limit) ?>"
            required
            min="0.01"
          />
          <div class="invalid-feedback">Batas anggaran wajib diisi (angka > 0).</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Anggaran</button>
        <a href="budgets_list.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
