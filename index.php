<?php
require __DIR__.'/config/database.php';
require __DIR__.'/includes/functions.php';
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sql = "SELECT * FROM products WHERE is_active=1";
$params = [];
if ($q !== '') {
	$sql .= " AND (name LIKE ? OR category LIKE ? OR description LIKE ?)";
	$params = ["%$q%", "%$q%", "%$q%"];
}
if ($category !== '') {
	$sql .= " AND category = ?";
	$params[] = $category;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params); $products = $stmt->fetchAll();
$categories = $pdo->query("SELECT DISTINCT category FROM products WHERE is_active=1 AND category IS NOT NULL AND category<>'' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$categoryTiles = $pdo->query("SELECT grouped.category, grouped.product_count, product.image_url
	FROM (SELECT category, COUNT(*) AS product_count, MIN(id) AS first_product_id
		FROM products WHERE is_active=1 AND category IS NOT NULL AND category<>''
		GROUP BY category) AS grouped
	JOIN products AS product ON product.id=grouped.first_product_id
	ORDER BY grouped.product_count DESC, grouped.category ASC LIMIT 6")->fetchAll();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$favoriteCount = count($_SESSION['favorites'] ?? []);
$customer = current_customer();
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Temukan laptop, smartphone, dan perangkat elektronik pilihan di ElectroStore.">
	<title>ElectroStore | Toko Elektronik</title>
	<link rel="stylesheet" href="assets/style.css">
	<link rel="stylesheet" href="assets/storefront.css">
</head>
<body class="storefront">
<header class="topbar">
	<a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
	<nav aria-label="Navigasi utama">
		<a href="index.php">Beranda</a>
		<a href="#produk">Katalog</a>
		<a href="favorites.php">Favorit <span class="nav-count"><?= $favoriteCount ?></span></a>
		<a href="cart.php">Keranjang <span class="nav-count"><?= $cartCount ?></span></a>
		<a href="track-order.php">Lacak pesanan</a>
		<a href="<?=$customer ? 'my-orders.php' : 'login.php'?>"><?=$customer ? 'Akun' : 'Masuk'?></a>
		<a class="nav-admin" href="admin/login.php">Admin <span aria-hidden="true">↗</span></a>
	</nav>
</header>

<section class="hero">
	<img class="hero-photo" src="assets/products/laptop.jpg" alt="Laptop untuk bekerja dan belajar">
	<div class="hero-copy">
		<span class="eyebrow">PERANGKAT PILIHAN, HARGA BERSAHABAT</span>
		<h1>Temukan teknologi yang pas untukmu.</h1>
		<p>Dari perangkat kerja sampai hiburan, jelajahi koleksi elektronik yang siap menemani hari-harimu.</p>
		<a class="btn hero-cta" href="#produk">Jelajahi katalog <span aria-hidden="true">↓</span></a>
	</div>
	<span class="hero-index" aria-hidden="true">EST. 2026&nbsp; / &nbsp;TECH FOR EVERY DAY</span>
</section>

<section class="category-explorer" aria-label="Jelajahi kategori produk">
	<div class="category-heading">
		<div><span class="eyebrow">MULAI DARI SINI</span><h2>Jelajahi kategori.</h2></div>
		<a href="#produk">Lihat semua produk <span aria-hidden="true">↘</span></a>
	</div>
	<div class="category-tiles">
		<?php foreach ($categoryTiles as $tile): ?>
			<a class="category-tile" href="index.php?category=<?=urlencode($tile['category'])?>#produk">
				<img src="<?=e($tile['image_url'] ?: 'assets/products/laptop.jpg')?>" alt="" loading="lazy">
				<span class="category-scrim" aria-hidden="true"></span>
				<strong><?=e($tile['category'])?></strong>
				<small><?=$tile['product_count']?> produk</small>
				<span class="category-arrow" aria-hidden="true">↗</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

<main class="container catalog" id="produk">
	<div class="section-head">
		<div>
			<span class="eyebrow">PILIHAN ELEKTRONIK</span>
			<h2>Belanja sesuai kebutuhan.</h2>
		</div>
		<p class="result-count"><strong><?=count($products)?></strong> produk ditemukan</p>
	</div>

	<form class="catalog-controls" method="get" action="index.php">
		<label class="search-control">
			<span class="sr-only">Cari nama atau deskripsi produk</span>
			<input type="search" name="q" value="<?=e($q)?>" placeholder="Cari laptop, kamera, aksesori...">
			<button class="btn search-button" type="submit">Cari <span aria-hidden="true">⌕</span></button>
		</label>
		<label class="category-control">
			<span class="sr-only">Filter kategori produk</span>
			<select name="category" onchange="this.form.submit()">
				<option value="">Semua kategori</option>
				<?php foreach ($categories as $itemCategory): ?>
					<option value="<?=e($itemCategory)?>" <?=$category === $itemCategory ? 'selected' : ''?>><?=e($itemCategory)?></option>
				<?php endforeach; ?>
			</select>
		</label>
		<?php if ($q !== '' || $category !== ''): ?>
			<a class="clear-filters" href="index.php">Hapus filter</a>
		<?php endif; ?>
	</form>

	<?php if ($products): ?>
		<div class="products">
			<?php foreach ($products as $product): ?>
				<article class="product">
					<div class="product-image">
						<?php if ($product['image_url']): ?>
							<img src="<?=e($product['image_url'])?>" alt="<?=e($product['name'])?>" loading="lazy">
						<?php else: ?>
							<span aria-hidden="true">▣</span>
						<?php endif; ?>
						<span class="product-category"><?=e($product['category'])?></span>
						<form class="favorite-form" method="post" action="favorites.php">
							<?=csrf_field()?>
							<input type="hidden" name="action" value="toggle">
							<input type="hidden" name="product_id" value="<?=$product['id']?>">
							<button type="submit" title="Simpan ke favorit" aria-label="Simpan <?=e($product['name'])?> ke favorit">♡</button>
						</form>
					</div>
					<div class="product-info">
						<h3><?=e($product['name'])?></h3>
						<strong class="product-price"><?=rupiah($product['price'])?></strong>
						<p class="stock-line"><span class="stock-dot"></span> Stok tersedia: <?= (int)$product['stock'] ?></p>
						<form class="add-cart-form" method="post" action="cart.php">
							<?=csrf_field()?>
							<input type="hidden" name="action" value="add">
							<input type="hidden" name="product_id" value="<?=$product['id']?>">
							<button class="btn add-cart-button" type="submit" <?=$product['stock'] < 1 ? 'disabled' : ''?>>Tambah ke keranjang <span aria-hidden="true">+</span></button>
						</form>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<p class="empty">Produk tidak ditemukan. Coba kata kunci atau kategori lain.</p>
	<?php endif; ?>
</main>

<footer class="store-footer">
	<a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
	<span>Teknologi pilihan untuk keseharianmu.</span>
	<span>© <?=date('Y')?> ElectroStore</span>
</footer>
</body>
</html>
