<?php
require_once 'includes/koneksi.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}
$uid = $_SESSION['user_id'];
if (isset($_GET['delete_id'])) {
    $id = (int) $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id AND user_id = :u");
    $stmt->execute(['id' => $id, 'u' => $uid]);
}
header('Location: transactions_list.php');
exit;
