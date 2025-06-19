<?php
// budgets_list.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];

// Ambil semua budget user
$stmt = $pdo->prepare("
  SELECT b.id, b.month, b.amount_limit, c.name AS cat_name,
    COALESCE(SUM(
      CASE WHEN t.type='expense' AND DATE_FORMAT(t.transaction_date, '%Y-%m') = b.month THEN t.amount ELSE 0 END
    ), 0) AS used_amount
  FROM budgets b
  JOIN categories c ON b.category_id = c.id
  LEFT JOIN transactions t ON b.category_id = t.category_id AND t.user_id = b.user_id
  WHERE b.user_id = :u
  GROUP BY b.id
  ORDER BY b.month DESC
");
$stmt->execute(['u' => $uid]);
$budgets = $stmt->fetchAll();
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
      <h4>Daftar Anggaran Bulanan</h4>
      <a href="budget_form.php" class="btn btn-primary">Tambah Anggaran</a>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table table-hover m-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Bulan</th>
                <th>Kategori</th>
                <th>Batas Anggaran</th>
                <th>Terpakai</th>
                <th>Sisa</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php foreach ($budgets as $b): 
                $sisa = $b['amount_limit'] - $b['used_amount'];
                if ($b['used_amount'] >= $b['amount_limit']) $status = 'bg-danger';
                elseif ($b['used_amount'] >= 0.8 * $b['amount_limit']) $status = 'bg-warning text-dark';
                else $status = 'bg-success';
              ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= date('M Y', strtotime($b['month'] . '-01')) ?></td>
                  <td><?= htmlspecialchars($b['cat_name']) ?></td>
                  <td>Rp <?= number_format($b['amount_limit'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($b['used_amount'], 0, ',', '.') ?></td>
                  <td>Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                  <td>
                    <span class="badge <?= $status ?>">
                      <?php
                        if ($b['used_amount'] >= $b['amount_limit']) echo 'Terlampaui';
                        elseif ($b['used_amount'] >= 0.8 * $b['amount_limit']) echo 'Mendekati';
                        else echo 'Aman';
                      ?>
                    </span>
                  </td>
                  <td>
                    <a href="budget_form.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="budget_process.php?delete_id=<?= $b['id'] ?>"
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('Yakin ingin menghapus anggaran ini?');">
                      Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($budgets)): ?>
                <tr><td colspan="8" class="text-center">Belum ada anggaran.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
