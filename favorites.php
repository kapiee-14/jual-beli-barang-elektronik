<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = (int)($_POST['product_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT id FROM products WHERE id=? AND is_active=1');
    $stmt->execute([$productId]);
    if ($stmt->fetch()) {
        $favorites = array_map('intval', $_SESSION['favorites'] ?? []);
        if (in_array($productId, $favorites, true)) {
            $_SESSION['favorites'] = array_values(array_diff($favorites, [$productId]));
            set_flash('success', 'Produk dihapus dari favorit.');
        } else {
            $_SESSION['favorites'][] = $productId;
            $_SESSION['favorites'] = array_values(array_unique(array_map('intval', $_SESSION['favorites'])));
            set_flash('success', 'Produk disimpan ke favorit.');
        }
    }
    header('Location: favorites.php');
    exit;
}

$favoriteIds = array_values(array_unique(array_map('intval', $_SESSION['favorites'] ?? [])));
$products = [];
if ($favoriteIds) {
    $placeholders = implode(',', array_fill(0, count($favoriteIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active=1 AND id IN ($placeholders) ORDER BY FIELD(id, $placeholders)");
    $stmt->execute(array_merge($favoriteIds, $favoriteIds));
    $products = $stmt->fetchAll();
}
$flash = take_flash();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$customer = current_customer();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Favorit | ElectroStore</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/storefront.css">
    <link rel="stylesheet" href="assets/commerce.css">
</head>
<body class="storefront commerce-page">
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
    <nav aria-label="Navigasi utama">
        <a href="index.php">Katalog</a>
        <a class="nav-current" href="favorites.php">Favorit <span class="nav-count"><?=count($favoriteIds)?></span></a>
        <a href="cart.php">Keranjang <span class="nav-count"><?=$cartCount?></span></a>
        <a href="track-order.php">Lacak pesanan</a>
        <a href="<?=$customer ? 'my-orders.php' : 'login.php'?>"><?=$customer ? 'Akun' : 'Masuk'?></a>
    </nav>
</header>
<main class="commerce-main">
    <div class="commerce-heading">
        <span class="eyebrow">DISIMPAN UNTUK NANTI</span>
        <h1>Favorit</h1>
        <p>Kumpulan perangkat yang ingin kamu lihat lagi.</p>
    </div>
    <?php if ($flash): ?><div class="notice"><?=e($flash['message'])?></div><?php endif; ?>
    <?php if (!$products): ?>
        <section class="empty-state">
            <span class="empty-mark" aria-hidden="true">♡</span>
            <h2>Belum ada produk favorit</h2>
            <p>Tekan ikon hati pada produk untuk menyimpannya di sini.</p>
            <a class="btn" href="index.php#produk">Jelajahi produk</a>
        </section>
    <?php else: ?>
        <div class="products favorite-products">
            <?php foreach ($products as $product): ?>
                <article class="product">
                    <div class="product-image"><img src="<?=e($product['image_url'] ?: 'assets/products/laptop.jpg')?>" alt="<?=e($product['name'])?>" loading="lazy"><span class="product-category"><?=e($product['category'])?></span></div>
                    <div class="product-info">
                        <h3><?=e($product['name'])?></h3>
                        <strong class="product-price"><?=rupiah($product['price'])?></strong>
                        <p class="stock-line">Stok tersedia: <?= (int)$product['stock'] ?></p>
                        <div class="favorite-actions">
                            <form method="post" action="cart.php">
                                <?=csrf_field()?>
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?=$product['id']?>">
                                <button class="btn add-cart-button" type="submit" <?=$product['stock'] < 1 ? 'disabled' : ''?>>Tambah ke keranjang</button>
                            </form>
                            <form method="post" action="favorites.php">
                                <?=csrf_field()?>
                                <input type="hidden" name="product_id" value="<?=$product['id']?>">
                                <button class="remove-favorite" type="submit" title="Hapus dari favorit" aria-label="Hapus <?=e($product['name'])?> dari favorit">♥</button>
                            </form>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>