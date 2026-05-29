<?php
require_once '../config/auth_check_admin.php';

try {
    $stmt = $conn->prepare(
        "DELETE FROM barang WHERE id_barang  = :id_barang"
    );

    $stmt->execute([
        ':id_barang' => $_GET['id_barang']
    ]);

    header("Location: ../public/index.php?status=deleted");
} catch (PDOException $e) {
    echo "Gagal hapus data: " . $e->getMessage();
}
