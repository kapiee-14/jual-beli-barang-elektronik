<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';
require_customer();

$customer = current_customer();
$stmt = $pdo->prepare("SELECT o.*,
    (SELECT GROUP_CONCAT(CONCAT(oi.product_name, ' × ', oi.quantity) ORDER BY oi.id SEPARATOR ', ')
     FROM order_items oi WHERE oi.order_id=o.id) AS items
    FROM orders o WHERE o.user_id=? ORDER BY o.created_at DESC");
$stmt->execute([$customer['id']]);
$orders = $stmt->fetchAll();
$flash = take_flash();
$favoriteCount = count($_SESSION['favorites'] ?? []);
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun Saya | ElectroStore</title>
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
        <a href="cart.php">Keranjang <span class="nav-count"><?=$cartCount?></span></a>
        <a class="nav-current" href="my-orders.php">Akun</a>
        <form class="logout-form" method="post" action="logout.php"><?=csrf_field()?><button type="submit">Keluar</button></form>
    </nav>
</header>
<main class="commerce-main account-main">
    <div class="account-heading">
        <div class="commerce-heading">
            <span class="eyebrow">AKUN PELANGGAN</span>
            <h1>Halo, <?=e($customer['name'])?>.</h1>
            <p><?=e($customer['email'])?> · <?=e($customer['phone'])?></p>
        </div>
        <a class="btn account-shop" href="index.php#produk">Lanjut belanja <span aria-hidden="true">→</span></a>
    </div>
    <?php if ($flash): ?><div class="notice"><?=e($flash['message'])?></div><?php endif; ?>
    <section class="account-orders">
        <div class="orders-title"><div><span class="eyebrow">RIWAYAT PEMBELIAN</span><h2>Pesanan saya</h2></div><span><?=count($orders)?> pesanan</span></div>
        <?php if (!$orders): ?>
            <div class="empty-state">
                <span class="empty-mark" aria-hidden="true">↗</span>
                <h2>Belum ada pesanan</h2>
                <p>Pesanan yang dibuat saat masuk ke akun ini akan tampil di sini.</p>
                <a class="btn" href="index.php#produk">Jelajahi katalog</a>
            </div>
        <?php else: ?>
            <div class="account-order-list">
                <?php foreach ($orders as $order): ?>
                    <article class="account-order">
                        <div class="account-order-top"><div><small><?=e(date('d M Y, H:i', strtotime($order['created_at'])))?></small><strong><?=e($order['order_code'])?></strong></div><span class="order-status"><?=e($order['status'])?></span></div>
                        <p><?=e($order['items'] ?? 'Rincian produk tidak tersedia')?></p>
                        <div class="account-order-bottom"><strong><?=rupiah($order['total'])?></strong><a href="track-order.php?code=<?=urlencode($order['order_code'])?>">Lihat pelacakan <span aria-hidden="true">→</span></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>