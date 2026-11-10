<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$cart = $_SESSION['cart'] ?? [];
$customer = current_customer();
if (!$cart) {
    set_flash('error', 'Keranjang masih kosong.');
    header('Location: cart.php');
    exit;
}

$error = '';
$customerName = $customer['name'] ?? '';
$phone = $customer['phone'] ?? '';
$shippingAddress = '';
$shippingFee = 15000;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $customerName = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');

    if (strlen($customerName) < 2 || strlen($customerName) > 120) {
        $error = 'Nama penerima harus berisi 2 sampai 120 karakter.';
    } elseif (!preg_match('/^[0-9+().\-\s]{8,30}$/', $phone)) {
        $error = 'Nomor telepon tidak valid.';
    } elseif (strlen($shippingAddress) < 10 || strlen($shippingAddress) > 2000) {
        $error = 'Alamat harus berisi 10 sampai 2.000 karakter.';
    } else {
        try {
            $ids = array_map('intval', array_keys($cart));
            sort($ids, SORT_NUMERIC);
            $pdo->beginTransaction();
            $lockedItems = [];
            $subtotal = 0;

            foreach ($ids as $id) {
                $quantity = max(1, (int)$cart[$id]);
                $stmt = $pdo->prepare('SELECT id, name, price, stock FROM products WHERE id=? AND is_active=1 FOR UPDATE');
                $stmt->execute([$id]);
                $product = $stmt->fetch();
                if (!$product || (int)$product['stock'] < $quantity) {
                    throw new RuntimeException('Stok salah satu produk berubah. Perbarui keranjang sebelum checkout.');
                }
                $product['quantity'] = $quantity;
                $product['line_total'] = round((float)$product['price'] * $quantity, 2);
                $subtotal += $product['line_total'];
                $lockedItems[] = $product;
            }

            $orderCode = 'ES' . date('ymd') . strtoupper(bin2hex(random_bytes(4)));
            $total = $subtotal + $shippingFee;
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_code, customer_name, phone, shipping_address, total, status) VALUES (?, ?, ?, ?, ?, ?, 'Menunggu')");
            $stmt->execute([$customer['id'] ?? null, $orderCode, $customerName, $phone, $shippingAddress, $total]);
            $orderId = (int)$pdo->lastInsertId();

            $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?, ?)');
            $decreaseStock = $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');
            foreach ($lockedItems as $item) {
                $insertItem->execute([$orderId, $item['id'], $item['name'], $item['quantity'], $item['price'], $item['line_total']]);
                $decreaseStock->execute([$item['quantity'], $item['id'], $item['quantity']]);
                if ($decreaseStock->rowCount() !== 1) {
                    throw new RuntimeException('Stok berubah saat checkout. Silakan coba kembali.');
                }
            }

            $pdo->commit();
            unset($_SESSION['cart']);
            $_SESSION['track_lookup'] = ['code' => $orderCode, 'phone' => $phone];
            set_flash('success', 'Pesanan berhasil dibuat. Simpan kode pesanan untuk pelacakan.');
            header('Location: track-order.php?code=' . urlencode($orderCode));
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Pesanan belum dapat diproses. Periksa kembali keranjang dan coba lagi.';
        }
    }
}

$ids = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE is_active=1 AND id IN ($placeholders)");
$stmt->execute($ids);
$items = [];
$subtotal = 0;
foreach ($stmt->fetchAll() as $item) {
    $item['quantity'] = min(max(1, (int)$cart[$item['id']]), (int)$item['stock']);
    $item['line_total'] = (float)$item['price'] * $item['quantity'];
    $items[] = $item;
    $subtotal += $item['line_total'];
}
$total = $subtotal + $shippingFee;
$favoriteCount = count($_SESSION['favorites'] ?? []);
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | ElectroStore</title>
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
        <span class="eyebrow">SATU LANGKAH LAGI</span>
        <h1>Checkout</h1>
        <p>Isi alamat penerima untuk menyelesaikan pesanan.</p>
    </div>
    <?php if ($error): ?><div class="notice notice-error"><?=e($error)?></div><?php endif; ?>
    <div class="checkout-layout">
        <form class="checkout-form" method="post" action="checkout.php">
            <?=csrf_field()?>
            <section class="checkout-section">
                <h2>Informasi penerima</h2>
                <label>Nama lengkap<input name="customer_name" value="<?=e($customerName)?>" autocomplete="name" maxlength="120" required></label>
                <label>Nomor telepon<input name="phone" type="tel" value="<?=e($phone)?>" autocomplete="tel" maxlength="30" required></label>
                <label>Alamat lengkap<textarea name="shipping_address" autocomplete="street-address" rows="4" maxlength="2000" required><?=e($shippingAddress)?></textarea></label>
            </section>
            <section class="checkout-section payment-note">
                <span class="payment-icon" aria-hidden="true">✓</span>
                <div><h2>Pembayaran</h2><p>Bayar di tempat (COD). Pembayaran online belum diaktifkan.</p></div>
            </section>
            <button class="btn place-order-button" type="submit">Buat pesanan <span aria-hidden="true">→</span></button>
        </form>
        <aside class="order-summary checkout-summary">
            <h2>Pesananmu</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-item"><span><?=e($item['name'])?> × <?= (int)$item['quantity'] ?></span><strong><?=rupiah($item['line_total'])?></strong></div>
            <?php endforeach; ?>
            <div class="summary-divider"></div>
            <div><span>Subtotal</span><strong><?=rupiah($subtotal)?></strong></div>
            <div><span>Pengiriman</span><strong><?=rupiah($shippingFee)?></strong></div>
            <p>Total pembayaran</p>
            <strong class="summary-total"><?=rupiah($total)?></strong>
        </aside>
    </div>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>