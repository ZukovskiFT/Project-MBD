<?php
require_once '../config/auth_check_admin.php';
require_once '../config/database.php';

$filterKategori = $_GET['kategori'] ?? null;

$query = "SELECT barang.*, kategori.nama_kategori FROM barang JOIN kategori ON barang.id_kategori = kategori.id_kategori";
if ($filterKategori) {
    $stmt = $conn->prepare($query . " WHERE barang.id_kategori = :id");
    $stmt->execute([':id' => $filterKategori]);
} else {
    $stmt = $conn->query($query);
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $conn->query("SELECT COUNT(*) FROM barang")->fetchColumn();

$kategoriData = $conn->query("
    SELECT k.id_kategori, k.nama_kategori, COUNT(b.id_barang) as total
    FROM kategori k LEFT JOIN barang b ON b.id_kategori = k.id_kategori
    GROUP BY k.id_kategori
")->fetchAll(PDO::FETCH_ASSOC);

usort($kategoriData, fn($a, $b) => $b['total'] - $a['total']);
$top3 = array_slice($kategoriData, 0, 2);
$sisanya = array_slice($kategoriData, 2);
$warna = ['kat-color-0','kat-color-1','kat-color-2','kat-color-3','kat-color-4','kat-color-5','kat-color-6','kat-color-7'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Barang – Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ee 50%, #fef9e7 100%);
    min-height: 100vh; color: #1e293b;
}

/* ── Tombol Gradient untuk Modal ── */
.btn-gradient-blue {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    color: white;
    padding: 10px 24px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    transition: 0.2s;
    text-decoration: none; /* Biar kalau pakai tag <a> tidak ada garis bawah */
    display: inline-block;
}

.btn-gradient-red {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    padding: 10px 24px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    transition: 0.2s;
}

.btn-gradient-blue:hover, .btn-gradient-red:hover { opacity: 0.9; transform: translateY(-2px); }

/* ── Topbar Admin ── */
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
.topbar-brand h1 { font-size: 17px; font-weight: 700; color: white; line-height: 1.2; margin:0;}
.topbar-brand p  { font-size: 11.5px; color: rgba(255,255,255,0.6); margin:0;}

.topbar-nav { display: flex; gap: 6px; margin-left: 10px; }

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
    border-left: 1px solid rgba(255,255,255,0.2); padding-left: 14px; margin-left: 10px;
}
.user-info .name { font-size: 13px; font-weight: 600; color: white; line-height: 1.2; }
.user-info .role { font-size: 11px; color: rgba(255,255,255,0.6); }
.avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.35);
    display: flex; align-items: center; justify-content: center; color: white; font-size: 15px;
}
.btn-logout {
    display: flex; align-items: center; gap: 6px;
    background: rgba(239,68,68,0.15); border: 1.5px solid rgba(239,68,68,0.4);
    color: white; text-decoration: none; padding: 7px 13px; font-family: 'Poppins', sans-serif;
    border-radius: 10px; font-size: 13px; font-weight: 600; transition: background 0.2s;
}
.btn-logout:hover { background: rgba(239,68,68,0.3); }

/* Tombol Dropdown Tambah */
.dropdown { position: relative; display: inline-block; }
.dropdown-menu { display: none; position: absolute; right: 0; top: 110%; background: white; border: 1px solid #e2e8f0; border-radius: 12px; min-width: 190px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 100; overflow: hidden; }
.dropdown-menu a { display: flex; align-items: center; gap: 10px; padding: 11px 16px; color: #334155; text-decoration: none; font-size: 14px; transition: 0.2s; }
.dropdown-menu a:hover { background: #f1f5f9; }
.dropdown.open .dropdown-menu { display: block; }
.btn-primary-tambah { 
    background: linear-gradient(135deg, #1e3a5f, #2563eb); 
    color: white; 
    font-weight: 600; 
    border: none; 
    padding: 10px 18px; 
    border-radius: 9px; 
    font-size: 13.5px; 
    cursor: pointer; 
    font-family: 'Poppins', sans-serif; 
    transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
    box-shadow: 0 4px 12px rgba(37,99,235,0.25);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-primary-tambah:hover { 
    opacity: 0.92; 
    transform: translateY(-2px); 
    box-shadow: 0 6px 16px rgba(37,99,235,0.35);
}
.btn-primary-tambah:active { 
    transform: translateY(0); 
}

/* Komponen Halaman */
.container { max-width: 1100px; margin: auto; padding: 28px 30px; }
.dashboard { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
.card { padding: 20px; border-radius: 16px; color: white; display: flex; align-items: center; gap: 15px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); transition: 0.3s; cursor: pointer; }
.card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,0,0,0.15); }
.card.aktif { outline: 3px solid #1e3a5f; outline-offset: 2px; }
.icon { width: 50px; height: 50px; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; border-radius: 12px; flex-shrink: 0; }
.card-total  { background: linear-gradient(135deg, #f97316, #fb923c); }
.kat-color-0 { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
.kat-color-1 { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.kat-color-2 { background: linear-gradient(135deg, #22c55e, #4ade80); }
.kat-color-3 { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.kat-color-4 { background: linear-gradient(135deg, #ec4899, #f472b6); }
.kat-color-5 { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
.kat-color-6 { background: linear-gradient(135deg, #ef4444, #f87171); }
.kat-color-7 { background: linear-gradient(135deg, #64748b, #94a3b8); }

/* Aksi Kategori */
.card-actions { margin-left: auto; display: flex; flex-direction: column; gap: 5px; opacity: 0; transition: 0.2s; }
.card:hover .card-actions { opacity: 1; }
.card-actions button { background: rgba(255,255,255,0.25); border: none; border-radius: 6px; color: white; width: 26px; height: 26px; cursor: pointer; font-size: 11px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.card-actions button:hover { background: rgba(255,255,255,0.45); }

/* Tabel */
.table-box { background: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
table { width: 100%; border-collapse: collapse; border-radius: 12px; overflow: hidden; }

/* Ukuran header tabel dibesarkan ke 17px dan padding ditambah */
th { 
    background: linear-gradient(135deg, #1e3a5f, #2563eb); 
    padding: 16px 14px; 
    color: white; 
    font-weight: 600; 
    text-align: center; 
    font-size: 17px; 
}

/* Ukuran isi tabel dibesarkan ke 16px dan padding ditambah */
td { 
    padding: 16px 14px; 
    text-align: center; 
    color: #334155; 
    font-size: 16px; 
    border-bottom: 1px solid #f1f5f9;
}

td.nama { text-align: left; font-weight: 400; } 
.harga { color: #0369a1; font-weight: bold; font-size: 16.5px; }

tr:nth-child(even) { background: #f8fafc; }
tr:nth-child(odd)  { background: white; }
tr:hover           { background: #e0f2fe; }

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

/* Modal & Toast CSS sama dengan versi lama */
.modal-overlay { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,23,42,0.4); backdrop-filter: blur(8px); align-items: center; justify-content: center; }
.modal-content { background: white; padding: 30px; border-radius: 16px; width: 100%; max-width: 400px; position: relative; box-shadow: 0 12px 40px rgba(0,0,0,0.15); }
.close-btn { position: absolute; right: 20px; top: 15px; font-size: 24px; color: #94a3b8; cursor: pointer; }
.close-btn:hover { color: #1e293b; }
.form-group { margin-bottom: 15px; text-align:left;}
.form-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #475569; font-weight: 500; }
.form-control { width: 100%; padding: 10px; border-radius: 8px; background: #f8fafc; border: 1.5px solid #e2e8f0; color: #1e293b; font-family: 'Poppins', sans-serif; }
.form-control:focus { outline: none; border-color: #0ea5e9; background: white; }
.toast { position: fixed; bottom: 30px; right: 30px; background: white; border-left: 4px solid #22c55e; color: #1e293b; padding: 14px 20px; border-radius: 10px; font-size: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 9999; opacity: 0; transform: translateY(20px); transition: 0.3s; pointer-events: none; }
.toast.show { opacity: 1; transform: translateY(0); }
.toast.error { border-left-color: #ef4444; }

/* ── Pembungkus Tombol ── */
.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 25px;
}

/* ── Base Style Tombol (Dilebarin) ── */
.btn {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    padding: 11px 36px;        /* Padding ditambah atas-bawah & kiri-kanan biar lebih lebar */
    min-width: 130px;          /* Mengunci lebar minimal tombol agar seimbang dan proporsional */
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    color: white !important;   /* Dipaksa putih karena kedua tombol sekarang pakai gradien gelap */
    text-decoration: none;
}

/* Efek Hover Prosedural */
.btn:hover {
    opacity: 0.95;
    transform: translateY(-2px);
}
.btn:active {
    transform: translateY(0);
}

/* ── Tombol Batal (Merah Gradien) ── */
.btn-ghost {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
}

/* ── Tombol Ya, Hapus (Biru Gradien) ── */
.btn-danger {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

.btn-danger:hover {
    background-color: #f87171;
}

.btn-danger:active {
    transform: translateY(0);
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
        <a href="admin_dashboard.php" class="nav-btn"><i class="fa-solid fa-users"></i> Kelola Kasir</a>
        <a href="admin_barang.php" class="nav-btn active"><i class="fa-solid fa-box"></i> Data Barang</a>
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

<div class="container">
    <div class="dashboard">
        <div class="card card-total <?= !$filterKategori ? 'aktif' : '' ?>" onclick="filterKategori('')">
            <div class="icon"><i class="fa-solid fa-box"></i></div>
            <div><?= $total ?><br><small>Total Barang</small></div>
        </div>

        <?php foreach ($top3 as $i => $kat): ?>
        <div class="card <?= $warna[$i % count($warna)] ?> <?= $filterKategori == $kat['id_kategori'] ? 'aktif' : '' ?>"
            onclick="filterKategori('<?= $kat['id_kategori'] ?>')">
            <div class="icon"><i class="fa-solid fa-tags"></i></div>
            <div><?= $kat['total'] ?><br><small><?= $kat['nama_kategori'] ?></small></div>
            <div class="card-actions" onclick="event.stopPropagation()">
                <button onclick="bukaEditKat(<?= $kat['id_kategori'] ?>, '<?= $kat['nama_kategori'] ?>')" title="Ubah"><i class="fa-solid fa-gear"></i></button>
                <button onclick="bukaHapusKat(<?= $kat['id_kategori'] ?>, '<?= $kat['nama_kategori'] ?>')" title="Hapuwws"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (!empty($sisanya)): ?>
        <div class="card kat-color-7" style="position:relative;" onclick="event.stopPropagation(); toggleKatLain()">
            <div class="icon"><i class="fa-solid fa-layer-group"></i></div>
            <div>+<?= count($sisanya) ?><br><small>Kategori Lainnya ▾</small></div>

            <div id="dropdownKatLain" style="display:none; position:absolute; top:110%; left:0; right:0;
                background:white; border:1px solid #e2e8f0; border-radius:10px; z-index:200;
                box-shadow:0 8px 24px rgba(0,0,0,0.12); overflow:hidden;">
                <?php foreach ($sisanya as $j => $kat): ?>
                <div style="display:flex; align-items:center; justify-content:space-between;
                            padding:10px 14px; cursor:pointer; transition:0.2s; font-size:13px; color:#334155;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''"
                    onclick="filterKategori('<?= $kat['id_kategori'] ?>')">
                    <span>
                        <i class="fa-solid fa-tags" style="color:<?= ['#0ea5e9','#8b5cf6','#22c55e','#f59e0b','#ec4899'][$j % 5] ?>; margin-right:8px;"></i>
                        <?= $kat['nama_kategori'] ?> (<?= $kat['total'] ?>)
                    </span>
                    <span onclick="event.stopPropagation()" style="display:flex; gap:5px;">
                        <button onclick="bukaEditKat(<?= $kat['id_kategori'] ?>, '<?= $kat['nama_kategori'] ?>')"
                                style="background:#e0f2fe; border:none; border-radius:5px; color:#0369a1; width:24px; height:24px; cursor:pointer; font-size:10px;">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                        <button onclick="bukaHapusKat(<?= $kat['id_kategori'] ?>, '<?= $kat['nama_kategori'] ?>')"
                                style="background:#fee2e2; border:none; border-radius:5px; color:#ef4444; width:24px; height:24px; cursor:pointer; font-size:10px;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="table-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 18px; font-weight: 700; color: #1e3a5f; margin: 0;">
                <i class="fa-solid fa-table-list" style="color:#2563eb"></i>&nbsp; Daftar Barang
            </h2>
            
            <div class="dropdown" id="dropdownTambah" style="position:relative;">
                <button class="btn-primary-tambah" onclick="toggleDropdown()">
                    <i class="fa-solid fa-plus"></i> Tambah Data ▾
                </button>
                <div class="dropdown-menu" style="right:0; top:110%; margin-top:8px;">
                    <a href="#" onclick="tutupDropdown(); bukaModal('tambah.php')"><i class="fa-solid fa-box" style="color:#0ea5e9"></i> Tambah Barang</a>
                    <a href="#" onclick="tutupDropdown(); bukaModal('tambah_kategori.php')"><i class="fa-solid fa-tags" style="color:#22c55e"></i> Tambah Kategori</a>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:50px; text-align:center;">ID</th>
                    <th>Kategori</th>
                    <th>Nama Barang</th>
                    <th style="text-align:right;">Harga Jual</th>
                    <th style="text-align:center; width: 140px;">Pengaturan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                <tr><td colspan="5" style="text-align:center; padding:30px; color:#94a3b8;">Belum ada barang di kategori ini.</td></tr>
                <?php else: ?>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td style="text-align:center;"><?= $row['id_barang'] ?></td>
                    <td><span style="background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;"><?= $row['nama_kategori'] ?></span></td>
                    <td class="nama"><?= $row['nama_barang'] ?></td>
                    <td class="harga" style="text-align:right;">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <button class="btn-icon-edit" onclick="bukaModal('edit.php?id_barang=<?= $row['id_barang'] ?>')" title="Edit Barang"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-icon-hapus" onclick="bukaModal('hapus.php?id_barang=<?= $row['id_barang'] ?>')" title="Hapus Barang"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalOverlay" class="modal-overlay">
    <div id="modalContent" class="modal-content"></div>
</div>

<div id="modalEditKat" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.4); backdrop-filter:blur(6px); z-index:1001; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:30px; width:360px; position:relative; box-shadow:0 12px 40px rgba(0,0,0,0.15);">
        <span class="close-btn" onclick="tutupEditKat()">×</span>
        <h3 style="margin:0 0 20px; color:#1e3a5f;"><i class="fa-solid fa-gear" style="color:#0ea5e9"></i> Ubah Nama Kategori</h3>
        <form action="../process/update_kategori.php" method="POST">
            <input type="hidden" name="id_kategori" id="editKatId">
            <div class="form-group">
                <label>Nama Kategori</label>
                <input type="text" name="nama_kategori" id="editKatNama" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-edit" style="width:100%; padding:10px; font-size:14px;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<div id="modalHapusKat" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.4); backdrop-filter:blur(6px); z-index:1001; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:30px; width:360px; text-align:center; box-shadow:0 12px 40px rgba(0,0,0,0.15);">
        <div style="width:56px; height:56px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
            <i class="fa-solid fa-trash" style="color:#ef4444; font-size:22px;"></i>
        </div>
        <h3 style="margin:0 0 8px; color:#1e293b;">Hapus Kategori?</h3>
        <p id="hapusKatNama" style="color:#64748b; font-size:13px; margin:0 0 1.5rem;"></p>
        <div class="modal-actions">
            <button type="button" class="btn btn-ghost" onclick="tutupModal('modalHapus')">Batal</button>
            <button type="submit" class="btn btn-danger" id="btn_hapus_submit">
                <i class="fa-solid fa-trash"></i> Ya, Hapus
            </button>
        </div>
    </div>
</div>

<div id="toast"></div>

<script>
    const $ = id => document.getElementById(id);

    function toggleDropdown() { $('dropdownTambah').classList.toggle('open'); }
    function tutupDropdown()  { $('dropdownTambah').classList.remove('open'); }
    document.addEventListener('click', e => { if (!$('dropdownTambah').contains(e.target)) tutupDropdown(); });

    function filterKategori(id) { window.location.href = id ? 'admin_barang.php?kategori=' + id : 'admin_barang.php'; }

    function bukaModal(url) {
        $('modalOverlay').style.display = 'flex';
        $('modalContent').innerHTML = '<p style="text-align:center;padding:20px;color:#64748b">Memuat data...</p>';
        fetch(url).then(r => r.text()).then(html => { $('modalContent').innerHTML = html; });
    }
    function tutupModal() { $('modalOverlay').style.display = 'none'; }
    window.onclick = e => { if (e.target === $('modalOverlay')) tutupModal(); };

    function bukaEditKat(id, nama) { $('editKatId').value = id; $('editKatNama').value = nama; $('modalEditKat').style.display = 'flex'; }
    function tutupEditKat()        { $('modalEditKat').style.display = 'none'; }

    function bukaHapusKat(id, nama) {
        $('hapusKatNama').innerHTML = 'Kategori <strong style="color:#0369a1">' + nama + '</strong> dan semua barangnya akan dihapus permanen.';
        $('linkHapusKat').href = '../process/delete_kategori.php?id=' + id;
        $('modalHapusKat').style.display = 'flex';
    }
    function tutupHapusKat() { $('modalHapusKat').style.display = 'none'; }

    function tampilToast(pesan, tipe = 'sukses') {
        const t = $('toast');
        t.textContent = pesan;
        t.className = 'toast' + (tipe === 'error' ? ' error' : '');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }

    window.onload = () => {
        const s = new URLSearchParams(window.location.search).get('status');
        if (s === 'updated')  tampilToast('Berhasil diperbarui.');
        if (s === 'deleted')  tampilToast('Berhasil dihapus.');
        if (s === 'inserted') tampilToast('Berhasil ditambahkan.');
        if (s) window.history.replaceState(null, null, window.location.pathname);
    };

    function toggleKatLain() {
        const dd = $('dropdownKatLain');
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', e => {
        const dd = $('dropdownKatLain');
        if (dd && !dd.closest('.card').contains(e.target)) dd.style.display = 'none';
    });
</script>
</body>
</html>