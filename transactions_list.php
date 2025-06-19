<?php

require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];
$where = "WHERE t.user_id = :u";
$params = ['u' => $uid];
$start_date = $_GET['start_date'] ?? '';
$end_date   = $_GET['end_date']   ?? '';
$type       = $_GET['type']       ?? '';
$account_id = $_GET['account_id'] ?? '';
$search     = trim($_GET['search_deskripsi'] ?? '');

if ($start_date) {
    $where .= " AND t.transaction_date >= :sd";
    $params['sd'] = $start_date;
}
if ($end_date) {
    $where .= " AND t.transaction_date <= :ed";
    $params['ed'] = $end_date;
}
if ($type) {
    $where .= " AND t.type = :t";
    $params['t'] = $type;
}
if ($account_id) {
    $where .= " AND t.account_id = :a";
    $params['a'] = $account_id;
}
if ($search) {
    $where .= " AND t.description LIKE :s";
    $params['s'] = "%{$search}%";
}

$sql = "
  SELECT
    t.*,
    c.name AS cat_name,
    a.name AS acc_name
  FROM transactions AS t
  JOIN categories  AS c ON t.category_id = c.id
  JOIN accounts    AS a ON t.account_id = a.id
  $where
  ORDER BY t.transaction_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transaksi = $stmt->fetchAll();
$stmt2 = $pdo->prepare("SELECT id, name FROM accounts WHERE user_id = :u");
$stmt2->execute(['u' => $uid]);
$akunList = $stmt2->fetchAll();
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
      <h4>Daftar Transaksi</h4>
      <a href="transaction_form.php" class="btn btn-primary">Tambah Transaksi</a>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12">
      <form action="transactions_list.php" method="get" class="row gy-2 gx-3 align-items-end">
        <div class="col-md-2 col-sm-6">
          <label for="start_date" class="form-label">Dari Tanggal</label>
          <input
            type="date"
            id="start_date"
            name="start_date"
            class="form-control"
            value="<?= htmlspecialchars($start_date) ?>"
          />
        </div>
        <div class="col-md-2 col-sm-6">
          <label for="end_date" class="form-label">Sampai Tanggal</label>
          <input
            type="date"
            id="end_date"
            name="end_date"
            class="form-control"
            value="<?= htmlspecialchars($end_date) ?>"
          />
        </div>
        <div class="col-md-2 col-sm-6">
          <label for="type" class="form-label">Tipe</label>
          <select id="type" name="type" class="form-select">
            <option value="">-- Semua --</option>
            <option value="income"  <?= $type === 'income'  ? 'selected' : '' ?>>Pemasukan</option>
            <option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>Pengeluaran</option>
          </select>
        </div>
        <div class="col-md-2 col-sm-6">
          <label for="account_id" class="form-label">Akun</label>
          <select id="account_id" name="account_id" class="form-select">
            <option value="">-- Semua --</option>
            <?php foreach ($akunList as $a): ?>
              <option value="<?= $a['id'] ?>" <?= ($account_id == $a['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 col-sm-6">
          <label for="search_deskripsi" class="form-label">Cari Deskripsi</label>
          <input
            type="text"
            id="search_deskripsi"
            name="search_deskripsi"
            class="form-control"
            placeholder="Ketik deskripsi..."
            value="<?= htmlspecialchars($search) ?>"
          />
        </div>
        <div class="col-md-2 col-sm-6 d-grid">
          <button type="submit" class="btn btn-outline-secondary mt-4">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table id="transactionsTable" class="table table-hover m-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Akun</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php foreach ($transaksi as $t): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= date('d M Y', strtotime($t['transaction_date'])) ?></td>
                  <td class="td-deskripsi"><?= htmlspecialchars($t['description'] ?: '-') ?></td>
                  <td><?= htmlspecialchars($t['cat_name']) ?></td>
                  <td><?= htmlspecialchars($t['acc_name']) ?></td>
                  <td>
                    <?php if ($t['type'] === 'income'): ?>
                      <span class="badge bg-success">Pemasukan</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Pengeluaran</span>
                    <?php endif; ?>
                  </td>
                  <td>Rp <?= number_format($t['amount'], 0, ',', '.') ?></td>
                  <td>
                    <a href="transaction_form.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="transaction_process.php?delete_id=<?= $t['id'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin ingin menghapus transaksi ini?');">
                      Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>

              <?php if (empty($transaksi)): ?>
                <tr><td colspan="8" class="text-center">Belum ada transaksi.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
require_once 'includes/footer.php';
?>

