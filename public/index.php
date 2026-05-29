<?php
require_once '../config/auth_check.php';
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
<title>Dashboard Barang - Kasir</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ee 50%, #fef9e7 100%);
    color: #1e293b;
    min-height: 100vh;
}
.container { max-width: 1100px; margin: auto; padding: 28px 30px; }

/* Topbar Kasir */
.topbar { background: linear-gradient(135deg, #1e3a5f, #2563eb); color: white; padding: 14px 30px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.topbar h1 { font-size: 20px; font-weight: 700; }
.topbar p  { font-size: 12px; opacity: 0.75; }
.topbar-nav { display: flex; gap: 10px; margin-left: auto; }
.nav-btn { padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; color: white; border: 1.5px solid rgba(255,255,255,0.3); }
.nav-btn:hover { background: rgba(255,255,255,0.15); }
.nav-btn.active { background: rgba(255,255,255,0.2); border-color: white; }

/* Dashboard Cards */
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
</style>
</head>
<body>
<div class="topbar">
    <div>
        <h1><i class="fa-solid fa-box" style="margin-right:8px"></i>Katalog Barang</h1>
        <p>Toko Elektronik Surya Makmur</p>
    </div>
    
    <div class="topbar-nav">
        <a href="index.php"             class="nav-btn active"><i class="fa-solid fa-box"></i> Katalog Barang</a>
        <a href="transaksi.php"         class="nav-btn"><i class="fa-solid fa-cash-register"></i> Kasir</a>
        <a href="riwayat_transaksi.php" class="nav-btn"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</a>
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
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th style="text-align:left;">Kategori</th>
                    <th style="text-align:left;">Nama Barang</th>
                    <th style="text-align:right; padding-right:20px;">Harga Jual</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="4" style="padding:30px; color:#94a3b8;">Belum ada barang di kategori ini.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['id_barang'] ?></td>
                    <td style="text-align:left;"><span style="background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;"><?= $row['nama_kategori'] ?></span></td>
                    <td class="nama"><?= $row['nama_barang'] ?></td>
                    <td class="harga" style="text-align:right; padding-right:20px;">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const $ = id => document.getElementById(id);
    function filterKategori(id) { window.location.href = id ? 'index.php?kategori=' + id : 'index.php'; }
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