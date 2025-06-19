<?php
// includes/sidebar.php
// Pastikan sudah ada session di header.php

?>
<!-- Sidebar -->
<nav class="col-md-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
  <div class="position-sticky pt-3">
    <ul class="nav flex-column">
      <?php if ($role === 'admin'): ?>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'dashboard_admin.php') ? 'active' : '' ?>" href="dashboard_admin.php">
            <span class="menu-icon" data-feather="home"></span>
            <span class="menu-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'accounts_list.php') ? 'active' : '' ?>" href="accounts_list.php">
            <span class="menu-icon" data-feather="credit-card"></span>
            <span class="menu-text">Akun</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'categories_list.php') ? 'active' : '' ?>" href="categories_list.php">
            <span class="menu-icon" data-feather="list"></span>
            <span class="menu-text">Kategori</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'transactions_list.php') ? 'active' : '' ?>" href="transactions_list.php">
            <span class="menu-icon" data-feather="file-text"></span>
            <span class="menu-text">Transaksi</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'budgets_list.php') ? 'active' : '' ?>" href="budgets_list.php">
            <span class="menu-icon" data-feather="bar-chart-2"></span>
            <span class="menu-text">Anggaran</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'user_management.php') ? 'active' : '' ?>" href="user_management.php">
            <span class="menu-icon" data-feather="users"></span>
            <span class="menu-text">Manajemen User</span>
          </a>
        </li>
      <?php else: /* role = user */ ?>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'dashboard_user.php') ? 'active' : '' ?>" href="dashboard_user.php">
            <span class="menu-icon" data-feather="home"></span>
            <span class="menu-text">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'accounts_list.php') ? 'active' : '' ?>" href="accounts_list.php">
            <span class="menu-icon" data-feather="credit-card"></span>
            <span class="menu-text">Akun</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'categories_list.php') ? 'active' : '' ?>" href="categories_list.php">
            <span class="menu-icon" data-feather="list"></span>
            <span class="menu-text">Kategori</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'transactions_list.php') ? 'active' : '' ?>" href="transactions_list.php">
            <span class="menu-icon" data-feather="file-text"></span>
            <span class="menu-text">Transaksi</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'budgets_list.php') ? 'active' : '' ?>" href="budgets_list.php">
            <span class="menu-icon" data-feather="bar-chart-2"></span>
            <span class="menu-text">Anggaran</span>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
