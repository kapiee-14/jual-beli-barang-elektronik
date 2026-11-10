<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php';
if (is_admin()) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=? LIMIT 1");
    $stmt->execute([trim($_POST['username'] ?? '')]); $admin = $stmt->fetch();
    if ($admin && password_verify($_POST['password'] ?? '', $admin['password'])) {
        session_regenerate_id(true); $_SESSION['admin_id']=$admin['id']; $_SESSION['admin_name']=$admin['name'];
        header('Location: dashboard.php'); exit;
    } else $error = 'Username atau password salah.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin</title><link rel="stylesheet" href="../assets/style.css"></head><body class="login-page"><form class="login-card" method="post"><a class="brand" href="../index.php">⚡ ElectroStore</a><h1>Admin Login</h1><p class="muted">Masuk untuk mengelola toko.</p><?php if($error): ?><div class="alert"><?=e($error)?></div><?php endif; ?><label>Username</label><input name="username" required autocomplete="username"><label>Password</label><input type="password" name="password" required autocomplete="current-password"><button class="btn full">Masuk Dashboard</button><a class="back" href="../index.php">← Kembali ke toko</a></form></body></html>
