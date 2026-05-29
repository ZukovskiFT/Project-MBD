<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin_dashboard.php");
    exit;
}

$id_kasir   = (int)($_POST['id_kasir']   ?? 0);
$nama_kasir = trim($_POST['nama_kasir']  ?? '');
$username   = trim($_POST['username']    ?? '');
$password   =      $_POST['password']   ?? '';

if (!$id_kasir || !$nama_kasir || !$username) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}

try {
    // Cek username duplikat (kecuali milik kasir ini sendiri)
    $check = $conn->prepare(
        "SELECT id_kasir FROM kasir WHERE username = :u AND id_kasir != :id LIMIT 1"
    );
    $check->execute([':u' => $username, ':id' => $id_kasir]);
    if ($check->fetch()) {
        header("Location: ../public/admin_dashboard.php?status=duplicate");
        exit;
    }

    if (!empty($password)) {
        // Ubah password sekaligus
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "UPDATE kasir SET nama_kasir = :nama, username = :username, password = :password
             WHERE id_kasir = :id"
        );
        $stmt->execute([
            ':nama'     => $nama_kasir,
            ':username' => $username,
            ':password' => $hashed,
            ':id'       => $id_kasir,
        ]);
    } else {
        // Biarkan password lama
        $stmt = $conn->prepare(
            "UPDATE kasir SET nama_kasir = :nama, username = :username WHERE id_kasir = :id"
        );
        $stmt->execute([':nama' => $nama_kasir, ':username' => $username, ':id' => $id_kasir]);
    }

    header("Location: ../public/admin_dashboard.php?status=updated");
    exit;

} catch (PDOException $e) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}