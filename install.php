<?php
require __DIR__.'/config/database.php';
require __DIR__.'/includes/functions.php';
$adminCount=(int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
$message='';
if($adminCount>0){$message='Akun admin sudah tersedia. Gunakan halaman login admin.';}
if($_SERVER['REQUEST_METHOD']==='POST' && $adminCount===0){
 verify_csrf();
 $name=trim($_POST['name']??'Administrator'); $username=trim($_POST['username']??'admin'); $password=$_POST['password']??'';
 if(strlen($name)<2 || strlen($name)>100){$message='Nama admin harus berisi 2 sampai 100 karakter.';}
 elseif(strlen($username)<3 || strlen($username)>50){$message='Username harus berisi 3 sampai 50 karakter.';}
 elseif(strlen($password)<8){$message='Password minimal 8 karakter.';}
 else {
  $hash=password_hash($password,PASSWORD_DEFAULT);
  $stmt=$pdo->prepare("INSERT INTO admins(name,username,password) VALUES(?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password)");
  $stmt->execute([$name,$username,$hash]);
    $adminCount=1;
  $message='Admin berhasil dibuat. Demi keamanan, hapus file install.php sekarang.';
 }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Setup Admin</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-page"><form class="login-card" method="post"><a class="brand" href="index.php">⚡ ElectroStore</a><h1>Buat akun admin</h1><p class="muted">Gunakan sekali untuk membuat akun administrator pertama.</p><?php if($message): ?><div class="success"><?=e($message)?></div><?php endif; ?><?php if($adminCount===0): ?><?=csrf_field()?><label>Nama admin<input name="name" value="Administrator" maxlength="100" required></label><label>Username<input name="username" value="admin" minlength="3" maxlength="50" required></label><label>Password (min. 8 karakter)<input type="password" name="password" minlength="8" required></label><button class="btn full">Buat Admin</button><?php else: ?><a class="btn full" href="admin/login.php">Ke login admin</a><?php endif; ?></form></body></html>
