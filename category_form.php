<?php
// category_form.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$error = '';
$editMode = false;
$name = '';
$type = '';

if (isset($_GET['id'])) {
    $editMode = true;
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id AND user_id = :u LIMIT 1");
    $stmt->execute(['id' => $id, 'u' => $uid]);
    $cat = $stmt->fetch();
    if (!$cat) {
        header('Location: categories_list.php');
        exit;
    }
    $name = $cat['name'];
    $type = $cat['type'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';

    if (empty($name) || empty($type)) {
        $error = "Semua field wajib diisi.";
    }
    else {
        if (isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            $stmt = $pdo->prepare("
              UPDATE categories
              SET name = :n, type = :t
              WHERE id = :id AND user_id = :u
            ");
            $stmt->execute(['n' => $name, 't' => $type, 'id' => $id, 'u' => $uid]);
        } else {
            // Cek duplikat nama+type
            $stmt = $pdo->prepare("
              SELECT id FROM categories
              WHERE user_id = :u AND name = :n AND type = :t
              LIMIT 1
            ");
            $stmt->execute(['u' => $uid, 'n' => $name, 't' => $type]);
            if ($stmt->fetch()) {
                $error = "Kategori untuk tipe tersebut sudah ada.";
            } else {
                $stmt = $pdo->prepare("
                  INSERT INTO categories (user_id, name, type)
                  VALUES (:u, :n, :t)
                ");
                $stmt->execute(['u' => $uid, 'n' => $name, 't' => $type]);
            }
        }
        if (!$error) {
            header('Location: categories_list.php');
            exit;
        }
    }
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col">
      <h4><?= $editMode ? 'Edit Kategori' : 'Form Kategori Baru' ?></h4>
      <hr />
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form id="categoryForm" action="" method="post" novalidate>
        <?php if ($editMode): ?>
          <input type="hidden" name="id" value="<?= $id ?>" />
        <?php endif; ?>
        <div class="mb-3">
          <label for="name" class="form-label">Nama Kategori</label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-control"
            placeholder="Misal: Makanan"
            value="<?= htmlspecialchars($name) ?>"
            required
          />
          <div class="invalid-feedback">Nama kategori wajib diisi.</div>
        </div>
        <div class="mb-3">
          <label for="type" class="form-label">Tipe</label>
          <select id="type" name="type" class="form-select" required>
            <option value="" disabled <?= $type === '' ? 'selected' : '' ?>>-- Pilih Tipe --</option>
            <option value="income"  <?= $type === 'income' ? 'selected' : '' ?>>Pemasukan</option>
            <option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
          </select>
          <div class="invalid-feedback">Tipe kategori wajib dipilih.</div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
        <a href="categories_list.php" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
