<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'add') {
        $stmt = $pdo->prepare('SELECT id, stock FROM products WHERE id=? AND is_active=1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if ($product && (int)$product['stock'] > 0) {
            $currentQuantity = (int)($_SESSION['cart'][$productId] ?? 0);
            $_SESSION['cart'][$productId] = min((int)$product['stock'], $currentQuantity + max(1, (int)($_POST['quantity'] ?? 1)));
            set_flash('success', 'Produk ditambahkan ke keranjang.');
        } else {
            set_flash('error', 'Produk tidak tersedia atau stok habis.');
        }
    } elseif ($action === 'update') {
        $quantity = max(0, (int)($_POST['quantity'] ?? 0));
        $stmt = $pdo->prepare('SELECT stock FROM products WHERE id=? AND is_active=1');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        if (!$product || $quantity === 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = min($quantity, (int)$product['stock']);
        }
        set_flash('success', 'Keranjang diperbarui.');
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$productId]);
        set_flash('success', 'Produk dihapus dari keranjang.');
    }

    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;
if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, category, price, stock, image_url FROM products WHERE is_active=1 AND id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $item) {
        $item['quantity'] = min((int)$cart[$item['id']], (int)$item['stock']);
        $item['subtotal'] = (float)$item['price'] * $item['quantity'];
        $items[] = $item;
        $total += $item['subtotal'];
    }
}
$flash = take_flash();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$favoriteCount = count($_SESSION['favorites'] ?? []);
$customer = current_customer();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang | ElectroStore</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/storefront.css">
    <link rel="stylesheet" href="assets/commerce.css">
</head>
<body class="storefront commerce-page">
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
    <nav aria-label="Navigasi utama">
        <a href="index.php">Katalog</a>
        <a href="favorites.php">Favorit <span class="nav-count"><?=$favoriteCount?></span></a>
        <a class="nav-current" href="cart.php">Keranjang <span class="nav-count"><?=$cartCount?></span></a>
        <a href="track-order.php">Lacak pesanan</a>
        <a href="<?=$customer ? 'my-orders.php' : 'login.php'?>"><?=$customer ? 'Akun' : 'Masuk'?></a>
    </nav>
</header>
<main class="commerce-main">
    <div class="commerce-heading">
        <span class="eyebrow">BELANJAANMU</span>
        <h1>Keranjang</h1>
        <p>Periksa barang dan jumlahnya sebelum lanjut ke pembayaran.</p>
    </div>
    <?php if ($flash): ?><div class="notice <?=$flash['type'] === 'error' ? 'notice-error' : ''?>"><?=e($flash['message'])?></div><?php endif; ?>
    <?php if (!$items): ?>
        <section class="empty-state">
            <span class="empty-mark" aria-hidden="true">＋</span>
            <h2>Keranjang masih kosong</h2>
            <p>Pilih produk yang kamu suka dan barangnya akan muncul di sini.</p>
            <a class="btn" href="index.php#produk">Lihat katalog</a>
        </section>
    <?php else: ?>
        <div class="cart-layout">
            <section class="cart-items" aria-label="Barang di keranjang">
                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <img src="<?=e($item['image_url'] ?: 'assets/products/laptop.jpg')?>" alt="<?=e($item['name'])?>">
                        <div class="cart-item-main">
                            <small><?=e($item['category'])?></small>
                            <h2><?=e($item['name'])?></h2>
                            <strong><?=rupiah($item['price'])?></strong>
                            <span class="cart-stock">Stok tersedia: <?= (int)$item['stock'] ?></span>
                        </div>
                        <div class="cart-item-actions">
                            <form method="post" action="cart.php" class="quantity-form">
                                <?=csrf_field()?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?=$item['id']?>">
                                <label class="sr-only" for="quantity-<?=$item['id']?>">Jumlah <?=e($item['name'])?></label>
                                <input id="quantity-<?=$item['id']?>" name="quantity" type="number" min="0" max="<?= (int)$item['stock'] ?>" value="<?=$item['quantity']?>">
                                <button type="submit">Ubah</button>
                            </form>
                            <form method="post" action="cart.php">
                                <?=csrf_field()?>
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?=$item['id']?>">
                                <button class="text-action" type="submit">Hapus</button>
                            </form>
                        </div>
                        <strong class="cart-subtotal"><?=rupiah($item['subtotal'])?></strong>
                    </article>
                <?php endforeach; ?>
            </section>
            <aside class="order-summary">
                <h2>Ringkasan</h2>
                <div><span><?=count($items)?> jenis barang</span><strong><?=rupiah($total)?></strong></div>
                <div><span>Pengiriman</span><strong>Dihitung saat checkout</strong></div>
                <p>Total sementara</p>
                <strong class="summary-total"><?=rupiah($total)?></strong>
                <a class="btn checkout-button" href="checkout.php">Lanjut checkout <span aria-hidden="true">→</span></a>
                <a class="continue-shopping" href="index.php#produk">Lanjut belanja</a>
            </aside>
        </div>
    <?php endif; ?>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>