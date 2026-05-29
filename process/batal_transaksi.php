<?php
header('Content-Type: application/json');
// Kolom 'status' tidak ada dalam skema database transaksi.
// Fitur pembatalan tidak tersedia pada skema database ini.
echo json_encode([
    'success' => false,
    'message' => 'Fitur batal transaksi tidak didukung pada skema database ini.'
]);
