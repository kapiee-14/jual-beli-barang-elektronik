<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

if (current_customer()) {
    header('Location: my-orders.php');
    exit;
}

$error = '';
$name = '';
$email = '';
$phone = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (strlen($name) < 2 || strlen($name) > 120) {
        $error = 'Nama harus berisi 2 sampai 120 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $error = 'Alamat email tidak valid.';
    } elseif (!preg_match('/^[0-9+().\-\s]{8,30}$/', $phone)) {
        $error = 'Nomor telepon tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $passwordConfirmation) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password_hash, is_active) VALUES (?, ?, ?, ?, 1)');
            $stmt->execute([$name, $email, $phone, $passwordHash]);
            session_regenerate_id(true);
            $_SESSION['customer'] = [
                'id' => (int)$pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ];
            set_flash('success', 'Akun berhasil dibuat.');
            header('Location: my-orders.php');
            exit;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'Email tersebut sudah terdaftar. Silakan masuk.';
            } else {
                $error = 'Akun belum dapat dibuat. Silakan coba kembali.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | ElectroStore</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/storefront.css">
    <link rel="stylesheet" href="assets/commerce.css">
</head>
<body class="storefront commerce-page">
<header class="topbar">
    <a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a>
    <nav aria-label="Navigasi utama"><a href="index.php">Katalog</a><a href="login.php">Masuk</a><a href="admin/login.php">Admin</a></nav>
</header>
<main class="auth-main">
    <section class="auth-card">
        <span class="eyebrow">MULAI BELANJA</span>
        <h1>Buat akun ElectroStore.</h1>
        <p>Simpan detail pesanan dan lacak pembelian dari satu tempat.</p>
        <?php if ($error): ?><div class="notice notice-error"><?=e($error)?></div><?php endif; ?>
        <form method="post" action="register.php" class="auth-form">
            <?=csrf_field()?>
            <label>Nama lengkap<input name="name" value="<?=e($name)?>" autocomplete="name" maxlength="120" required></label>
            <label>Email<input type="email" name="email" value="<?=e($email)?>" autocomplete="email" maxlength="150" required></label>
            <label>Nomor telepon<input type="tel" name="phone" value="<?=e($phone)?>" autocomplete="tel" maxlength="30" required></label>
            <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
            <label>Ulangi password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
            <button class="btn" type="submit">Buat akun <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk</a></p>
    </section>
</main>
<footer class="store-footer"><a class="brand" href="index.php"><span class="brand-mark">E</span> ElectroStore</a><span>© <?=date('Y')?> ElectroStore</span></footer>
</body>
</html>