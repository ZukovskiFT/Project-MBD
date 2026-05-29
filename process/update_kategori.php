<?php
require_once '../config/auth_check_admin.php';

$stmt = $conn->prepare("UPDATE kategori SET nama_kategori = :nama WHERE id_kategori = :id");
$stmt->execute([
    ':nama' => $_POST['nama_kategori'],
    ':id'   => $_POST['id_kategori']
]);

header("Location: ../public/index.php?status=updated");
exit;