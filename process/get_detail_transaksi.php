<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    exit;
}

try {
    // Ambil header transaksi beserta nama kasir
    $stmtTrx = $conn->prepare("
        SELECT t.*,
               k.nama_kasir,
               CONCAT('TRX-', DATE_FORMAT(t.tanggal, '%Y%m%d'), '-', LPAD(t.id_transaksi, 4, '0')) AS kode_transaksi,
               t.total AS total_harga
        FROM transaksi t
        JOIN kasir k ON k.id_kasir = t.id_kasir
        WHERE t.id_transaksi = :id
    ");
    $stmtTrx->execute([':id' => $id]);
    $transaksi = $stmtTrx->fetch(PDO::FETCH_ASSOC);

    if (!$transaksi) {
        echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan.']);
        exit;
    }

    // Ambil detail item beserta nama barang dan harga dari tabel barang
    $stmtDetail = $conn->prepare("
        SELECT d.id_barang, d.id_transaksi, d.jumlah, d.subtotal,
               b.nama_barang, b.harga_jual AS harga_satuan
        FROM detail_transaksi d
        JOIN barang b ON b.id_barang = d.id_barang
        WHERE d.id_transaksi = :id
        ORDER BY b.nama_barang
    ");
    $stmtDetail->execute([':id' => $id]);
    $detail = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'   => true,
        'transaksi' => $transaksi,
        'detail'    => $detail
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
