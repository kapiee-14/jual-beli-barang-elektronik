<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php'; require_admin();
$products=(int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders=(int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$users=(int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$revenue=(float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='Selesai'")->fetchColumn();
$latest=$pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard Admin</title><link rel="stylesheet" href="../assets/style.css"></head><body class="admin-body">
<aside class="sidebar"><a class="brand" href="../index.php">⚡ ElectroStore</a><small>ADMIN PANEL</small><a class="active" href="dashboard.php">▦ Dashboard</a><a href="products.php">▣ Produk</a><a href="orders.php">▤ Transaksi</a><a href="users.php">♙ Pengguna</a><a href="logout.php">↪ Keluar</a></aside>
<main class="admin-main"><div class="admin-top"><div><span class="eyebrow">OVERVIEW</span><h1>Dashboard</h1><p class="muted">Selamat datang, <?=e($_SESSION['admin_name'])?>.</p></div><a class="btn" href="../index.php" target="_blank">Lihat Website ↗</a></div>
<div class="stats"><div class="stat"><span>Total Produk</span><b><?=$products?></b><small>Produk terdaftar</small></div><div class="stat"><span>Total Pesanan</span><b><?=$orders?></b><small>Semua transaksi</small></div><div class="stat"><span>Pengguna</span><b><?=$users?></b><small>Akun pelanggan</small></div><div class="stat"><span>Pendapatan selesai</span><b><?=rupiah($revenue)?></b><small>Pesanan berstatus selesai</small></div></div>
<section class="panel"><div class="panel-head"><h2>Pesanan terbaru</h2><a href="orders.php">Lihat semua →</a></div><div class="table-wrap"><table><thead><tr><th>Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead><tbody><?php foreach($latest as $o): ?><tr><td><?=e($o['order_code'])?></td><td><?=e($o['customer_name'])?></td><td><?=rupiah($o['total'])?></td><td><span class="status"><?=e($o['status'])?></span></td><td><?=e($o['created_at'])?></td></tr><?php endforeach; ?><?php if(!$latest): ?><tr><td colspan="5">Belum ada pesanan.</td></tr><?php endif; ?></tbody></table></div></section>
</main></body></html>
