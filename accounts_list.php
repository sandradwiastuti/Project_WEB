<?php
// accounts_list.php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$uid = $_SESSION['user_id'];

// Ambil semua akun user
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = :u ORDER BY id DESC");
$stmt->execute(['u' => $uid]);
$accounts = $stmt->fetchAll();
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
      <h4>Daftar Akun Keuangan</h4>
      <a href="account_form.php" class="btn btn-primary">Tambah Akun Baru</a>
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
                <th>Nama Akun</th>
                <th>Saldo Awal</th>
                <th>Mata Uang</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php foreach ($accounts as $acc): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($acc['name']) ?></td>
                  <td>Rp <?= number_format($acc['initial_balance'], 0, ',', '.') ?></td>
                  <td><?= htmlspecialchars($acc['currency']) ?></td>
                  <td>
                    <a href="account_form.php?id=<?= $acc['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <!-- Tombol Hapus: arahkan ke proses, nanti di-process dieksekusi di account_process.php -->
                    <a href="account_process.php?delete_id=<?= $acc['id'] ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Yakin ingin menghapus akun ini?');">
                      Hapus
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($accounts)): ?>
                <tr><td colspan="5" class="text-center">Belum ada akun. Tambahkan akun baru.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>
