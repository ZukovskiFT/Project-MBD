<?php
require_once '../config/database.php';
if (!isset($_GET['id_barang'])) exit;

$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = :id");
$stmt->execute([':id' => $_GET['id_barang']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div style="text-align: center;">
    <span class="close-btn" onclick="tutupModal()">&times;</span>
    
    <div style="width:66px; height:66px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
        <i class="fa-solid fa-trash" style="color:#ef4444; font-size:30px;"></i>
    </div>

    <h2 style="color: #ef4444; margin-top: 0; margin-bottom: 10px;">Konfirmasi Hapus</h2>
    
    <p style="color: #64748b; margin-bottom: 25px;">
        Apakah anda yakin ingin menghapus data <br>
        <strong style="color: #0369a1; font-size: 18px;"><?= $data['nama_barang']; ?></strong> ?
    </p>
    
    <div style="display: flex; gap: 10px; justify-content: center;">
        <button class="btn" style="background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0; padding: 10px 20px;" onclick="tutupModal()">Batal</button>
        <a href="../process/delete.php?id_barang=<?= $data['id_barang']; ?>" class="btn btn-hapus" style="padding: 10px 20px;">Ya, Hapus</a>
    </div>
</div>
