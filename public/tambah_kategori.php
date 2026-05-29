<?php
echo '<span class="close-btn" onclick="tutupModal()">×</span>';
echo '<h3 style="margin:0 0 20px; color:#1e3a5f;">Tambah Kategori</h3>';
?>

<form action="../process/insert_kategori.php" method="POST">
    <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: AC, Laptop..." required>
    </div>
    <button type="submit" class="btn btn-tambah" style="width:100%; margin-top:5px;">Simpan</button>
</form>
