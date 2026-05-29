<?php
// 1. Pengecekan keamanan khusus admin (Wajib agar tidak sembarang orang bisa akses)
require_once '../config/auth_check_admin.php';

// 2. Hubungkan ke database (Ini yang menyelesaikan error $conn pada baris 6 Anda)
require_once '../config/database.php';

// Tolak akses jika bukan dari form (mencegah akses URL langsung)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/admin_barang.php");
    exit;
}

// 3. Tangkap data dari form (edit.php)
$id_barang   = (int)($_POST['id_barang'] ?? 0);
$id_kategori = (int)($_POST['id_kategori'] ?? 0);
$nama_barang = trim($_POST['nama_barang'] ?? '');
$harga_jual  = (int)($_POST['harga_jual'] ?? 0);

// Validasi jika ada data yang kosong
if (!$id_barang || !$id_kategori || !$nama_barang || !$harga_jual) {
    header("Location: ../public/admin_barang.php?status=error");
    exit;
}

try {
    // 4. Proses Update ke Database
    $stmt = $conn->prepare("UPDATE barang SET id_kategori = :kategori, nama_barang = :nama, harga_jual = :harga WHERE id_barang = :id");
    
    $stmt->execute([
        ':kategori' => $id_kategori,
        ':nama'     => $nama_barang,
        ':harga'    => $harga_jual,
        ':id'       => $id_barang
    ]);

    // 5. Kembalikan ke halaman admin_barang dengan status sukses
    header("Location: ../public/admin_barang.php?status=updated");
    exit;

} catch (PDOException $e) {
    // Jika ada error pada eksekusi database
    header("Location: ../public/admin_barang.php?status=error");
    exit;
}