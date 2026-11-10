<?php
require __DIR__.'/config/database.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['name']??'Administrator'); $username=trim($_POST['username']??'admin'); $password=$_POST['password']??'';
 if(strlen($password)<8){$message='Password minimal 8 karakter.';}
 else {
  $hash=password_hash($password,PASSWORD_DEFAULT);
  $stmt=$pdo->prepare("INSERT INTO admins(name,username,password) VALUES(?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password)");
  $stmt->execute([$name,$username,$hash]);
  $message='Admin berhasil dibuat. Demi keamanan, hapus file install.php sekarang.';
 }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page"><form class="login-card" method="post"><a class="brand" href="index.php">⚡ ElectroStore</a><h1>Buat akun admin</h1><p class="muted">Jalankan sekali setelah mengimpor database.</p><?php if($message): ?><div class="success"><?=$message?></div><?php endif; ?><label>Nama admin<input name="name" value="Administrator" required></label><label>Username<input name="username" value="admin" required></label><label>Password (min. 8 karakter)<input type="password" name="password" minlength="8" required></label><button class="btn full">Buat Admin</button></form></body></html>
