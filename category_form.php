<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Form Kategori - Finance App</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <!-- Navbar & Sidebar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">FinanceApp</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarMenu"
        aria-controls="navbarMenu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Hi, Nama_User
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="profile.html">Profil Saya</a></li>
              <li><a class="dropdown-item" href="settings.html">Pengaturan</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item text-danger" href="auth/login.html?logout=1">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
        <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="dashboard_user.html">
                <span class="menu-icon" data-feather="home"></span>
                <span class="menu-text">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="accounts_list.html">
                <span class="menu-icon" data-feather="credit-card"></span>
                <span class="menu-text">Akun</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="categories_list.html">
                <span class="menu-icon" data-feather="list"></span>
                <span class="menu-text">Kategori</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="transactions_list.html">
                <span class="menu-icon" data-feather="file-text"></span>
                <span class="menu-text">Transaksi</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="budgets_list.html">
                <span class="menu-icon" data-feather="bar-chart-2"></span>
                <span class="menu-text">Anggaran</span>
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Form Kategori -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
        <div class="row mb-3">
          <div class="col">
            <h4>Form Kategori Baru</h4>
            <hr />
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <!-- 
              Contoh error server‐side:
              <div class="alert alert-danger">Nama kategori sudah ada.</div>
            -->
            <form id="categoryForm" action="category_process.php" method="post" novalidate>
              <!-- Jika edit, tambahkan hidden field untuk id dan type:
              <input type="hidden" name="id" value="3" />
              <input type="hidden" name="type" value="expense" />
              -->
              <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori</label>
                <input
                  type="text"
                  id="name"
                  name="name"
                  class="form-control"
                  placeholder="Misal: Makanan"
                  required
                />
                <div class="invalid-feedback">
                  Nama kategori wajib diisi.
                </div>
              </div>
              <div class="mb-3">
                <label for="type" class="form-label">Tipe</label>
                <select id="type" name="type" class="form-select" required>
                  <option value="" disabled selected>-- Pilih Tipe --</option>
                  <option value="income">Pemasukan</option>
                  <option value="expense">Pengeluaran</option>
                </select>
                <div class="invalid-feedback">
                  Tipe kategori wajib dipilih.
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Simpan Kategori</button>
              <a href="categories_list.html" class="btn btn-secondary">Batal</a>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script>
    feather.replace();
  </script>
</body>
</html>
