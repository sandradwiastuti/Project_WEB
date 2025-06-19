<?php
require_once 'includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: auth/login.php');
    exit;
}

if (isset($_GET['delete_id'])) {
    $del_id = (int) $_GET['delete_id'];
    if ($del_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $del_id]);
    }
}

header('Location: user_management.php');
exit;
