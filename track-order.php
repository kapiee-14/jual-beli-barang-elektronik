<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $code = strtoupper(trim($_POST['order_code'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    if ($code !== '' && $phone !== '') {
        $_SESSION['track_lookup'] = ['code' => $code, 'phone' => $phone];
    } else {
        set_flash('error', 'Masukkan kode pesanan dan nomor telepon.');
    }
    header('Location: track-order.php' . ($code !== '' ? '?code=' . urlencode($code) : ''));
    exit;
}

$requestedCode = strtoupper(trim($_GET['code'] ?? ''));
$lookup = $_SESSION['track_lookup'] ?? [];
$code = $requestedCode !== '' ? $requestedCode : ($lookup['code'] ?? '');
$phone = ($lookup['code'] ?? '') === $code ? ($lookup['phone'] ?? '') : '';
$order = null;
$items = [];
if ($code !== '' && $phone !== '') {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_code=? AND phone=? LIMIT 1');
    $stmt->execute([$code, $phone]);
    $order = $stmt->fetch();
    if ($order) {
        $stmt = $pdo->prepare('SELECT product_name, quantity, unit_price, line_total FROM order_items WHERE order_id=? ORDER BY id');
        $stmt->execute([$order['id']]);
        $items = $stmt->fetchAll();
    }
}
$flash = take_flash();
$favoriteCount = count($_SESSION['favorites'] ?? []);
$cartCount = array_sum($_SESSION['cart'] ?? []);
$customer = current_customer();
$steps = ['Menunggu', 'Diproses', 'Dikirim', 'Selesai'];
$statusIndex = array_search($order['status'] ?? '', $steps, true);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lacak Pesanan | ElectroStore</title>
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
        <a class="nav-current" href="track-order.php">Lacak pesanan</a>
        <a href="<?=$customer ? 'my-orders.php' : 'login.php'?>"><?=$customer ? 'Akun' : 'Masuk'?></a>
    </nav>
</header>
<main class="commerce-main tracking-main">
    <div class="commerce-heading">
        <span class="eyebrow">PANTAU PERJALANAN BARANG</span>
        <h1>Lacak pesanan</h1>
        <p>Masukkan kode pesanan dan nomor telepon yang digunakan saat checkout.</p>
    </div>
    <?php if ($flash): ?><div class="notice <?=$flash['type'] === 'error' ? 'notice-error' : ''?>"><?=e($flash['message'])?></div><?php endif; ?>
    <form class="tracking-form" method="post" action="track-order.php">
        <?=csrf_field()?>
        <label>Kode pesanan<input name="order_code" value="<?=e($code)?>" placeholder="Contoh: ES260101A1B2C3D4" required></label>
        <label>Nomor telepon<input name="phone" type="tel" value="<?=e($phone)?>" autocomplete="tel" required></label>
        <button class="btn" type="submit">Lacak status <span aria-hidden="true">→</span></button>
    </form>
    <?php if ($code !== '' && $phone !== '' && !$order): ?>
        <div class="notice notice-error">Pesanan tidak ditemukan. Periksa kembali kode dan nomor telepon.</div>
    <?php endif; ?>
    <?php if ($order): ?>
        <section class="tracking-result">
            <div class="tracking-title"><div><span class="eyebrow">KODE PESANAN</span><h2><?=e($order['order_code'])?></h2></div><span class="order-status"><?=e($order['status'])?></span></div>
            <?php if ($order['status'] === 'Dibatalkan'): ?>
                <div class="cancelled-status">Pesanan ini dibatalkan. Hubungi toko jika membutuhkan bantuan.</div>
            <?php else: ?>
                <ol class="status-steps">
                    <?php foreach ($steps as $index => $step): ?>
                        <li class="<?=$statusIndex !== false && $index <= $statusIndex ? 'step-done' : ''?>">
                            <span class="step-marker"><?=$index < ($statusIndex === false ? 0 : $statusIndex) ? '✓' : $index + 1?></span>
                            <span><?=$step?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
            <div class="tracking-details">
                <div><span>Penerima</span><strong><?=e($order['customer_name'])?></strong></div>
                <div><span>Alamat</span><strong><?=nl2br(e($order['shipping_address'] ?? ''))?></strong></div>
                <div><span>Tanggal</span><strong><?=e(date('d M Y, H:i', strtotime($order['created_at'])))?></strong></div>
            </div>
            <div class="tracking-lines">
                <?php foreach ($items as $item): ?>
                    <div><span><?=e($item['product_name'])?> × <?= (int)$item['quantity'] ?></span><strong><?=rupiah($item['line_total'])?></strong></div>
                <?php endforeach; ?>
                <?php if (!$items): ?><p>Rincian produk tidak tersedia untuk pesanan lama.</p><?php endif; ?>
                <div class="tracking-total"><span>Total pesanan</span><strong><?=rupiah($order['total'])?></strong></div>
            </div>
        </section>
    <?php endif; ?>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>