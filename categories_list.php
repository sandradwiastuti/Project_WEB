<?php
// categories_list.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];

// Ambil kategori user terpisah berdasarkan type
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = :u AND type = 'income' ORDER BY id DESC");
$stmt->execute(['u' => $uid]);
$catsIncome = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = :u AND type = 'expense' ORDER BY id DESC");
$stmt->execute(['u' => $uid]);
$catsExpense = $stmt->fetchAll();
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
      <h4>Daftar Kategori</h4>
      <a href="category_form.php" class="btn btn-primary">Tambah Kategori Baru</a>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <ul class="nav nav-tabs" id="tabKategori" role="tablist">
        <li class="nav-item" role="presentation">
          <button
            class="nav-link active"
            id="income-tab"
            data-bs-toggle="tab"
            data-bs-target="#income"
            type="button"
            role="tab"
            aria-controls="income"
            aria-selected="true"
          >
            Pemasukan
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            id="expense-tab"
            data-bs-toggle="tab"
            data-bs-target="#expense"
            type="button"
            role="tab"
            aria-controls="expense"
            aria-selected="false"
          >
            Pengeluaran
          </button>
        </li>
      </ul>
      <div class="tab-content mt-2" id="tabKategoriContent">
        <!-- Tab Income -->
        <div
          class="tab-pane fade show active"
          id="income"
          role="tabpanel"
          aria-labelledby="income-tab"
        >
          <div class="card shadow-sm">
            <div class="card-body p-0">
              <table class="table table-hover m-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; ?>
                  <?php foreach ($catsIncome as $c): ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= htmlspecialchars($c['name']) ?></td>
                      <td>
                        <a href="category_form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="category_process.php?delete_id=<?= $c['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Yakin ingin menghapus kategori ini?');">
                          Hapus
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($catsIncome)): ?>
                    <tr><td colspan="3" class="text-center">Belum ada kategori pemasukan.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tab Expense -->
        <div
          class="tab-pane fade"
          id="expense"
          role="tabpanel"
          aria-labelledby="expense-tab"
        >
          <div class="card shadow-sm">
            <div class="card-body p-0">
              <table class="table table-hover m-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i = 1; ?>
                  <?php foreach ($catsExpense as $c): ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= htmlspecialchars($c['name']) ?></td>
                      <td>
                        <a href="category_form.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="category_process.php?delete_id=<?= $c['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Yakin ingin menghapus kategori ini?');">
                          Hapus
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                  <?php if (empty($catsExpense)): ?>
                    <tr><td colspan="3" class="text-center">Belum ada kategori pengeluaran.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
