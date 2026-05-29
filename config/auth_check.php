<?php
/**
 * auth_check.php
 * ──────────────────────────────────────────────────────────
 * Include file ini di BAGIAN PALING ATAS setiap halaman
 * yang ingin diproteksi (sebelum require database.php).
 *
 * Cara pakai:
 *   require_once '../config/auth_check.php';
 *   require_once '../config/database.php';
 * ──────────────────────────────────────────────────────────
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['kasir_id'])) {
    header("Location: ../public/login.php");
    exit;
}

// Variabel global siap pakai di halaman yang meng-include file ini:
// $_SESSION['kasir_id']       → ID kasir yang login
// $_SESSION['kasir_nama']     → Nama kasir
// $_SESSION['kasir_username'] → Username kasir
