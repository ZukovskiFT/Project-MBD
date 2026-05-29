<?php
require_once '../config/database.php';
if (!isset($_GET['id_barang'])) exit;

$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = :id");
$stmt->execute([':id' => $_GET['id_barang']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) exit("<p style='color:#ef4444;'>Data tidak ditemukan</p>");

$stmtKategori = $conn->query("SELECT * FROM kategori");
$kategoriList = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);
?>

<span class="close-btn" onclick="tutupModal()">&times;</span>
<h2 style="color: #0369a1; margin-top: 0; text-align: center;">Edit Barang</h2>

<form action="../process/update.php" method="POST">
    <input type="hidden" name="id_barang" value="<?= $data['id_barang']; ?>">
    
    <div class="form-group">
        <label>Kategori</label>
        <select name="id_kategori" class="form-control" required style="cursor: pointer;">
            <option value="" disabled>-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat['id_kategori']; ?>" <?= ($data['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                    <?= $kat['nama_kategori']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang'], ENT_QUOTES); ?>" required>
    </div>
    
    <div class="form-group">
        <label>Harga Jual (Rp)</label>
        <input type="number" name="harga_jual" class="form-control" value="<?= $data['harga_jual']; ?>" required>
    </div>
    
    <button type="submit" class="btn" style="background: linear-gradient(135deg,#0369a1,#0ea5e9); color: white; width: 100%; padding: 12px; border-radius: 8px; margin-top: 10px; font-weight: 600; border: none; cursor: pointer;">Simpan Perubahan</button>
</form>
