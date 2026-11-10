<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function rupiah($value) { return 'Rp ' . number_format((float)$value, 0, ',', '.'); }
function is_admin() { return !empty($_SESSION['admin_id']); }
function current_customer() { return $_SESSION['customer'] ?? null; }
function require_customer() {
    if (!current_customer()) {
        set_flash('error', 'Silakan masuk ke akun pelanggan terlebih dahulu.');
        header('Location: login.php');
        exit;
    }
}
function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    $token = $_POST['csrf'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Permintaan tidak valid. Muat ulang halaman dan coba lagi.');
    }
}
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function take_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
function require_admin() {
    if (!is_admin()) { header('Location: login.php'); exit; }
}
