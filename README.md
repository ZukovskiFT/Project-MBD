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
│   ├── auth_check.php             ← Proteksi halaman login/session
│   └── database.php               ← Konfigurasi koneksi database
│
├── process/
│   ├── auth_login.php
│   ├── auth_register.php
│   ├── logout.php
│   ├── insert.php
│   ├── insert_kategori.php
│   ├── insert_transaksi.php
│   ├── update.php
│   ├── update_kategori.php
│   ├── delete.php
│   ├── delete_kategori.php
│   ├── batal_transaksi.php
│   └── get_detail_transaksi.php
│
├── public/
│   ├── index.php                   ← Halaman utama / dashboard
│   ├── register.php
│   ├── login.php
│   ├── transaksi.php
│   ├── riwayat_transaksi.php
│   ├── tambah.php
│   ├── tambah_kategori.php
│   ├── edit.php
│   └── hapus.php
│
├── tokoelektroniksuryamakmur.sql   ← File SQL siap import
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

## Fitur Sistem

1. Autentikasi Pengguna
- Register akun kasir
- Login pengguna
- Logout
- Proteksi halaman menggunakan session
- Redirect otomatis jika belum login

2. Manajemen Barang
- Menampilkan daftar barang
- Tambah barang
- Edit barang
- Hapus barang
- Relasi barang dengan kategori

3. Manajemen Kategori
- Menampilkan daftar kategori
- Tambah kategori
- Edit kategori
- Hapus kategori

4. Sistem Transaksi
- Menambahkan transaksi penjualan
- Menghitung total transaksi
- Menyimpan detail transaksi ke database
- Membatalkan transaksi

5. Riwayat Transaksi
- Menampilkan seluruh riwayat transaksi
- Menampilkan detail transaksi per transaksi
- Melihat barang yang dibeli pada setiap transaksi

6. Dashboard
- Statistik jumlah barang per kategori
- Ringkasan data barang
- Tampilan dashboard setelah login

7. UI/UX
- Modal form popup tanpa reload penuh
- AJAX request untuk beberapa aksi
- Notifikasi toast setelah aksi berhasil
- Responsive layout sederhana berbasis web

---

## Konsep Database yang Diterapkan

- ERD & Skema Relasional
- Query JOIN (barang ↔ kategori)
- Relasi Foreign Key dengan CASCADE
- Koneksi PDO MySQL dengan error handling

---

## Lisensi

Project ini dibuat untuk keperluan akademik sebagai tugas Mata Kuliah Manajemen Basis Data (MBD).

---