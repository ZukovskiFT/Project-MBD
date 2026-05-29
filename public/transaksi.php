<?php
require_once '../config/auth_check.php';
require_once '../config/database.php';

// Ambil semua barang beserta kategori
$query = "SELECT barang.*, kategori.nama_kategori FROM barang
          JOIN kategori ON barang.id_kategori = kategori.id_kategori
          ORDER BY kategori.nama_kategori, barang.nama_barang";
$barangList = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Ambil kategori untuk filter
$kategoriList = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar kasir dari database
$kasirList = $conn->query("SELECT id_kasir, nama_kasir FROM kasir ORDER BY nama_kasir")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kasir - Transaksi</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ee 50%, #fef9e7 100%);
    color: #1e293b;
    min-height: 100vh;
}

/* ---- NAV ---- */
.topbar {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    color: white;
    padding: 14px 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.topbar h1 { font-size: 20px; font-weight: 700; }
.topbar p  { font-size: 12px; opacity: 0.75; }
.topbar-nav { margin-left: auto; display: flex; gap: 10px; }
.nav-btn {
    padding: 8px 16px; border-radius: 10px; text-decoration: none;
    font-size: 13px; font-weight: 600; transition: 0.2s; color: white;
    border: 1.5px solid rgba(255,255,255,0.3);
}
.nav-btn:hover { background: rgba(255,255,255,0.15); }
.nav-btn.active { background: rgba(255,255,255,0.2); border-color: white; }

/* ---- LAYOUT ---- */
.main { display: grid; grid-template-columns: 1fr 380px; gap: 20px; padding: 24px 30px; max-width: 1400px; margin: auto; }

/* ---- PANEL KIRI (Katalog Barang) ---- */
.panel { background: white; border-radius: 16px; padding: 22px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
.panel-title { font-size: 16px; font-weight: 700; color: #1e3a5f; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

.search-bar {
    display: flex; gap: 10px; margin-bottom: 14px;
}
.search-bar input, .search-bar select {
    padding: 9px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0;
    font-family: 'Poppins', sans-serif; font-size: 13px; color: #1e293b;
    background: #f8fafc;
}
.search-bar input { flex: 1; }
.search-bar input:focus, .search-bar select:focus { outline: none; border-color: #0ea5e9; background: white; }

.barang-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(165px, 1fr));
    gap: 12px;
    max-height: calc(100vh - 280px);
    overflow-y: auto;
    padding-right: 4px;
}
.barang-card {
    border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 14px;
    cursor: pointer; transition: 0.25s; background: white; position: relative;
}
.barang-card:hover { border-color: #0ea5e9; background: #f0f9ff; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(14,165,233,0.15); }
.barang-card .kat-badge {
    font-size: 10px; padding: 2px 8px; border-radius: 20px; background: #e0f2fe; color: #0369a1;
    font-weight: 600; margin-bottom: 8px; display: inline-block;
}
.barang-card .nama { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px; line-height: 1.4; }
.barang-card .harga { font-size: 13px; color: #0369a1; font-weight: 700; }
.barang-card .add-icon {
    position: absolute; top: 10px; right: 10px;
    width: 26px; height: 26px; border-radius: 50%;
    background: #22c55e; color: white; display: flex; align-items: center; justify-content: center;
    font-size: 14px; opacity: 0; transition: 0.2s;
}
.barang-card:hover .add-icon { opacity: 1; }
.barang-card.in-cart { border-color: #22c55e; background: #f0fdf4; }
.barang-card.in-cart .add-icon { opacity: 1; background: #16a34a; }

/* ---- PANEL KANAN (Keranjang) ---- */
.cart-panel { display: flex; flex-direction: column; gap: 0; }
.cart-box { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); flex: 1; display: flex; flex-direction: column; }

.form-group { margin-bottom: 12px; }
.form-group label { display: block; margin-bottom: 5px; font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.form-control {
    width: 100%; padding: 9px 12px; border-radius: 9px; border: 1.5px solid #e2e8f0;
    font-family: 'Poppins', sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc;
}
.form-control:focus { outline: none; border-color: #0ea5e9; background: white; }

/* Keranjang List */
.cart-list { flex: 1; overflow-y: auto; max-height: 280px; margin: 10px 0; }
.cart-empty { text-align: center; color: #94a3b8; padding: 30px 0; }
.cart-empty i { font-size: 36px; display: block; margin-bottom: 10px; }

.cart-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid #f1f5f9;
}
.cart-item:last-child { border-bottom: none; }
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-info .nama { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cart-item-info .sub { font-size: 12px; color: #0369a1; font-weight: 700; }
.qty-ctrl { display: flex; align-items: center; gap: 5px; }
.qty-btn {
    width: 26px; height: 26px; border-radius: 7px; border: 1.5px solid #e2e8f0;
    background: #f8fafc; cursor: pointer; font-size: 14px; font-weight: 700;
    color: #475569; display: flex; align-items: center; justify-content: center; transition: 0.15s;
}
.qty-btn:hover { background: #e0f2fe; border-color: #0ea5e9; color: #0369a1; }
.qty-num { width: 28px; text-align: center; font-weight: 700; font-size: 13px; }
.cart-del { color: #ef4444; cursor: pointer; font-size: 14px; padding: 4px; }
.cart-del:hover { color: #b91c1c; }

/* Totals */
.divider { border: none; border-top: 1.5px dashed #e2e8f0; margin: 12px 0; }
.total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px; }
.total-row.big { font-size: 16px; font-weight: 700; color: #1e3a5f; }
.total-row span:last-child { color: #0369a1; font-weight: 700; }
.total-row.big span:last-child { font-size: 17px; }

.kembalian-box {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius: 10px;
    padding: 10px 14px; margin: 8px 0; display: flex; justify-content: space-between;
    align-items: center; font-size: 14px;
}
.kembalian-box .label { color: #16a34a; font-weight: 600; }
.kembalian-box .nilai { font-size: 16px; font-weight: 700; color: #15803d; }
.kembalian-box.kurang { background: linear-gradient(135deg, #fff1f2, #ffe4e6); }
.kembalian-box.kurang .label { color: #ef4444; }
.kembalian-box.kurang .nilai { color: #dc2626; }

.btn-bayar {
    width: 100%; padding: 14px; border-radius: 12px;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: white; font-family: 'Poppins', sans-serif; font-size: 15px;
    font-weight: 700; border: none; cursor: pointer; margin-top: 12px;
    box-shadow: 0 6px 18px rgba(34,197,94,0.35); transition: 0.25s;
}
.btn-bayar:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(34,197,94,0.4); }
.btn-bayar:disabled { background: #94a3b8; cursor: not-allowed; box-shadow: none; transform: none; }
.btn-reset { width: 100%; padding: 9px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: transparent; color: #64748b; font-family: 'Poppins', sans-serif; font-size: 13px; cursor: pointer; margin-top: 8px; transition: 0.2s; }
.btn-reset:hover { background: #fee2e2; border-color: #ef4444; color: #ef4444; }

/* Toast */
.toast { position: fixed; bottom: 30px; right: 30px; background: white; border-left: 4px solid #22c55e; color: #1e293b; padding: 14px 20px; border-radius: 10px; font-size: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 9999; opacity: 0; transform: translateY(20px); transition: 0.3s; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.error { border-left-color: #ef4444; }

/* Modal Struk */
.modal-overlay { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,23,42,0.45); backdrop-filter: blur(8px); align-items: center; justify-content: center; }
.struk-box { background: white; border-radius: 16px; padding: 30px 28px; width: 100%; max-width: 420px; box-shadow: 0 16px 48px rgba(0,0,0,0.2); }
.struk-header { text-align: center; margin-bottom: 20px; }
.struk-header h2 { font-size: 20px; color: #1e3a5f; }
.struk-header p  { font-size: 12px; color: #64748b; }
.struk-kode { text-align: center; font-size: 13px; background: #f1f5f9; border-radius: 8px; padding: 6px 14px; display: inline-block; margin-bottom: 14px; font-weight: 600; color: #475569; }
.struk-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 12px; }
.struk-table th { text-align: left; padding: 6px 4px; border-bottom: 1px solid #e2e8f0; color: #64748b; font-weight: 600; font-size: 11px; text-transform: uppercase; }
.struk-table td { padding: 7px 4px; border-bottom: 1px dashed #f1f5f9; color: #334155; }
.struk-table td:last-child { text-align: right; font-weight: 600; }
.struk-total { display: flex; justify-content: space-between; padding: 8px 0; font-weight: 700; font-size: 15px; border-top: 2px solid #1e3a5f; color: #1e3a5f; }
.struk-bayar-info { font-size: 13px; color: #64748b; margin: 4px 0; display: flex; justify-content: space-between; }
.struk-kembalian { background: #f0fdf4; border-radius: 8px; padding: 8px 12px; display: flex; justify-content: space-between; font-weight: 700; color: #16a34a; font-size: 14px; margin-top: 8px; }
.struk-footer { text-align: center; margin-top: 16px; font-size: 12px; color: #94a3b8; }
.btn-tutup-struk { display: block; width: 100%; padding: 11px; margin-top: 16px; border-radius: 10px; background: #1e3a5f; color: white; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: 0.2s; }
.btn-tutup-struk:hover { background: #2563eb; }
.btn-print { display: block; width: 100%; padding: 9px; margin-top: 8px; border-radius: 10px; background: transparent; border: 1.5px solid #e2e8f0; color: #475569; font-family: 'Poppins', sans-serif; font-size: 13px; cursor: pointer; }
.btn-print:hover { background: #f1f5f9; }

@media (max-width: 900px) {
    .main { grid-template-columns: 1fr; }
    .cart-panel { order: -1; }
    .barang-grid { max-height: 350px; }
    .cart-list { max-height: 200px; }
}
</style>
</head>
<body>

<div class="topbar">
    <div>
        <h1><i class="fa-solid fa-cash-register" style="margin-right:8px"></i>Kasir - Point of Sale</h1>
        <p>Toko Elektronik Surya Makmur</p>
    </div>
    <div class="topbar-nav">
        <a href="index.php" class="nav-btn"><i class="fa-solid fa-box"></i> Katalog Barang</a>
        <a href="transaksi.php" class="nav-btn active"><i class="fa-solid fa-cash-register"></i> Kasir</a>
        <a href="riwayat_transaksi.php" class="nav-btn"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a>
    </div>
    <!-- Info user yang login + tombol logout -->
    <div style="display:flex; align-items:center; gap:12px; margin-left:10px;">
        <div style="text-align:right; line-height:1.3;">
            <div style="font-size:13px; font-weight:600;">
                <?= htmlspecialchars($_SESSION['kasir_nama']) ?>
            </div>
            <div style="font-size:11px; opacity:0.7;">
                <?= htmlspecialchars($_SESSION['kasir_username']) ?>
            </div>
        </div>
        <div style="width:36px; height:36px; border-radius:50%;
                    background:rgba(255,255,255,0.2); border:2px solid rgba(255,255,255,0.4);
                    display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid fa-user" style="font-size:16px;"></i>
        </div>
        <a href="../process/logout.php"
        title="Keluar"
        style="display:flex; align-items:center; gap:6px;
                background:rgba(239,68,68,0.18); border:1.5px solid rgba(239,68,68,0.5);
                color:white; text-decoration:none; padding:7px 13px;
                border-radius:10px; font-size:13px; font-weight:600; transition:0.2s;"
        onmouseover="this.style.background='rgba(239,68,68,0.35)'"
        onmouseout="this.style.background='rgba(239,68,68,0.18)'">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>

<div class="main">

    <!-- KIRI: Katalog Barang -->
    <div class="panel">
        <div class="panel-title">
            <i class="fa-solid fa-store" style="color:#0ea5e9"></i>
            Pilih Barang
        </div>

        <div class="search-bar">
            <input type="text" id="cariBarang" placeholder="🔍 Cari nama barang..." oninput="filterBarang()">
            <select id="filterKat" onchange="filterBarang()">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="barang-grid" id="barangGrid">
            <?php foreach ($barangList as $b): ?>
            <div class="barang-card"
                 id="card-<?= $b['id_barang'] ?>"
                 data-id="<?= $b['id_barang'] ?>"
                 data-nama="<?= htmlspecialchars($b['nama_barang'], ENT_QUOTES) ?>"
                 data-harga="<?= $b['harga_jual'] ?>"
                 data-kat="<?= $b['id_kategori'] ?>"
                 onclick="tambahKeKeranjang(<?= $b['id_barang'] ?>)">
                <div class="add-icon"><i class="fa-solid fa-plus"></i></div>
                <div class="kat-badge"><?= $b['nama_kategori'] ?></div>
                <div class="nama"><?= htmlspecialchars($b['nama_barang']) ?></div>
                <div class="harga">Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- KANAN: Keranjang & Pembayaran -->
    <div class="cart-panel">
        <div class="cart-box">
            <div class="panel-title">
                <i class="fa-solid fa-cart-shopping" style="color:#22c55e"></i>
                Keranjang Belanja
            </div>

            <div class="form-group">
                <label>Kasir Bertugas</label>
                <select id="pilih_kasir" class="form-control" onchange="hitungKembalian()">
                    <option value="">-- Pilih Kasir --</option>
                    <?php foreach ($kasirList as $k): ?>
                    <option value="<?= $k['id_kasir'] ?>"><?= htmlspecialchars($k['nama_kasir']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nama Pelanggan (opsional)</label>
                <input type="text" id="nama_pelanggan" class="form-control" placeholder="Masukkan nama pelanggan...">
            </div>

            <!-- Daftar Item Keranjang -->
            <div class="cart-list" id="cartList">
                <div class="cart-empty" id="cartEmpty">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Keranjang masih kosong.<br>
                    <small>Klik barang untuk menambahkan.</small>
                </div>
            </div>

            <hr class="divider">

            <div class="total-row">
                <span>Subtotal</span>
                <span id="subtotalText">Rp 0</span>
            </div>

            <hr class="divider">

            <div class="total-row big">
                <span>TOTAL</span>
                <span id="totalText">Rp 0</span>
            </div>

            <div class="form-group" style="margin-top:10px;">
                <label>Jumlah Bayar (Rp)</label>
                <input type="number" id="jumlah_bayar" class="form-control" placeholder="0" min="0" oninput="hitungKembalian()">
            </div>

            <div class="kembalian-box" id="kembalianBox">
                <span class="label"><i class="fa-solid fa-money-bill-wave"></i> Kembalian</span>
                <span class="nilai" id="kembalianText">Rp 0</span>
            </div>

            <button class="btn-bayar" id="btnBayar" onclick="prosesBayar()" disabled>
                <i class="fa-solid fa-check-circle"></i> Proses Pembayaran
            </button>
            <button class="btn-reset" onclick="resetKeranjang()">
                <i class="fa-solid fa-rotate-left"></i> Reset Keranjang
            </button>

        </div>
    </div>

</div>

<!-- Modal Struk -->
<div id="modalStruk" class="modal-overlay">
    <div class="struk-box">
        <div class="struk-header">
            <div style="width:56px;height:56px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="fa-solid fa-check" style="color:#16a34a;font-size:26px;"></i>
            </div>
            <h2>Pembayaran Berhasil!</h2>
            <p>Toko Elektronik Surya Makmur</p>
        </div>
        <div style="text-align:center;"><span class="struk-kode" id="strukKode"></span></div>
        <div id="strukInfo" style="font-size:12px;color:#64748b;margin-bottom:10px;"></div>
        <table class="struk-table">
            <thead><tr><th>Barang</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody id="strukItems"></tbody>
        </table>
        <div class="struk-total"><span>TOTAL</span><span id="strukTotal"></span></div>
        <div class="struk-bayar-info"><span>Bayar</span><span id="strukBayar"></span></div>
        <div class="struk-kembalian"><span>Kembalian</span><span id="strukKembalian"></span></div>
        <div class="struk-footer">Terima kasih telah berbelanja!<br>Simpan struk ini sebagai bukti pembelian.</div>
        <button class="btn-print" onclick="cetakStruk()"><i class="fa-solid fa-print"></i> Cetak Struk</button>
        <button class="btn-tutup-struk" onclick="tutupStruk()"><i class="fa-solid fa-plus-circle"></i> Transaksi Baru</button>
    </div>
</div>

<div id="toast"></div>

<script>
const keranjang = {}; // { id_barang: { nama, harga, jumlah } }

function formatRp(num) {
    return 'Rp ' + parseInt(num).toLocaleString('id-ID');
}

function filterBarang() {
    const cari = document.getElementById('cariBarang').value.toLowerCase();
    const kat  = document.getElementById('filterKat').value;
    document.querySelectorAll('.barang-card').forEach(card => {
        const cocokNama = card.dataset.nama.toLowerCase().includes(cari);
        const cocokKat  = !kat || card.dataset.kat === kat;
        card.style.display = (cocokNama && cocokKat) ? '' : 'none';
    });
}

function tambahKeKeranjang(id) {
    const card = document.getElementById('card-' + id);
    const nama  = card.dataset.nama;
    const harga = parseInt(card.dataset.harga);
    if (keranjang[id]) {
        keranjang[id].jumlah++;
    } else {
        keranjang[id] = { nama, harga, jumlah: 1 };
    }
    card.classList.add('in-cart');
    renderKeranjang();
    tampilToast('+ ' + nama, 'sukses');
}

function ubahJumlah(id, delta) {
    if (!keranjang[id]) return;
    keranjang[id].jumlah += delta;
    if (keranjang[id].jumlah <= 0) {
        delete keranjang[id];
        const card = document.getElementById('card-' + id);
        if (card) card.classList.remove('in-cart');
    }
    renderKeranjang();
}

function hapusItem(id) {
    delete keranjang[id];
    const card = document.getElementById('card-' + id);
    if (card) card.classList.remove('in-cart');
    renderKeranjang();
}

function renderKeranjang() {
    const list  = document.getElementById('cartList');
    const empty = document.getElementById('cartEmpty');
    const ids   = Object.keys(keranjang);

    if (ids.length === 0) {
        list.innerHTML = '';
        list.appendChild(empty);
        document.getElementById('subtotalText').textContent = 'Rp 0';
        document.getElementById('totalText').textContent    = 'Rp 0';
        document.getElementById('jumlah_bayar').value = '';
        document.getElementById('kembalianText').textContent = 'Rp 0';
        document.getElementById('kembalianBox').classList.remove('kurang');
        document.getElementById('btnBayar').disabled = true;
        return;
    }

    let total = 0;
    let html  = '';
    ids.forEach(id => {
        const item = keranjang[id];
        const sub  = item.harga * item.jumlah;
        total += sub;
        html += `
        <div class="cart-item">
            <div class="cart-item-info">
                <div class="nama">${item.nama}</div>
                <div class="sub">${formatRp(item.harga)} × ${item.jumlah} = ${formatRp(sub)}</div>
            </div>
            <div class="qty-ctrl">
                <button class="qty-btn" onclick="ubahJumlah(${id}, -1)">−</button>
                <span class="qty-num">${item.jumlah}</span>
                <button class="qty-btn" onclick="ubahJumlah(${id}, 1)">+</button>
            </div>
            <i class="fa-solid fa-trash cart-del" onclick="hapusItem(${id})" title="Hapus"></i>
        </div>`;
    });
    list.innerHTML = html;
    document.getElementById('subtotalText').textContent = formatRp(total);
    document.getElementById('totalText').textContent    = formatRp(total);
    hitungKembalian();
}

function hitungKembalian() {
    const ids = Object.keys(keranjang);
    let total = 0;
    ids.forEach(id => { total += keranjang[id].harga * keranjang[id].jumlah; });

    const bayar    = parseInt(document.getElementById('jumlah_bayar').value) || 0;
    const kembalian = bayar - total;
    const box  = document.getElementById('kembalianBox');
    const teks = document.getElementById('kembalianText');

    if (kembalian < 0) {
        teks.textContent = '- ' + formatRp(Math.abs(kembalian));
        box.classList.add('kurang');
    } else {
        teks.textContent = formatRp(kembalian);
        box.classList.remove('kurang');
    }

    const kasirId = document.getElementById('pilih_kasir').value;
    const valid = ids.length > 0 && bayar >= total && kasirId !== '';
    document.getElementById('btnBayar').disabled = !valid;
}

document.getElementById('nama_pelanggan').addEventListener('input', hitungKembalian);

function prosesBayar() {
    const ids  = Object.keys(keranjang);
    if (!ids.length) return;

    const nama    = document.getElementById('nama_pelanggan').value.trim() || 'Umum';
    const bayar   = parseInt(document.getElementById('jumlah_bayar').value) || 0;
    const kasirId = parseInt(document.getElementById('pilih_kasir').value) || 0;

    if (!kasirId) { tampilToast('Pilih kasir terlebih dahulu!', 'error'); return; }

    let total = 0;
    const items = ids.map(id => {
        const item = keranjang[id];
        const sub  = item.harga * item.jumlah;
        total += sub;
        return { id_barang: id, nama_barang: item.nama, harga_satuan: item.harga, jumlah: item.jumlah, subtotal: sub };
    });

    if (bayar < total) { tampilToast('Jumlah bayar kurang!', 'error'); return; }

    const payload = {
        id_kasir:        kasirId,
        nama_pelanggan:  nama,
        total_harga:     total,
        bayar:           bayar,
        kembalian:       bayar - total,
        items:           items
    };

    document.getElementById('btnBayar').disabled = true;
    document.getElementById('btnBayar').textContent = 'Memproses...';

    fetch('../process/insert_transaksi.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            payload._serverRes = res;
            tampilStruk(payload);
        } else {
            tampilToast('Gagal menyimpan: ' + (res.message || ''), 'error');
            document.getElementById('btnBayar').disabled = false;
            document.getElementById('btnBayar').innerHTML = '<i class="fa-solid fa-check-circle"></i> Proses Pembayaran';
        }
    })
    .catch(() => {
        tampilToast('Terjadi kesalahan koneksi.', 'error');
        document.getElementById('btnBayar').disabled = false;
        document.getElementById('btnBayar').innerHTML = '<i class="fa-solid fa-check-circle"></i> Proses Pembayaran';
    });
}

function tampilStruk(data) {
    const res = data._serverRes || data;
    document.getElementById('strukKode').textContent = res.kode_transaksi || ('TRX-' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '-????');
    document.getElementById('strukInfo').innerHTML =
        `<b>Pelanggan:</b> ${data.nama_pelanggan} &nbsp;|&nbsp; <b>Tanggal:</b> ${new Date().toLocaleString('id-ID')}`;

    let rows = '';
    data.items.forEach(item => {
        rows += `<tr>
            <td>${item.nama_barang}</td>
            <td style="text-align:center;">${item.jumlah}</td>
            <td>${formatRp(item.subtotal)}</td>
        </tr>`;
    });
    document.getElementById('strukItems').innerHTML = rows;
    document.getElementById('strukTotal').textContent    = formatRp(data.total_harga);
    document.getElementById('strukBayar').textContent    = formatRp(data.bayar);
    document.getElementById('strukKembalian').textContent = formatRp(data.kembalian);
    document.getElementById('modalStruk').style.display = 'flex';
}

function tutupStruk() {
    document.getElementById('modalStruk').style.display = 'none';
    resetKeranjang();
}

function resetKeranjang() {
    Object.keys(keranjang).forEach(id => {
        delete keranjang[id];
        const card = document.getElementById('card-' + id);
        if (card) card.classList.remove('in-cart');
    });
    document.getElementById('nama_pelanggan').value = '';
    document.getElementById('jumlah_bayar').value   = '';
    renderKeranjang();
}

function cetakStruk() { window.print(); }

function tampilToast(pesan, tipe = 'sukses') {
    const t = document.getElementById('toast');
    t.textContent = pesan;
    t.className = 'toast' + (tipe === 'error' ? ' error' : '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

window.onload = () => {
    const s = new URLSearchParams(window.location.search).get('status');
    if (s === 'success') tampilToast('Transaksi berhasil disimpan!');
};
</script>
</body>
</html>
