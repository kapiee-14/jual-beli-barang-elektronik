<?php
require __DIR__.'/config/database.php';
require __DIR__.'/includes/functions.php';
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM products WHERE is_active=1";
$params = [];
if ($q !== '') { $sql .= " AND (name LIKE ? OR category LIKE ?)"; $params = ["%$q%", "%$q%"]; }
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ElectroStore - Toko Elektronik</title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php">⚡ ElectroStore</a><nav><a href="index.php">Beranda</a><a href="#produk">Produk</a><a href="admin/login.php">Admin</a></nav></header>
<section class="hero"><div><span class="eyebrow">TOKO ELEKTRONIK TERPERCAYA</span><h1>Teknologi pilihan,<br><em>harga bersahabat.</em></h1><p>Temukan laptop, smartphone, aksesori, dan perangkat elektronik pilihan.</p><a class="btn" href="#produk">Jelajahi Produk</a></div><div class="hero-art">⌁<span>⚡</span><small>TECH<br>FOR YOU</small></div></section>
<main class="container" id="produk"><div class="section-head"><div><span class="eyebrow">KOLEKSI KAMI</span><h2>Produk elektronik</h2></div><form class="search" method="get"><input name="q" value="<?=e($q)?>" placeholder="Cari produk atau kategori..."><button class="btn" type="submit">Cari</button></form></div>
<div class="products"><?php foreach($products as $p): ?><article class="product"><div class="product-image"><?php if($p['image_url']): ?><img src="<?=e($p['image_url'])?>" alt="<?=e($p['name'])?>"><?php else: ?><span>▣</span><?php endif; ?></div><div class="product-info"><small><?=e($p['category'])?></small><h3><?=e($p['name'])?></h3><strong><?=rupiah($p['price'])?></strong><p class="muted">Stok: <?= (int)$p['stock'] ?></p></div></article><?php endforeach; ?></div>
<?php if (!$products): ?><p class="empty">Produk belum tersedia atau tidak ditemukan.</p><?php endif; ?>
</main><footer>© <?=date('Y')?> ElectroStore · Belanja elektronik jadi mudah.</footer></body></html>
