<?php
require_once '../config/auth_check_admin.php';

// Validasi input tidak kosong
if (empty($_POST['nama_barang']) || empty($_POST['harga_jual']) || empty($_POST['id_kategori'])) {
    die("Input tidak boleh kosong.");
}

// Validasi harga harus numerik dan lebih dari 0
if (!is_numeric($_POST['harga_jual']) || $_POST['harga_jual'] <= 0) {
    die("Harga tidak valid.");
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO barang (id_kategori, nama_barang, harga_jual)
         VALUES (:id_kategori, :nama_barang, :harga_jual)"
    );

    $stmt->execute([
        ':id_kategori' => $_POST['id_kategori'],
        ':nama_barang' => trim($_POST['nama_barang']),
        ':harga_jual'  => $_POST['harga_jual']
    ]);

    header("Location: ../public/index.php?status=inserted");
    exit;

} catch (PDOException $e) {
    die("Gagal menambah data: " . $e->getMessage());
}