<?php
require_once '../config/auth_check.php';
require_once '../config/database.php';

// ── Filter ──────────────────────────────────────────────────────────────
$filterTgl   = $_GET['tanggal']   ?? '';
$filterKasir = $_GET['id_kasir']  ?? '';

$where  = [];
$params = [];

if ($filterTgl) {
    $where[]  = "DATE(t.tanggal) = :tgl";
    $params[':tgl'] = $filterTgl;
}
if ($filterKasir) {
    $where[]  = "t.id_kasir = :id_kasir";
    $params[':id_kasir'] = (int)$filterKasir;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ── Query utama ──────────────────────────────────────────────────────────
$query = "SELECT t.*,
                 k.nama_kasir,
                 COUNT(d.id_barang) AS jml_item,
                 CONCAT('TRX-', DATE_FORMAT(t.tanggal, '%Y%m%d'), '-', LPAD(t.id_transaksi, 4, '0')) AS kode_transaksi
          FROM transaksi t
          JOIN kasir k ON k.id_kasir = t.id_kasir
          LEFT JOIN detail_transaksi d ON d.id_transaksi = t.id_transaksi
          $whereSQL
          GROUP BY t.id_transaksi, k.nama_kasir
          ORDER BY t.tanggal DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Statistik ────────────────────────────────────────────────────────────
$stat = $conn->query("
    SELECT
        COUNT(*)        AS total_trx,
        SUM(total)      AS total_omzet,
        AVG(total)      AS rata_omzet,
        COUNT(DISTINCT id_kasir) AS total_kasir_aktif
    FROM transaksi
")->fetch(PDO::FETCH_ASSOC);

// ── Daftar kasir untuk filter ────────────────────────────────────────────
$kasirList = $conn->query("SELECT id_kasir, nama_kasir FROM kasir ORDER BY nama_kasir")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Riwayat Transaksi – Toko Elektronik Surya Makmur</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ee 50%, #fef9e7 100%); color: #1e293b; min-height: 100vh; }

.topbar { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 14px 30px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.topbar h1 { font-size: 20px; font-weight: 700; }
.topbar p  { font-size: 12px; opacity: 0.75; }
.topbar-nav { margin-left: auto; display: flex; gap: 10px; }
.nav-btn { padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; color: white; border: 1.5px solid rgba(255,255,255,0.3); }
.nav-btn:hover { background: rgba(255,255,255,0.15); }
.nav-btn.active { background: rgba(255,255,255,0.2); border-color: white; }

.container { max-width: 1200px; margin: auto; padding: 28px 30px; }

/* Stat Cards */
.stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 26px; }
.stat-card { border-radius: 16px; padding: 20px; color: white; display: flex; align-items: center; gap: 14px; box-shadow: 0 6px 18px rgba(0,0,0,0.1); }
.stat-card .icon { width: 48px; height: 48px; background: rgba(255,255,255,0.22); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.stat-card .val { font-size: 20px; font-weight: 700; line-height: 1.2; }
.stat-card .lbl { font-size: 11px; opacity: 0.85; font-weight: 400; }
.c1 { background: linear-gradient(135deg, #2563eb, #3b82f6); }
.c2 { background: linear-gradient(135deg, #16a34a, #22c55e); }
.c3 { background: linear-gradient(135deg, #f97316, #fb923c); }
.c4 { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

/* Filter */
.filter-box { background: white; border-radius: 14px; padding: 18px 22px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
.filter-group { flex: 1; min-width: 150px; }
.filter-group label { display: block; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.filter-group input, .filter-group select { width: 100%; padding: 9px 12px; border-radius: 9px; border: 1.5px solid #e2e8f0; font-family: 'Poppins', sans-serif; font-size: 13px; color: #1e293b; background: #f8fafc; }
.filter-group input:focus, .filter-group select:focus { outline: none; border-color: #0ea5e9; background: white; }
.btn-filter { padding: 9px 18px; border-radius: 9px; background: #1e3a5f; color: white; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: 0.2s; }
.btn-filter:hover { background: #2563eb; }
.btn-reset-filter { padding: 9px 14px; border-radius: 9px; background: transparent; border: 1.5px solid #e2e8f0; color: #64748b; font-family: 'Poppins', sans-serif; font-size: 13px; cursor: pointer; }
.btn-reset-filter:hover { background: #f1f5f9; }

/* Table */
.table-box { background: white; border-radius: 16px; padding: 22px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
.table-title { font-size: 16px; font-weight: 700; color: #1e3a5f; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
table { width: 100%; border-collapse: collapse; }
th { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 12px 14px; font-weight: 600; font-size: 13px; text-align: left; }
th:last-child { text-align: center; }
td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #334155; }
tr:hover td { background: #f0f9ff; }
.harga { color: #0369a1; font-weight: 700; }
.td-center { text-align: center; }
.no-data { text-align: center; padding: 40px; color: #94a3b8; }
.no-data i { font-size: 40px; display: block; margin-bottom: 10px; }
.kode-badge { font-weight: 700; color: #1e3a5f; font-size: 12px; letter-spacing: 0.5px; }
.kasir-badge { background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }

.btn-detail { background: #e0f2fe; color: #0369a1; border: none; padding: 5px 12px; border-radius: 7px; cursor: pointer; font-size: 12px; font-family: 'Poppins', sans-serif; font-weight: 600; transition: 0.15s; }
.btn-detail:hover { background: #0ea5e9; color: white; }

/* Modal Detail */
.modal-overlay { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,23,42,0.45); backdrop-filter: blur(8px); align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 16px; padding: 28px; width: 100%; max-width: 520px; box-shadow: 0 16px 48px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 18px; font-weight: 700; color: #1e3a5f; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
.detail-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px dashed #f1f5f9; font-size: 13px; }
.detail-row .label { color: #64748b; }
.detail-row .val   { font-weight: 600; color: #1e293b; }
.detail-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
.detail-table th { background: #f1f5f9; padding: 9px 12px; color: #475569; font-weight: 600; text-align: left; border-radius: 6px; }
.detail-table td { padding: 9px 12px; border-bottom: 1px solid #f8fafc; }
.detail-table td:last-child { text-align: right; font-weight: 600; color: #0369a1; }
.detail-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; color: #1e3a5f; padding: 10px 0 0; border-top: 2px solid #1e3a5f; margin-top: 8px; }
.btn-tutup { width: 100%; padding: 11px; margin-top: 16px; border-radius: 10px; background: #1e3a5f; color: white; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; border: none; cursor: pointer; }
.btn-tutup:hover { background: #2563eb; }

/* Toast */
.toast { position: fixed; bottom: 30px; right: 30px; background: white; border-left: 4px solid #22c55e; color: #1e293b; padding: 14px 20px; border-radius: 10px; font-size: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 9999; opacity: 0; transform: translateY(20px); transition: 0.3s; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.error { border-left-color: #ef4444; }
</style>
</head>
<body>

<div class="topbar">
    <div>
        <h1><i class="fa-solid fa-clock-rotate-left" style="margin-right:8px"></i>Riwayat Transaksi</h1>
        <p>Toko Elektronik Surya Makmur</p>
    </div>
    <div class="topbar-nav">
        <a href="index.php"     class="nav-btn"><i class="fa-solid fa-box"></i> Katalog Barang</a>
        <a href="transaksi.php" class="nav-btn"><i class="fa-solid fa-cash-register"></i> Kasir</a>
        <a href="riwayat_transaksi.php" class="nav-btn active"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a>
    </div>
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

<div class="container">

    <!-- Statistik -->
    <div class="stat-grid">
        <div class="stat-card c1">
            <div class="icon"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <div class="val"><?= number_format($stat['total_trx']) ?></div>
                <div class="lbl">Total Transaksi</div>
            </div>
        </div>
        <div class="stat-card c2">
            <div class="icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div>
                <div class="val" style="font-size:14px;">Rp <?= number_format($stat['total_omzet'] ?? 0, 0, ',', '.') ?></div>
                <div class="lbl">Total Omzet</div>
            </div>
        </div>
        <div class="stat-card c3">
            <div class="icon"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <div class="val" style="font-size:14px;">Rp <?= number_format($stat['rata_omzet'] ?? 0, 0, ',', '.') ?></div>
                <div class="lbl">Rata-rata Transaksi</div>
            </div>
        </div>
        <div class="stat-card c4">
            <div class="icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="val"><?= number_format($stat['total_kasir_aktif'] ?? 0) ?></div>
                <div class="lbl">Kasir Bertugas</div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="">
    <div class="filter-box">
        <div class="filter-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($filterTgl) ?>">
        </div>
        <div class="filter-group">
            <label>Kasir</label>
            <select name="id_kasir">
                <option value="">Semua Kasir</option>
                <?php foreach ($kasirList as $k): ?>
                <option value="<?= $k['id_kasir'] ?>" <?= $filterKasir == $k['id_kasir'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kasir']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        <a href="riwayat_transaksi.php"><button type="button" class="btn-reset-filter">Reset</button></a>
    </div>
    </form>

    <!-- Tabel Riwayat -->
    <div class="table-box">
        <div class="table-title">
            <i class="fa-solid fa-list" style="color:#2563eb"></i>
            Daftar Transaksi
            <span style="margin-left:auto; font-size:13px; color:#64748b; font-weight:400;"><?= count($data) ?> data ditemukan</span>
        </div>

        <?php if (empty($data)): ?>
        <div class="no-data">
            <i class="fa-solid fa-inbox"></i>
            Belum ada transaksi.
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Kasir</th>
                    <th>Tanggal</th>
                    <th style="text-align:center;">Item</th>
                    <th>Total</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><span class="kode-badge"><?= htmlspecialchars($row['kode_transaksi']) ?></span></td>
                    <td><span class="kasir-badge"><i class="fa-solid fa-user" style="margin-right:4px;"></i><?= htmlspecialchars($row['nama_kasir']) ?></span></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                    <td class="td-center"><?= $row['jml_item'] ?> item</td>
                    <td class="harga">Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td class="td-center">
                        <button class="btn-detail" onclick="lihatDetail(<?= $row['id_transaksi'] ?>)">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- Modal Detail Transaksi -->
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

<div id="toast"></div>

<script>
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

function tampilToast(pesan, tipe = 'sukses') {
    const t = document.getElementById('toast');
    t.textContent = pesan;
    t.className = 'toast' + (tipe === 'error' ? ' error' : '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>
