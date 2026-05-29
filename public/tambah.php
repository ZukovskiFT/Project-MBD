<?php
require_once '../config/database.php';
$stmtKategori = $conn->query("SELECT * FROM kategori");
$kategoriList = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);
?>

<span class="close-btn" onclick="tutupModal()">&times;</span>
<h2 style="color:#16a34a; margin-top:0; text-align:center;">Tambah Barang Baru</h2>

<form action="../process/insert.php" method="POST" onsubmit="return validasiForm()">
    <div class="form-group">
        <label>Kategori</label>
        <select name="id_kategori" class="form-control" required style="cursor:pointer;">
            <option value="" disabled selected>-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
        <small id="pesanNama" style="color:#ef4444; font-size:12px; display:none;">Nama barang tidak boleh kosong.</small>
    </div>

    <div class="form-group">
        <label>Harga Jual (Rp)</label>
        <input type="number" name="harga_jual" id="harga_jual" class="form-control" required>
        <small id="pesanHarga" style="color:#ef4444; font-size:12px; display:none;">Harga harus lebih dari 0.</small>
    </div>

    <button type="submit" class="btn" style="background:linear-gradient(135deg,#16a34a,#22c55e); color:white; width:100%; padding:12px; border-radius:8px; margin-top:10px; font-weight:600; border:none; cursor:pointer;">
        Simpan Barang
    </button>
</form>

<script>
function validasiForm() {
    let valid = true;
    const nama = document.getElementById('nama_barang').value.trim();
    const pesanNama = document.getElementById('pesanNama');
    if (nama === '') { pesanNama.style.display = 'block'; valid = false; }
    else { pesanNama.style.display = 'none'; }

    const harga = parseFloat(document.getElementById('harga_jual').value);
    const pesanHarga = document.getElementById('pesanHarga');
    if (isNaN(harga) || harga <= 0) { pesanHarga.style.display = 'block'; valid = false; }
    else { pesanHarga.style.display = 'none'; }

    return valid;
}
</script>
