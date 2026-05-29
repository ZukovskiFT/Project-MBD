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
    
    <div class="modal-actions">
        <button type="button" class="btn btn-ghost" onclick="tutupModal()">Batal</button>
        <a href="../process/delete.php?id=<?= $id_barang ?>" class="btn btn-danger" id="btn_hapus_submit">
            <i class="fa-solid fa-trash"></i> Ya, Hapus
        </a>
    </div>
</div>
