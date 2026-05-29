<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin_dashboard.php");
    exit;
}

$nama_kasir = trim($_POST['nama_kasir'] ?? '');
$username   = trim($_POST['username']   ?? '');
$password   =      $_POST['password']  ?? '';

if (!$nama_kasir || !$username || !$password) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}

try {
    // Cek username sudah dipakai
    $check = $conn->prepare("SELECT id_kasir FROM kasir WHERE username = :u LIMIT 1");
    $check->execute([':u' => $username]);
    if ($check->fetch()) {
        header("Location: ../public/admin_dashboard.php?status=duplicate");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare(
        "INSERT INTO kasir (nama_kasir, username, password) VALUES (:nama, :username, :password)"
    );
    $stmt->execute([':nama' => $nama_kasir, ':username' => $username, ':password' => $hashed]);

    header("Location: ../public/admin_dashboard.php?status=inserted");
    exit;

} catch (PDOException $e) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}