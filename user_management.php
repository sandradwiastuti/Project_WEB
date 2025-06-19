<?php
require_once 'includes/koneksi.php';
require_once 'includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard_user.php');
    exit;
}

require_once 'includes/sidebar.php';
$stmt = $pdo->query("
  SELECT id, username, full_name, email, role, DATE_FORMAT(created_at, '%d %b %Y') AS reg_date
  FROM users
  ORDER BY created_at DESC
");
$users = $stmt->fetchAll();
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
  <div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
      <h4>Manajemen User</h4>
      <a href="user_form.php" class="btn btn-primary">Tambah User Baru</a>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card shadow-sm">
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
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; ?>
              <?php foreach ($users as $u): ?>
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
                  <td><?= $u['reg_date'] ?></td>
                  <td>
                    <a href="user_form.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                      <a href="user_process.php?delete_id=<?= $u['id'] ?>"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Yakin ingin menghapus user ini?');">
                        Hapus
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center">Belum ada user.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
<?php require_once 'includes/footer.php'; ?>

