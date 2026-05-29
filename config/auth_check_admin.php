<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../public/login.php");
    exit;
}

// Variabel siap pakai:
// $_SESSION['admin_id']       → ID admin
// $_SESSION['admin_nama']     → Nama admin
// $_SESSION['admin_username'] → Username admin