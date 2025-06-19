<?php
// File: dashboard_user.php

// 1) Aktifkan error reporting (sementara, untuk debugging)
//    Setelah chart muncul dengan baik, Anda bisa menonaktifkan baris‐baris ini.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Inklusi koneksi & layout
require_once 'includes/koneksi.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// 3) Ambil user_id dari session
$uid = $_SESSION['user_id'];

// ----------------------------------------
// 4) Hitung Total Saldo Keseluruhan
$stmt = $pdo->prepare("
  SELECT 
    COALESCE(
      SUM(
        a.initial_balance 
        + COALESCE((
            SELECT SUM(
              CASE WHEN t.type = 'income' THEN t.amount ELSE -t.amount END
            )
            FROM transactions t
            WHERE t.account_id = a.id
          ), 0)
      ), 
      0
    ) AS total_saldo
  FROM accounts a
  WHERE a.user_id = :u
");
$stmt->execute(['u' => $uid]);
$rowSaldo = $stmt->fetch();
$total_saldo = $rowSaldo['total_saldo'] ?? 0;

// ----------------------------------------
// 5) Hitung Pemasukan & Pengeluaran Bulan Ini
$currentMonth = date('Y-m');
$stmt = $pdo->prepare("
  SELECT
    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0)   AS pemasukan,
    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS pengeluaran
  FROM transactions
  WHERE user_id = :u
    AND DATE_FORMAT(transaction_date, '%Y-%m') = :bulan
");
$stmt->execute([
  'u'     => $uid,
  'bulan' => $currentMonth
]);
$row = $stmt->fetch();
$pemasukan   = $row['pemasukan']   ?? 0;
$pengeluaran = $row['pengeluaran'] ?? 0;

// ----------------------------------------
// 6) Ambil 5 Transaksi Terakhir
$stmt = $pdo->prepare("
  SELECT 
    t.id, 
    t.transaction_date, 
    t.description, 
    t.type, 
    t.amount,
    c.name AS cat_name,
    a.name AS acc_name
  FROM transactions t
  JOIN categories c ON t.category_id = c.id
  JOIN accounts   a ON t.account_id = a.id
  WHERE t.user_id = :u
  ORDER BY t.transaction_date DESC
  LIMIT 5
");
$stmt->execute(['u' => $uid]);
$recentTrans = $stmt->fetchAll();

// ----------------------------------------
// 7) Ambil Top 5 Kategori Pengeluaran Bulan Ini
//    Kita group by kategori, hanya tipe='expense' dan bulan = currentMonth
$stmt = $pdo->prepare("
  SELECT 
    c.name AS cat_name, 
    COALESCE(SUM(t.amount), 0) AS total_pengeluaran
  FROM transactions t
  JOIN categories c ON t.category_id = c.id
  WHERE t.user_id = :u
    AND t.type = 'expense'
    AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :bulan
  GROUP BY c.id
  ORDER BY total_pengeluaran DESC
  LIMIT 5
");
$stmt->execute([
  'u'     => $uid,
  'bulan' => $currentMonth
]);
$topCatRows = $stmt->fetchAll();

// Pecah hasilnya ke dalam dua array JS: $catNames, $catValues
$catNames  = [];
$catValues = [];
foreach ($topCatRows as $rowCat) {
    $catNames[]  = $rowCat['cat_name'];
    $catValues[] = (float)$rowCat['total_pengeluaran'];
}
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">

  <!-- Bagian Judul & Ringkasan -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold">Dashboard (User)</h2>
      <hr />
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Total Saldo Keseluruhan</h6>
          <h2 class="fw-bold">Rp <?= number_format($total_saldo, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Pemasukan Bulan Ini</h6>
          <h2 class="fw-bold">Rp <?= number_format($pemasukan, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Pengeluaran Bulan Ini</h6>
          <h2 class="fw-bold">Rp <?= number_format($pengeluaran, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Bagian Grafik: Pemasukan vs Pengeluaran -->
  <div class="row mb-4">
    <div class="col-lg-8 mb-3">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">Perbandingan Pemasukan vs Pengeluaran</h6>
        </div>
        <div class="card-body">
          <canvas id="chartIncomeExpense" height="200"></canvas>
        </div>
      </div>
    </div>

    <!-- Top 5 Kategori Pengeluaran -->
    <div class="col-lg-4 mb-3">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">Top 5 Kategori Pengeluaran</h6>
        </div>
        <div class="card-body">
          <canvas id="chartTopCategories" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel 5 Transaksi Terakhir -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">Transaksi Terakhir</h6>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped align-middle m-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Akun</th>
                <th>Tipe</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php if (!empty($recentTrans)): ?>
                <?php foreach ($recentTrans as $t): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= date('d M Y', strtotime($t['transaction_date'])) ?></td>
                    <td><?= htmlspecialchars($t['description'] ?: '-') ?></td>
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
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center">Belum ada transaksi.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="text-end p-3">
            <a href="transactions_list.php" class="btn btn-sm btn-outline-primary">
              Lihat Semua Transaksi
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
// 8) Tutup layout dengan footer (Chart.js & skrip kustom)
require_once 'includes/footer.php';
?>

<!-- 9) Blok JavaScript untuk menggambar Chart.js -->
<script>
  // Data PHP untuk Pemasukan vs Pengeluaran
  const pemasukanUser   = <?= json_encode((float)$pemasukan) ?>;
  const pengeluaranUser = <?= json_encode((float)$pengeluaran) ?>;

  // Gambar chart “Pemasukan vs Pengeluaran”
  const ctxIE = document.getElementById('chartIncomeExpense').getContext('2d');
  new Chart(ctxIE, {
    type: 'bar',
    data: {
      labels: ['Pemasukan', 'Pengeluaran'],
      datasets: [{
        label: 'Jumlah (Rp)',
        data: [pemasukanUser, pengeluaranUser],
        backgroundColor: ['rgba(40, 167, 69, 0.7)', 'rgba(220, 53, 69, 0.7)'],
        borderColor: ['rgba(40, 167, 69, 1)', 'rgba(220, 53, 69, 1)'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'Rp ' + value.toLocaleString('id-ID');
            }
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // Data PHP untuk Top 5 Kategori Pengeluaran
  const catLabels  = <?= json_encode($catNames)  ?>;
  const catValues  = <?= json_encode($catValues) ?>;

  // Gambar chart “Top 5 Kategori Pengeluaran” (Pie Chart)
  const ctxTC = document.getElementById('chartTopCategories').getContext('2d');
  new Chart(ctxTC, {
    type: 'pie',
    data: {
      labels: catLabels,
      datasets: [{
        data: catValues,
        backgroundColor: [
          'rgba(220, 53, 69, 0.7)',
          'rgba(255, 193, 7, 0.7)',
          'rgba(13, 110, 253, 0.7)',
          'rgba(40, 167, 69, 0.7)',
          'rgba(108, 117, 125, 0.7)'
        ],
        borderColor: [
          'rgba(220, 53, 69, 1)',
          'rgba(255, 193, 7, 1)',
          'rgba(13, 110, 253, 1)',
          'rgba(40, 167, 69, 1)',
          'rgba(108, 117, 125, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
</script>
