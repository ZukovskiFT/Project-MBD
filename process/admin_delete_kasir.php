<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin_dashboard.php");
    exit;
}

$id_kasir = (int)($_POST['id_kasir'] ?? 0);

if (!$id_kasir) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}

try {
    // Cek apakah kasir punya riwayat transaksi
    $cekTrx = $conn->prepare("SELECT COUNT(*) FROM transaksi WHERE id_kasir = :id");
    $cekTrx->execute([':id' => $id_kasir]);
    if ((int)$cekTrx->fetchColumn() > 0) {
        // Jangan hapus — akan merusak data laporan
        header("Location: ../public/admin_dashboard.php?status=has_trx");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM kasir WHERE id_kasir = :id");
    $stmt->execute([':id' => $id_kasir]);

    header("Location: ../public/admin_dashboard.php?status=deleted");
    exit;

} catch (PDOException $e) {
    header("Location: ../public/admin_dashboard.php?status=error");
    exit;
}