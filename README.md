# Project-MBD
=======
# Sistem Informasi Manajemen Kasir — Toko Elektronik Surya Makmur

Project Mata Kuliah Manajemen Basis Data  
Program Studi Informatika — Universitas Tanjungpura

---

## Anggota Kelompok

1. Dzulfikar Nuril Al-Amien — D1041241028 (Ketua)
2. Zukovski Tangguh Dirgantara — D1041241032 (Anggota)
3. Dhimas Dwi Prasetyo — D1041241088 (Anggota)

---

## Deskripsi Project

Sistem ini merupakan implementasi Sistem Informasi Manajemen Kasir pada Toko Elektronik Surya Makmur, dibuat untuk membantu proses pengelolaan data barang agar lebih cepat, akurat, dan terstruktur dalam database.

Sistem ini juga mendukung pengelolaan data barang, kategori, transaksi kasir, riwayat transaksi, serta autentikasi pengguna, seperti login dan register.

---

## Link Sistem yang Sudah ter-Hosting

http://projectmbd.infinityfreeapp.com/

---

## Teknologi yang Digunakan

- PHP Native
- MySQL Database
- XAMPP (Apache + MySQL)
- HTML
- CSS
- JavaScript
- PDO MySQL
- Session Authentication

---

## Struktur Folder Project

```
Project_MBD/
│
├── config/
│   ├── auth_check.php
│   ├── auth_check_admin.php
│   └── database.php
│
├── process/
│   ├── admin_delete_kasir.php
│   ├── admin_insert_kasir.php
│   ├── admin_update_kasir.php
│   │
│   ├── auth_login.php
│   ├── logout.php
│   │
│   ├── insert.php
│   ├── update.php
│   ├── delete.php
│   │
│   ├── insert_kategori.php
│   ├── update_kategori.php
│   ├── delete_kategori.php
│   │
│   ├── insert_transaksi.php
│   ├── batal_transaksi.php
│   └── get_detail_transaksi.php
│
├── public/
│   ├── index.php
│   ├── login.php
│   ├── transaksi.php
│   ├── riwayat_transaksi.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   ├── tambah_kategori.php
│   │
│   ├── admin_dashboard.php
│   ├── admin_barang.php
│   └── admin_laporan.php
│
├── tokoelektroniksuryamakmur.sql   ← File SQL siap import
├── LICENSE   ← File SQL siap import
└── README.md
```

---

## Cara Menjalankan di Localhost (XAMPP)

### Langkah 1 — Install & Jalankan XAMPP

1. Download XAMPP di https://www.apachefriends.org
2. Install dan buka **XAMPP Control Panel**
3. Klik **Start** pada modul **Apache** dan **MySQL**
4. Pastikan keduanya berstatus **Running** (hijau)

---

### Langkah 2 — Letakkan Project di Folder htdocs

1. Ekstrak zip project ini
2. Copy folder `Project_MBD` ke dalam folder htdocs XAMPP:
   ```
   C:\xampp\htdocs\Project_MBD\
   ```
   Sehingga struktur lengkapnya menjadi:
   ```
   C:\xampp\htdocs\Project_MBD\public\index.php
   C:\xampp\htdocs\Project_MBD\config\database.php
   ... dst
   ```

---

### Langkah 3 — Import Database via phpMyAdmin

1. Buka browser, akses:
   ```
   http://localhost/phpmyadmin
   ```
2. Klik tab **Import** di menu atas
3. Klik **Choose File**, lalu pilih file:
   ```
   Project_MBD/tokoelektroniksuryamakmur.sql
   ```
4. Pastikan format diset ke **SQL**
5. Klik tombol **Import** / **Go**
6. Jika berhasil, akan muncul pesan hijau:
   > *Import has been successfully finished.*

   Database `tokoelektroniksuryamakmur` beserta tabel dan data contoh akan otomatis terbuat.

---

### Langkah 4 — Periksa Konfigurasi Koneksi

Buka file `config/database.php` dan pastikan isinya sudah sesuai:

```php
$host     = "localhost";
$db       = "tokoelektroniksuryamakmur";
$username = "root";
$password = "";   // Kosongkan jika XAMPP default (tanpa password)
```

> Jika MySQL kamu menggunakan password, isi bagian `$password` sesuai password yang diset.

---

### Langkah 5 — Jalankan Aplikasi

Buka browser dan akses:

```
http://localhost/Project_MBD/public/index.php
```

Aplikasi siap digunakan.

---

## Isi Database (Data Contoh)

File SQL sudah menyertakan **10 kategori** dan **100 data barang** contoh:

| Kategori              | Jumlah Barang  |
|-----------------------|:--------------:|
| Kipas Angin           | 10             |
| AC                    | 10             |
| Kulkas                | 10             |
| Mesin Cuci            | 10             |
| Televisi              | 10             |
| Audio & Speaker       | 10             |
| Smartphone & Tablet   | 10             |
| Laptop & Komputer     | 10             |
| Peralatan Dapur       | 10             |
| Akesoris Elektronik   | 10             |
---

# Fitur Sistem

1. Authentication
- Login Admin
- Login Kasir
- Logout
- Session Authentication
- Proteksi halaman berdasarkan role user

---

2. Manajemen Barang
- Menampilkan daftar barang
- Tambah barang
- Edit barang
- Hapus barang
- Filter berdasarkan kategori

---

3. Manajemen Kategori
- Tambah kategori
- Edit kategori
- Hapus kategori

---

4. Manajemen Transaksi
- Input transaksi penjualan
- Detail transaksi
- Riwayat transaksi
- Batalkan transaksi

---

5. Dashboard Admin
- Kelola data kasir
- Tambah akun kasir
- Edit akun kasir
- Hapus akun kasir
- Monitoring data barang

---

6. Laporan
- Rekap laporan transaksi
- Histori transaksi penjualan

---

# Konsep Database yang Diterapkan

Project ini menerapkan konsep:

- Entity Relationship Diagram (ERD)
- Relasi antar tabel
- Foreign Key Constraint
- Query JOIN
- Aggregation Query
- CRUD Database
- Session-based Authentication
- Role-based Access Control (Admin & Kasir)

---

# Akun Login

Gunakan akun yang tersedia di database setelah import.

Contoh:

## Admin
```text
Username : admin
Password : admin
```

## Kasir
```text
Username : kasir
Password : kasir
```

> Sesuaikan dengan data pada tabel `kasir` di database apabila berbeda.

---

## Konsep Database yang Diterapkan

- Entity Relationship Diagram (ERD)
- Relasi antar tabel
- Query JOIN
- Aggregation Query
- CRUD Database
- Session-based Authentication
- Role-based Access Control (Admin & Kasir)
- Relasi Foreign Key dengan CASCADE

---

## Lisensi

Project ini dibuat untuk keperluan akademik sebagai tugas Mata Kuliah Manajemen Basis Data (MBD).

---
