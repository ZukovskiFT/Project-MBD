<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

// ── Inisialisasi Filter ────────────────────────────────────
// Nilai default dimodifikasi untuk mengakomodasi data historis (Januari 2025)
$tgl_awal  = $_GET['tgl_awal'] ?? '2025-01-01';
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
$filterKasir = $_GET['id_kasir'] ?? '';

// Ambil daftar kasir untuk dropdown filter
$kasirList = $conn->query("SELECT id_kasir, nama_kasir FROM kasir ORDER BY nama_kasir")->fetchAll(PDO::FETCH_ASSOC);

// Susun kondisi WHERE dengan Parameterized Query
$where = ["DATE(t.tanggal) BETWEEN :tgl_awal AND :tgl_akhir"];
$params = [
    ':tgl_awal'  => $tgl_awal,
    ':tgl_akhir' => $tgl_akhir
];

if ($filterKasir) {
    $where[] = "t.id_kasir = :id_kasir";
    $params[':id_kasir'] = (int)$filterKasir;
}
$whereSQL = implode(' AND ', $where);

// ── Pengambilan Data Transaksi ─────────────────────────────
$query = "SELECT t.id_transaksi, t.tanggal, t.total, k.nama_kasir,
                 COUNT(d.id_barang) AS jml_item,
                 CONCAT('TRX-', DATE_FORMAT(t.tanggal, '%Y%m%d'), '-', LPAD(t.id_transaksi, 4, '0')) AS kode_transaksi
          FROM transaksi t
          LEFT JOIN kasir k ON t.id_kasir = k.id_kasir
          LEFT JOIN detail_transaksi d ON d.id_transaksi = t.id_transaksi
          WHERE $whereSQL
          GROUP BY t.id_transaksi, k.nama_kasir
          ORDER BY t.tanggal DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Agregasi Data ──────────────────────────────────────────
$total_pendapatan = 0;
$total_item_terjual = 0;
$total_transaksi  = count($laporan);

foreach ($laporan as $row) {
    $total_pendapatan += $row['total'];
    $total_item_terjual += $row['jml_item'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Transaksi – Toko Elektronik Surya Makmur</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ee 50%, #fef9e7 100%);
    min-height: 100vh; color: #1e293b;
}

/* ── Topbar ── */
.topbar {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    padding: 0 28px; display: flex; align-items: center; gap: 14px;
    box-shadow: 0 4px 20px rgba(30,58,95,0.35);
    position: sticky; top: 0; z-index: 100; min-height: 70px; flex-wrap: wrap;
}
.topbar-brand { flex: 1; min-width: 200px; }
.badge-admin {
    display: inline-flex; align-items: center; gap: 5px;
    background: rgba(245,158,11,0.2); border: 1px solid rgba(245,158,11,0.45);
    color: #fbbf24; font-size: 10.5px; font-weight: 700;
    padding: 2px 9px; border-radius: 20px; letter-spacing: 0.6px; margin-bottom: 4px;
}
.topbar-brand h1 { font-size: 17px; font-weight: 700; color: white; line-height: 1.2; }
.topbar-brand p  { font-size: 11.5px; color: rgba(255,255,255,0.6); }

.topbar-nav { display: flex; gap: 6px; }
/* ── Navigasi yang stabil (Tidak bergeser) ── */
.nav-btn {
    display: flex; 
    align-items: center; 
    gap: 7px; 
    padding: 8px 14px;
    border-radius: 10px; 
    color: white; 
    text-decoration: none;
    font-size: 13px; 
    font-weight: 600; 
    transition: 0.2s;
    /* Memberikan border samar secara default agar ukuran tombol sudah dipesan */
    border: 1.5px solid rgba(255, 255, 255, 0.3);
}

.nav-btn:hover { 
    background: rgba(255, 255, 255, 0.15); 
    border: 1.5px solid rgba(255, 255, 255, 0.5); /* Border jadi lebih tegas saat hover */
}

.nav-btn.active { 
    background: rgba(255, 255, 255, 0.2); 
    /* Tetap 1.5px, jadi tidak akan ada pergeseran layout */
    border: 1.5px solid white; 
}

.topbar-user {
    display: flex; align-items: center; gap: 10px;
    border-left: 1px solid rgba(255,255,255,0.2); padding-left: 14px;
}
.user-info .name { font-size: 13px; font-weight: 600; color: white; line-height: 1.2; }
.user-info .role { font-size: 11px; color: rgba(255,255,255,0.6); }
.avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 15px;
}
.btn-logout {
    display: flex; align-items: center; gap: 6px;
    background: rgba(239,68,68,0.15); border: 1.5px solid rgba(239,68,68,0.4);
    color: white; text-decoration: none; padding: 7px 13px;
    font-family: 'Poppins', sans-serif;
    border-radius: 10px; font-size: 13px; font-weight: 600; transition: background 0.2s;
}
.btn-logout:hover { background: rgba(239,68,68,0.3); }

/* ── Main & Cards ── */
.main { padding: 28px; max-width: 1280px; margin: 0 auto; }
.card {
    background: white; border-radius: 18px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px;
}
.card-header {
    padding: 18px 24px; display: flex; align-items: center;
    justify-content: space-between; border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(to right, #f8fafc, white);
}
.card-header h2 { font-size: 16px; font-weight: 700; color: #1e3a5f; }

/* ── Filter Form ── */
.filter-container { padding: 20px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.filter-form { display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; }
.form-group { flex: 1; min-width: 180px; }
.form-group label { display: block; font-size: 12.5px; font-weight: 600; color: #475569; margin-bottom: 6px; }
.form-control {
    width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px;
    font-size: 13.5px; font-family: 'Poppins', sans-serif; outline: none; background: white;
}
.form-control:focus { border-color: #2563eb; }

/* ── Table ── */
/* ── Table ── */
.table-wrap { overflow-x: auto; border-radius: 12px; }
table { width: 100%; border-collapse: collapse; overflow: hidden; }

thead tr { background: transparent; }

th {
    background: linear-gradient(135deg, #1e3a5f, #2563eb); 
    padding: 12px 18px; 
    color: white; 
    font-weight: 600; 
    text-align: center !important; /* Memaksa judul kolom ke tengah */
    font-size: 13.5px; 
    border: none;
    text-transform: none; /* Menghilangkan huruf kapital semua (uppercase) bawaan lama */
}

td { 
    padding: 13px 18px; 
    font-size: 13.5px; 
    color: #374151; 
    border-bottom: 1px solid #f1f5f9; 
    /*text-align: left; /* Menyesuaikan isi tabel agar rata tengah mengikuti judulnya */
    vertical-align: middle;
}

tbody tr:nth-child(even) { background: #f8fafc; }
tbody tr:nth-child(odd)  { background: white; }
tbody tr:hover { background: #e0f2fe; }

.kode-badge { font-weight: 700; color: #1e3a5f; font-size: 13.5px; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; }

/* ── Buttons ── */
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 18px; border-radius: 9px; font-size: 13.5px; font-weight: 600;
    cursor: pointer; border: none; font-family: 'Poppins', sans-serif; 
    text-decoration: none; transition: opacity 0.2s;
}
.btn:hover  { opacity: 0.9; }
.btn-primary { background: linear-gradient(135deg,#1e3a5f,#2563eb); color:white; }
.btn-success { background: linear-gradient(135deg,#16a34a,#22c55e); color:white; }

/* ── Summary ── */
.summary-bar {
    display: flex; justify-content: space-between; padding: 20px 24px; gap: 20px;
    background: #eff6ff; border-top: 1px solid #bfdbfe; font-weight: 700; color: #1e3a5f; flex-wrap: wrap;
}
.summary-item { display: flex; flex-direction: column; gap: 4px; }
.summary-item span:first-child { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

/* ── Print Media Query ── */
@media print {
    body { background: white; }
    .topbar, .filter-container, .btn-print, .btn-logout { display: none !important; }
    .main { padding: 0; }
    .card { box-shadow: none; border: 1px solid #cbd5e1; }
}

/* ── Tombol Detail ── */
.btn-detail { background: #e0f2fe; color: #0369a1; border: none; padding: 5px 12px; border-radius: 7px; cursor: pointer; font-size: 12.5px; font-family: 'Poppins', sans-serif; font-weight: 600; transition: 0.15s; }
.btn-detail:hover { background: #0ea5e9; color: white; }

/* ── Modal Detail Transaksi ── */
.modal-overlay { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,23,42,0.45); backdrop-filter: blur(8px); align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 16px; padding: 28px; width: 100%; max-width: 520px; box-shadow: 0 16px 48px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 18px; font-weight: 700; color: #1e3a5f; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.detail-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px dashed #f1f5f9; font-size: 13.5px; }
.detail-row .label { color: #64748b; }
.detail-row .val   { font-weight: 600; color: #1e293b; }
.detail-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13.5px; }
.detail-table th { background: #f1f5f9; padding: 9px 12px; color: #475569; font-weight: 600; text-align: left; border-radius: 6px; border:none;}
.detail-table td { padding: 9px 12px; border-bottom: 1px solid #f8fafc; }
.detail-table td:last-child { text-align: right; font-weight: 600; color: #0369a1; }
.detail-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; color: #1e3a5f; padding: 12px 0 0; border-top: 2px solid #1e3a5f; margin-top: 10px; }
.btn-tutup { width: 100%; padding: 11px; margin-top: 20px; border-radius: 10px; background: #1e3a5f; color: white; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
.btn-tutup:hover { background: #2563eb; }
</style>
</head>
<body>

<div class="topbar">
    <div class="topbar-brand">
        <div class="badge-admin"><i class="fa-solid fa-shield-halved"></i> MODE ADMIN</div>
        <h1><i class="fa-solid fa-gauge-high" style="margin-right:7px"></i>Dashboard Admin</h1>
        <p>Toko Elektronik Surya Makmur</p>
    </div>

    <nav class="topbar-nav">
        <a href="admin_dashboard.php" class="nav-btn"><i class="fa-solid fa-users"></i> Kelola Kasir</a>
        <a href="admin_barang.php" class="nav-btn"><i class="fa-solid fa-box"></i> Data Barang</a>
        <a href="admin_laporan.php" class="nav-btn active"><i class="fa-solid fa-chart-line"></i> Laporan</a>
    </nav>

    <div class="topbar-user">
        <div class="user-info" style="text-align:right">
            <div class="name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
            <div class="role">Administrator</div>
        </div>
        <div class="avatar"><i class="fa-solid fa-user-shield"></i></div>
        <a href="../process/logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>

<div class="main">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-file-invoice-dollar" style="color:#2563eb"></i>&nbsp; Laporan Transaksi Penjualan</h2>
            <button class="btn btn-success btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </button>
        </div>

        <div class="filter-container">
            <form method="GET" action="admin_laporan.php" class="filter-form">
                <div class="form-group">
                    <label>Periode Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>" required>
                </div>
                <div class="form-group">
                    <label>Periode Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>" required>
                </div>
                <div class="form-group">
                    <label>Pilih Kasir (Opsional)</label>
                    <select name="id_kasir" class="form-control">
                        <option value="">-- Semua Kasir --</option>
                        <?php foreach ($kasirList as $k): ?>
                            <option value="<?= $k['id_kasir'] ?>" <?= $filterKasir == $k['id_kasir'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kasir']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
                </button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px; text-align:center;">No</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal Pembelian</th>
                        <th>Nama Kasir</th>
                        <th style="text-align:center;">Item</th>
                        <th style="text-align:right;">Total Belanja</th>
                        <th style="text-align:center;">Aksi</th> </tr>
                </thead>
                <tbody>
                <?php if (empty($laporan)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">
                            <i class="fa-solid fa-folder-open" style="font-size:32px; margin-bottom:10px; display:block;"></i>
                            Tidak ada transaksi yang cocok dengan filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($laporan as $i => $trx): ?>
                    <tr>
                        <td style="text-align:center; font-weight:600; color:#94a3b8;"><?= $i + 1 ?></td>
                        <td><span class="kode-badge"><?= htmlspecialchars($trx['kode_transaksi']) ?></span></td>
                        <td><?= date('d M Y H:i', strtotime($trx['tanggal'])) ?></td>
                        <td><i class="fa-regular fa-user" style="color:#64748b; margin-right:5px;"></i> <?= htmlspecialchars($trx['nama_kasir'] ?? 'Kasir Dihapus') ?></td>
                        <td style="text-align:center;"><?= $trx['jml_item'] ?> <span style="color:#94a3b8; font-size:12px;">item</span></td>
                        <td style="text-align:right; font-weight:600; color:#0369a1;">Rp <?= number_format($trx['total'], 0, ',', '.') ?></td>
                        
                        <td style="text-align:center;">
                            <button class="btn-detail" onclick="lihatDetail(<?= $trx['id_transaksi'] ?>)">
                                <i class="fa-solid fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($laporan)): ?>
        <div class="summary-bar">
            <div class="summary-item">
                <span>Total Transaksi</span>
                <div style="color:#2563eb; font-size:17px;"><?= number_format($total_transaksi) ?> Transaksi</div>
            </div>
            <div class="summary-item">
                <span>Total Item Terjual</span>
                <div style="color:#d97706; font-size:17px;"><?= number_format($total_item_terjual) ?> Item</div>
            </div>
            <div class="summary-item" style="text-align:right;">
                <span>Total Pendapatan</span>
                <div style="color:#16a34a; font-size:20px;">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalDetail" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-title">
            <i class="fa-solid fa-receipt" style="color:#2563eb"></i>
            Detail Transaksi
        </div>
        <div id="detailBody">Memuat...</div>
        <button class="btn-tutup" onclick="tutupDetail()">Tutup</button>
    </div>
</div>

<script>
// Fungsi untuk memanggil detail barang ke layar popup
function lihatDetail(id) {
    document.getElementById('detailBody').innerHTML = '<p style="text-align:center;padding:20px;color:#64748b;">Memuat detail...</p>';
    document.getElementById('modalDetail').style.display = 'flex';

    fetch('../process/get_detail_transaksi.php?id=' + id)
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('detailBody').innerHTML = '<p style="color:#ef4444;text-align:center;">Gagal memuat data: ' + (data.message || '') + '</p>';
            return;
        }
        const t = data.transaksi;
        const d = data.detail;

        let rows = '';
        d.forEach(item => {
            rows += `<tr>
                <td>${item.nama_barang}</td>
                <td style="text-align:center;">${item.jumlah}</td>
                <td>Rp ${parseInt(item.harga_satuan).toLocaleString('id-ID')}</td>
                <td>Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
            </tr>`;
        });

        const tgl = new Date(t.tanggal);
        const tglFormatted = isNaN(tgl) ? t.tanggal : tgl.toLocaleString('id-ID');

        document.getElementById('detailBody').innerHTML = `
            <div class="detail-row"><span class="label">Kode Transaksi</span><span class="val" style="color:#2563eb;">${t.kode_transaksi}</span></div>
            <div class="detail-row"><span class="label">Kasir</span><span class="val">${t.nama_kasir}</span></div>
            <div class="detail-row"><span class="label">Tanggal</span><span class="val">${tglFormatted}</span></div>
            <table class="detail-table" style="margin-top:16px;">
                <thead><tr><th>Barang</th><th style="text-align:center;">Qty</th><th>Harga Satuan</th><th style="text-align:right;">Subtotal</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <div class="detail-total"><span>TOTAL</span><span>Rp ${parseInt(t.total).toLocaleString('id-ID')}</span></div>
        `;
    })
    .catch(err => {
        document.getElementById('detailBody').innerHTML = '<p style="color:#ef4444;text-align:center;">Terjadi kesalahan koneksi.</p>';
    });
}

function tutupDetail() {
    document.getElementById('modalDetail').style.display = 'none';
}

window.onclick = e => {
    if (e.target === document.getElementById('modalDetail')) tutupDetail();
};
</script>

</body>
</html>