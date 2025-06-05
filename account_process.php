<?php
// account_process.php
require_once 'includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}
$uid = $_SESSION['user_id'];

if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    // Delete akun jika milik user
    $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = :id AND user_id = :u");
    $stmt->execute(['id' => $id, 'u' => $uid]);
}

header('Location: accounts_list.php');
exit;
