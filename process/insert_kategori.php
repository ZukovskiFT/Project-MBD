<?php
require_once '../config/auth_check_admin.php';

$stmt = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (:nama_kategori)");
$stmt->execute([':nama_kategori' => $_POST['nama_kategori']]);

header("Location: ../public/index.php?status=inserted");
exit;