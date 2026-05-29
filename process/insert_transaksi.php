<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
    exit;
}

$id_kasir       = isset($input['id_kasir'])     ? (int)$input['id_kasir']     : 0;
$total          = isset($input['total_harga'])   ? (int)$input['total_harga']  : 0;
$bayar          = isset($input['bayar'])          ? (int)$input['bayar']        : 0;
$kembalian      = $bayar - $total;
$items          = $input['items']          ?? [];
$nama_pelanggan = trim($input['nama_pelanggan'] ?? 'Umum');

if (!$id_kasir) {
    echo json_encode(['success' => false, 'message' => 'Kasir belum dipilih.']);
    exit;
}
if (empty($items) || !is_array($items)) {
    echo json_encode(['success' => false, 'message' => 'Keranjang belanja kosong.']);
    exit;
}
if ($bayar < $total) {
    echo json_encode(['success' => false, 'message' => 'Jumlah bayar kurang dari total harga.']);
    exit;
}

try {
    $conn->beginTransaction();

    // 1. Insert ke tabel transaksi (sesuai skema DB: id_kasir, tanggal, total)
    $stmtTrx = $conn->prepare(
        "INSERT INTO transaksi (id_kasir, tanggal, total) VALUES (:id_kasir, NOW(), :total)"
    );
    $stmtTrx->execute([
        ':id_kasir' => $id_kasir,
        ':total'    => $total,
    ]);
    $id_transaksi   = $conn->lastInsertId();
    $kode_transaksi = 'TRX-' . date('Ymd') . '-' . str_pad($id_transaksi, 4, '0', STR_PAD_LEFT);

    // 2. Insert ke tabel detail_transaksi (sesuai skema DB: id_barang, id_transaksi, jumlah, subtotal)
    $stmtDetail = $conn->prepare(
        "INSERT INTO detail_transaksi (id_barang, id_transaksi, jumlah, subtotal)
         VALUES (:id_barang, :id_transaksi, :jumlah, :subtotal)"
    );

    foreach ($items as $item) {
        if (empty($item['id_barang']) || empty($item['jumlah']) || (int)$item['jumlah'] <= 0) {
            throw new Exception('Item tidak valid: jumlah harus lebih dari 0.');
        }
        $stmtDetail->execute([
            ':id_barang'    => (int)$item['id_barang'],
            ':id_transaksi' => $id_transaksi,
            ':jumlah'       => (int)$item['jumlah'],
            ':subtotal'     => (int)$item['subtotal'],
        ]);
    }

    $conn->commit();
    echo json_encode([
        'success'        => true,
        'id_transaksi'   => $id_transaksi,
        'kode_transaksi' => $kode_transaksi,
        'nama_pelanggan' => $nama_pelanggan,
        'total_harga'    => $total,
        'bayar'          => $bayar,
        'kembalian'      => $kembalian,
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
