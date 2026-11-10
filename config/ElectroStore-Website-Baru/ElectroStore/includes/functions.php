<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function rupiah($value) { return 'Rp ' . number_format((float)$value, 0, ',', '.'); }
function is_admin() { return !empty($_SESSION['admin_id']); }
function require_admin() {
    if (!is_admin()) { header('Location: login.php'); exit; }
}
