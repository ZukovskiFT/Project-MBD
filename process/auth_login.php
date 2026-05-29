<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password =      $_POST['password'] ?? '';

if (!$username || !$password) {
    $_SESSION['login_error'] = 'Username dan password wajib diisi.';
    header("Location: ../public/login.php");
    exit;
}

try {
    // ── 1. Cek tabel admin terlebih dahulu ──────────────────
    $stmtAdmin = $conn->prepare("SELECT * FROM admin WHERE username = :u LIMIT 1");
    $stmtAdmin->execute([':u' => $username]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id_admin'];
        $_SESSION['admin_nama']     = $admin['nama'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: ../public/admin_dashboard.php");
        exit;
    }

    // ── 2. Cek tabel kasir ──────────────────────────────────
    $stmtKasir = $conn->prepare("SELECT * FROM kasir WHERE username = :u LIMIT 1");
    $stmtKasir->execute([':u' => $username]);
    $kasir = $stmtKasir->fetch(PDO::FETCH_ASSOC);

    $passwordValid = false;
    if ($kasir) {
        if (password_verify($password, $kasir['password'])) {
            $passwordValid = true;
        } elseif ($kasir['password'] === $password) {
            // Upgrade password lama (plaintext) ke bcrypt otomatis
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE kasir SET password = :pw WHERE id_kasir = :id");
            $upd->execute([':pw' => $hashed, ':id' => $kasir['id_kasir']]);
            $passwordValid = true;
        }
    }

    if (!$kasir || !$passwordValid) {
        $_SESSION['login_error']  = 'Username atau password salah. Silakan coba lagi.';
        $_SESSION['old_username'] = $username;
        header("Location: ../public/login.php");
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['kasir_id']       = $kasir['id_kasir'];
    $_SESSION['kasir_nama']     = $kasir['nama_kasir'];
    $_SESSION['kasir_username'] = $kasir['username'];
    header("Location: ../public/index.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['login_error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header("Location: ../public/login.php");
    exit;
}
