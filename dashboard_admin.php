<?php
// File: dashboard_admin.php

// 1) Aktifkan error reporting (opsional, untuk debugging; matikan kalau sudah oke)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Inklusi koneksi & layout
require_once 'includes/koneksi.php';
require_once 'includes/header.php';

// 3) Pastikan hanya admin boleh mengakses
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard_user.php');
    exit;
}

require_once 'includes/sidebar.php';

// 4) Ambil statistik global

// 4a) Total pengguna
$stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = (int)$stmt->fetchColumn();

// 4b) Total transaksi
$stmt = $pdo->query("SELECT COUNT(*) AS total_trans FROM transactions");
$total_trans = (int)$stmt->fetchColumn();

// 4c) Pemasukan & Pengeluaran keseluruhan
$stmt = $pdo->query("
  SELECT
    COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0)   AS tot_income,
    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS tot_expense
  FROM transactions
");
$row = $stmt->fetch();
$tot_income  = $row['tot_income']  ?? 0;
$tot_expense = $row['tot_expense'] ?? 0;

// 4d) Data grafik transaksi per bulan (6 bulan terakhir)
$labels = [];
$dataTrans = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $labels[] = date('M Y', strtotime($month . '-01'));

    $stmt = $pdo->prepare("
      SELECT COUNT(*) FROM transactions
      WHERE DATE_FORMAT(transaction_date, '%Y-%m') = :m
    ");
    $stmt->execute(['m' => $month]);
    $dataTrans[] = (int)$stmt->fetchColumn();
}

// 4e) Daftar 5 user terbaru
$stmt = $pdo->query("
  SELECT id, username, full_name, email, role, DATE_FORMAT(created_at, '%d %b %Y') AS reg_date
  FROM users
  ORDER BY created_at DESC
  LIMIT 5
");
$newUsers = $stmt->fetchAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <!-- Judul & Ringkasan Angka -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold">Dashboard (Admin)</h2>
      <hr />
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Total Pengguna</h6>
          <h2 class="fw-bold"><?= $total_users ?></h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Total Transaksi</h6>
          <h2 class="fw-bold"><?= $total_trans ?></h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Pemasukan Keseluruhan</h6>
          <h2 class="fw-bold">Rp <?= number_format($tot_income, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6>Pengeluaran Keseluruhan</h6>
          <h2 class="fw-bold">Rp <?= number_format($tot_expense, 0, ',', '.') ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Transaksi per Bulan -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">Grafik Transaksi per Bulan (6 Bulan Terakhir)</h6>
        </div>
        <div class="card-body">
          <canvas id="chartAdminTransactions" height="100"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabel 5 User Terbaru -->
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header">
          <h6 class="mb-0">User Terbaru</h6>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover align-middle m-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Role</th>
                <th>Terdaftar</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php foreach ($newUsers as $u): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($u['username']) ?></td>
                  <td><?= htmlspecialchars($u['full_name']) ?></td>
                  <td><?= htmlspecialchars($u['email']) ?></td>
                  <td>
                    <?php if ($u['role'] === 'admin'): ?>
                      <span class="badge bg-primary">Admin</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">User</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($u['reg_date']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($newUsers)): ?>
                <tr><td colspan="6" class="text-center">Belum ada user baru.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div class="text-end p-3">
            <a href="user_management.php" class="btn btn-sm btn-outline-primary">
              Kelola Semua Pengguna
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
// 5) Footer (memuat Chart.js & skrip kustom)
require_once 'includes/footer.php';
?>

<!-- 6) Blok JavaScript inisialisasi Chart.js -->
<script>
  // Data untuk Chart.js: 6 bulan terakhir
  const labelsAdmin = <?= json_encode($labels) ?>;
  const dataAdmin   = <?= json_encode($dataTrans) ?>;

  const ctx = document.getElementById('chartAdminTransactions').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labelsAdmin,
      datasets: [{
        label: 'Jumlah Transaksi',
        data: dataAdmin,
        borderColor: 'rgba(13, 110, 253, 0.7)',
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
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
</script>
