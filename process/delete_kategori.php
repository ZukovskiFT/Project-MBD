<?php
require_once '../config/auth_check_admin.php';

$id = $_GET['id'];

// Hapus semua barang dengan kategori ini dulu
$stmt = $conn->prepare("DELETE FROM barang WHERE id_kategori = :id");
$stmt->execute([':id' => $id]);

// Baru hapus kategorinya
$stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = :id");
$stmt->execute([':id' => $id]);

header("Location: ../public/index.php?status=deleted");
exit;