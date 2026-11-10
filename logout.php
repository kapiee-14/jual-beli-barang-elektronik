<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    unset($_SESSION['customer'], $_SESSION['track_lookup']);
    session_regenerate_id(true);
    set_flash('success', 'Kamu sudah keluar dari akun.');
}
header('Location: index.php');
exit;