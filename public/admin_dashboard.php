<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

// ── Statistik ──────────────────────────────────────────────
$totalKasir  = $conn->query("SELECT COUNT(*) FROM kasir")->fetchColumn();
$totalBarang = $conn->query("SELECT COUNT(*) FROM barang")->fetchColumn();

$bulanIni = date('Y-m');
$stmtTrx  = $conn->prepare(
    "SELECT COUNT(*) AS jml, COALESCE(SUM(total), 0) AS total
     FROM transaksi WHERE DATE_FORMAT(tanggal,'%Y-%m') = ?"
);
$stmtTrx->execute([$bulanIni]);
$st = $stmtTrx->fetch(PDO::FETCH_ASSOC);
$totalTrxBulan  = $st['jml'];
$pendapatanBulan = $st['total'];

// ── Daftar kasir ───────────────────────────────────────────
$kasirList = $conn->query(
    "SELECT k.id_kasir, k.nama_kasir, k.username,
            COUNT(t.id_transaksi) AS jumlah_transaksi
     FROM kasir k
     LEFT JOIN transaksi t ON t.id_kasir = k.id_kasir
     GROUP BY k.id_kasir
     ORDER BY k.id_kasir ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// ── Status notifikasi ──────────────────────────────────────
$status = $_GET['status'] ?? '';
$toastMap = [
    'inserted'  => ['success', 'fa-circle-check',       'Kasir baru berhasil ditambahkan.'],
    'updated'   => ['success', 'fa-circle-check',       'Data kasir berhasil diperbarui.'],
    'deleted'   => ['success', 'fa-circle-check',       'Kasir berhasil dihapus.'],
    'duplicate' => ['error',   'fa-circle-exclamation', 'Username sudah digunakan, pilih yang lain.'],
    'has_trx'   => ['error',   'fa-triangle-exclamation','Kasir ini memiliki riwayat transaksi dan tidak dapat dihapus.'],
    'error'     => ['error',   'fa-circle-exclamation', 'Terjadi kesalahan sistem, coba lagi.'],
];
$toast = $toastMap[$status] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin – Toko Elektronik Surya Makmur</title>
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
.nav-btn {
    display: flex; align-items: center; gap: 7px; padding: 8px 14px;
    border-radius: 10px; color: white; text-decoration: none;
    font-size: 13px; font-weight: 600; transition: 0.2s;
    border: 1.5px solid rgba(255,255,255,0.3);
}
.nav-btn:hover  { background: rgba(255,255,255,0.15); }
.nav-btn.active { background: rgba(255,255,255,0.2); border-color: white;}

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
    border-radius: 10px; font-size: 13px; font-weight: 600; transition: background 0.2s;
}
.btn-logout:hover { background: rgba(239,68,68,0.3); }

/* ── Main ── */
.main { padding: 28px; max-width: 1280px; margin: 0 auto; }

/* ── Stats (Desain Baru Menyerupai Dashboard Kasir) ── */
.stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 15px; margin-bottom: 28px;
}
.stat-card {
    border-radius: 16px; padding: 20px; color: white;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1); 
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.15); }
.stat-icon {
    width: 48px; height: 48px; background: rgba(255,255,255,0.25);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.stat-info .val { font-size: 22px; font-weight: 700; line-height: 1.2; margin-bottom: 2px; }
.stat-info .val.sm { font-size: 16px; margin-top: 3px; }
.stat-info .lbl { font-size: 12px; opacity: 0.9; font-weight: 400; margin-top: 2px; }

/* Background Warna Gradient */
.bg-blue   { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
.bg-green  { background: linear-gradient(135deg, #22c55e, #4ade80); }
.bg-orange { background: linear-gradient(135deg, #f97316, #fb923c); }
.bg-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

/* ── Card ── */
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

/* ── Table ── */
.table-wrap { 
    overflow-x: auto; 
    border-radius: 12px; 
}
table { width: 100%; border-collapse: collapse; }
thead tr { background: transparent; }

th {
    background: linear-gradient(135deg, #1e3a5f, #2563eb); 
    padding: 16px 14px; 
    color: white; 
    font-weight: 600; 
    text-align: left; 
    font-size: 17px; 
    border: none;
}

td { 
    padding: 16px 14px; 
    font-size: 16px; 
    color: #334155; 
    border-bottom: 1px solid #f1f5f9; 
    vertical-align: middle; 
    text-align: center;
}

tbody tr:nth-child(even) { background: #f8fafc; }
tbody tr:nth-child(odd)  { background: white; }
tbody tr:last-child td { border-bottom: none; }
tbody tr:hover { background: #e0f2fe; }

/* ── Buttons ── */
.btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600;
    cursor: pointer; border: none; font-family: 'Poppins', sans-serif;
    text-decoration: none; transition: opacity 0.2s, transform 0.15s;
}
.btn:hover  { opacity: 0.88; transform: translateY(-1px); }
.btn:active { transform: translateY(0); }
.btn-primary { background: linear-gradient(135deg,#1e3a5f,#2563eb); color:white; box-shadow:0 3px 10px rgba(37,99,235,.25); }
.btn-success { background: linear-gradient(135deg,#16a34a,#22c55e); color:white; box-shadow:0 3px 10px rgba(22,163,74,.25); }
.btn-warning { background: linear-gradient(135deg,#d97706,#f59e0b); color:white; box-shadow:0 3px 10px rgba(217,119,6,.25); }
.btn-danger  { background: linear-gradient(135deg,#dc2626,#ef4444); color:white; box-shadow:0 3px 10px rgba(220,38,38,.25); }
.btn-ghost   { background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0; }

/* ── Tombol Ikon Edit & Hapus (Gradient) ── */
.btn-icon-edit { 
    background: linear-gradient(135deg, #0ea5e9, #0284c7); /* Biru gradient cerah */
    color: white; 
    padding: 8px 12px; 
    border-radius: 8px; 
    border: none; 
    cursor: pointer; 
    transition: all 0.2s ease; 
    font-size: 13px; 
    box-shadow: 0 4px 10px rgba(14, 165, 233, 0.25); /* Bayangan biru */
}
.btn-icon-hapus { 
    background: linear-gradient(135deg, #ef4444, #dc2626); 
    color: white; 
    padding: 8px 12px; 
    border-radius: 8px; 
    border: none; 
    cursor: pointer; 
    transition: all 0.2s ease; 
    font-size: 13px; 
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
}
.btn-icon-edit:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(14, 165, 233, 0.35); }
.btn-icon-hapus:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(220, 38, 38, 0.35); }

/* ── Modal ── */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,0.55); z-index: 900;
    align-items: center; justify-content: center; padding: 20px;
}
.modal-overlay.aktif { display: flex; animation: fIn 0.2s ease; }
@keyframes fIn { from { opacity:0; } to { opacity:1; } }

.modal-box {
    background: white; border-radius: 22px; padding: 32px 30px;
    width: 100%; max-width: 450px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.28);
    animation: sUp 0.3s cubic-bezier(0.16,1,0.3,1);
}
@keyframes sUp { from { opacity:0; transform:translateY(24px) scale(0.97); } to { opacity:1; transform:none; } }

.modal-box h2 { font-size: 18px; font-weight: 700; margin-bottom: 22px; display:flex; align-items:center; gap:9px; }
.close-btn {
    float: right; font-size: 22px; cursor: pointer; color: #94a3b8;
    background: none; border: none; font-family: inherit; line-height: 1;
}
.close-btn:hover { color: #ef4444; }

/* ── Form ── */
.form-group { margin-bottom: 16px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:7px; }
.form-control {
    width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 14px; font-family: 'Poppins', sans-serif; color: #1e293b; background: #f8fafc;
    outline: none; transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus { border-color: #2563eb; background: white; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
.input-hint { font-size: 11.5px; color: #94a3b8; margin-top: 5px; }

/* ── Modal actions ── */
.modal-actions { display:flex; gap:10px; margin-top:22px; }
.modal-actions .btn { flex: 1; justify-content: center; }

/* ── Badge ── */
.badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px;
}
.badge-blue   { background: #eff6ff; color: #1d4ed8; }
.badge-gray   { background: #f1f5f9; color: #64748b; }

/* ── Toast ── */
.toast {
    position: fixed; top: 86px; right: 24px; z-index: 9999;
    background: white; border-radius: 12px; padding: 14px 18px 14px 16px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.15);
    display: flex; align-items: center; gap: 10px;
    min-width: 280px; max-width: 400px;
    border-left: 4px solid #22c55e;
    animation: slideR 0.3s ease, fadeO 0.4s ease 3.5s forwards;
}
.toast.error { border-left-color: #ef4444; }
@keyframes slideR { from { opacity:0; transform:translateX(60px); } to { opacity:1; transform:none; } }
@keyframes fadeO  { to   { opacity:0; transform:translateX(60px); } }
.toast .t-icon { font-size: 18px; flex-shrink: 0; }
.toast.success .t-icon { color: #22c55e; }
.toast.error   .t-icon { color: #ef4444; }
.toast p { font-size: 13.5px; font-weight: 500; color: #1e293b; }

/* ── Responsive ── */
@media (max-width: 900px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px) {
    .stats-grid { grid-template-columns: 1fr; }
    .topbar { padding: 12px 16px; }
    .main { padding: 16px; }
    .topbar-nav { display: none; }
}
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
        <a href="admin_dashboard.php" class="nav-btn active"><i class="fa-solid fa-users"></i> Kelola Kasir</a>
        <a href="admin_barang.php" class="nav-btn"><i class="fa-solid fa-box"></i> Data Barang</a>
        <a href="admin_laporan.php" class="nav-btn"><i class="fa-solid fa-chart-line"></i> Laporan</a>
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

    <div class="stats-grid">
        <div class="stat-card bg-blue">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-info">
                <div class="val"><?= number_format($totalKasir) ?></div>
                <div class="lbl">Total Kasir Terdaftar</div>
            </div>
        </div>
        <div class="stat-card bg-green">
            <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="stat-info">
                <div class="val"><?= number_format($totalTrxBulan) ?></div>
                <div class="lbl">Transaksi Bulan Ini</div>
            </div>
        </div>
        <div class="stat-card bg-orange">
            <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <div class="val sm">Rp <?= number_format($pendapatanBulan, 0, ',', '.') ?></div>
                <div class="lbl">Pendapatan Bulan Ini</div>
            </div>
        </div>
        <div class="stat-card bg-purple">
            <div class="stat-icon"><i class="fa-solid fa-box-open"></i></div>
            <div class="stat-info">
                <div class="val"><?= number_format($totalBarang) ?></div>
                <div class="lbl">Total Produk</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-user-tie" style="color:#2563eb"></i>&nbsp; Data Kasir</h2>
            <button class="btn btn-primary" onclick="bukaModal('modalTambah')">
                <i class="fa-solid fa-user-plus"></i> Tambah Kasir
            </button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px; text-align:center;">No</th>
                        <th>Nama Kasir</th>
                        <th>Username</th>
                        <th style="text-align:center">Transaksi</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($kasirList)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:36px; color:#94a3b8;">
                        <i class="fa-solid fa-users-slash" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                        Belum ada data kasir terdaftar.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($kasirList as $i => $k): ?>
                <tr>
                    <td style="text-align:center; color:#64748b; font-weight:500;">
                        <?= $i + 1 ?>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:11px;">
                            <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#dbeafe,#bfdbfe); display:flex; align-items:center; justify-content:center; color:#1d4ed8; font-size:15px; flex-shrink:0;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <strong style="color:#1e293b;"><?= htmlspecialchars($k['nama_kasir']) ?></strong>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:monospace; background:#f0f9ff; color:#0369a1; padding:4px 10px; border-radius:7px; font-size:13px; font-weight:600;">
                            @<?= htmlspecialchars($k['username']) ?>
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <span class="badge badge-blue">
                            <i class="fa-solid fa-receipt"></i>
                            <?= $k['jumlah_transaksi'] ?> transaksi
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:8px; justify-content:center;">
                            <button class="btn-icon-edit"
                                    onclick="bukaEdit(<?= $k['id_kasir'] ?>, '<?= addslashes($k['nama_kasir']) ?>', '<?= addslashes($k['username']) ?>')" title="Edit Kasir">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn-icon-hapus"
                                    onclick="bukaHapus(<?= $k['id_kasir'] ?>, '<?= addslashes($k['nama_kasir']) ?>', <?= $k['jumlah_transaksi'] ?>)" title="Hapus Kasir">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div></div><div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
        <button class="close-btn" onclick="tutupModal('modalTambah')">&times;</button>
        <h2 style="color:#16a34a;"><i class="fa-solid fa-user-plus"></i> Tambah Kasir Baru</h2>

        <form action="../process/admin_insert_kasir.php" method="POST">
            <div class="form-group">
                <label>Nama Lengkap Kasir</label>
                <input type="text" name="nama_kasir" class="form-control"
                       placeholder="Contoh: Budi Santoso" required minlength="3">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control"
                       placeholder="Contoh: budi_kasir" required minlength="3"
                       pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, underscore">
                <div class="input-hint">Hanya huruf, angka, dan underscore (_)</div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Minimal 6 karakter" required minlength="6">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="tutupModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-check"></i> Simpan Kasir
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <button class="close-btn" onclick="tutupModal('modalEdit')">&times;</button>
        <h2 style="color:#d97706;"><i class="fa-solid fa-pen-to-square"></i> Edit Data Kasir</h2>

        <form action="../process/admin_update_kasir.php" method="POST">
            <input type="hidden" name="id_kasir" id="edit_id">
            <div class="form-group">
                <label>Nama Lengkap Kasir</label>
                <input type="text" name="nama_kasir" id="edit_nama" class="form-control" required minlength="3">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="edit_username" class="form-control"
                       required minlength="3" pattern="[a-zA-Z0-9_]+" title="Hanya huruf, angka, underscore">
                <div class="input-hint">Hanya huruf, angka, dan underscore (_)</div>
            </div>
            <div class="form-group">
                <label>Password Baru
                    <span style="font-weight:400; color:#94a3b8; font-size:12px;">(kosongkan jika tidak diubah)</span>
                </label>
                <input type="password" name="password" class="form-control"
                       placeholder="Isi untuk ganti password" minlength="6">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="tutupModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-warning">
                    <i class="fa-solid fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="modalHapus">
    <div class="modal-box" style="max-width:400px; text-align:center;">
        <button class="close-btn" onclick="tutupModal('modalHapus')">&times;</button>

        <div style="width:70px; height:70px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <i class="fa-solid fa-trash" style="color:#ef4444; font-size:28px;"></i>
        </div>
        <h2 style="color:#ef4444; display:block; text-align:center; margin-bottom:10px;">Hapus Kasir?</h2>
        <p style="color:#64748b; margin-bottom:14px; line-height:1.7;">
            Anda akan menghapus akun kasir<br>
            <strong id="hapus_nama" style="color:#1e3a5f; font-size:16px;"></strong>
        </p>

        <div id="warn_trx" style="display:none; font-size:12.5px; color:#b45309; background:#fffbeb;
             border:1px solid #fde68a; border-radius:10px; padding:10px 14px; margin-bottom:16px; text-align:left;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Kasir ini memiliki riwayat transaksi. Hapus akan <strong>diblokir</strong> untuk menjaga integritas data laporan.
        </div>

        <form action="../process/admin_delete_kasir.php" method="POST">
            <input type="hidden" name="id_kasir" id="hapus_id">
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="tutupModal('modalHapus')">Batal</button>
                <button type="submit" class="btn btn-danger" id="btn_hapus_submit">
                    <i class="fa-solid fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($toast): ?>
<div class="toast <?= $toast[0] ?>" id="toastEl">
    <i class="fa-solid <?= $toast[1] ?> t-icon"></i>
    <p><?= htmlspecialchars($toast[2]) ?></p>
</div>
<?php endif; ?>

<script>
/* ── Buka/tutup modal ── */
function bukaModal(id) {
    document.getElementById(id).classList.add('aktif');
    document.body.style.overflow = 'hidden';
}
function tutupModal(id) {
    document.getElementById(id).classList.remove('aktif');
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) tutupModal(o.id); });
});

/* ── Isi data modal Edit ── */
function bukaEdit(id, nama, username) {
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_nama').value     = nama;
    document.getElementById('edit_username').value = username;
    bukaModal('modalEdit');
}

/* ── Isi data modal Hapus ── */
function bukaHapus(id, nama, jumlahTrx) {
    document.getElementById('hapus_id').value              = id;
    document.getElementById('hapus_nama').textContent      = nama;
    document.getElementById('warn_trx').style.display      = jumlahTrx > 0 ? 'block' : 'none';
    document.getElementById('btn_hapus_submit').disabled   = jumlahTrx > 0;
    if (jumlahTrx > 0) {
        document.getElementById('btn_hapus_submit').style.opacity = '0.4';
        document.getElementById('btn_hapus_submit').style.cursor  = 'not-allowed';
    } else {
        document.getElementById('btn_hapus_submit').style.opacity = '';
        document.getElementById('btn_hapus_submit').style.cursor  = '';
    }
    bukaModal('modalHapus');
}

/* ── Auto-hilangkan toast ── */
setTimeout(() => { const t = document.getElementById('toastEl'); if (t) t.remove(); }, 4000);
</script>

</body>
</html>