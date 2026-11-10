<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if (current_customer()) {
    header('Location: my-orders.php');
    exit;
}

$error = '';
$flash = take_flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $stmt = $pdo->prepare('SELECT id, name, email, phone, password_hash FROM users WHERE email=? AND is_active=1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && $user['password_hash'] && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['customer'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? ''
        ];
        set_flash('success', 'Berhasil masuk. Selamat datang kembali.');
        header('Location: my-orders.php');
        exit;
    }
    $error = 'Email atau password salah, atau akun tidak aktif.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | ElectroStore</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/storefront.css">
    <link rel="stylesheet" href="assets/commerce.css">
</head>
<body class="storefront commerce-page">
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
    <nav aria-label="Navigasi utama"><a href="index.php">Katalog</a><a href="register.php">Buat akun</a><a href="admin/login.php">Admin</a></nav>
</header>
<main class="auth-main">
    <section class="auth-card">
        <span class="eyebrow">AKUN PELANGGAN</span>
        <h1>Selamat datang kembali.</h1>
        <p>Masuk untuk melihat pesanan dan melanjutkan belanja.</p>
        <?php if ($flash): ?><div class="notice <?=$flash['type'] === 'error' ? 'notice-error' : ''?>"><?=e($flash['message'])?></div><?php endif; ?>
        <?php if ($error): ?><div class="notice notice-error"><?=e($error)?></div><?php endif; ?>
        <form method="post" action="login.php" class="auth-form">
            <?=csrf_field()?>
            <label>Email<input type="email" name="email" autocomplete="email" maxlength="150" required></label>
            <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
            <button class="btn" type="submit">Masuk <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-switch">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </section>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>