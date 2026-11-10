<?php
require __DIR__.'/../config/database.php'; require __DIR__.'/../includes/functions.php'; require_admin();
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action']??'';
    if ($action==='save') {
        $id=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $category=trim($_POST['category']??'');
        $price=max(0,(float)($_POST['price']??0)); $stock=max(0,(int)($_POST['stock']??0)); $image=trim($_POST['image_url']??''); $desc=trim($_POST['description']??'');
        if($name!=='') {
            if($id){$s=$pdo->prepare("UPDATE products SET name=?,category=?,price=?,stock=?,image_url=?,description=? WHERE id=?");$s->execute([$name,$category,$price,$stock,$image,$desc,$id]);}
            else {$s=$pdo->prepare("INSERT INTO products(name,category,price,stock,image_url,description) VALUES(?,?,?,?,?,?)");$s->execute([$name,$category,$price,$stock,$image,$desc]);}
            $msg='Produk berhasil disimpan.';
        }
    } elseif($action==='delete') { $pdo->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['id']]); $msg='Produk dihapus.'; }
}
$edit=null;
if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM products WHERE id=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$items=$pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Produk</title><link rel="stylesheet" href="../assets/style.css"></head><body class="admin-body"><aside class="sidebar"><a class="brand" href="../index.php">⚡ ElectroStore</a><small>ADMIN PANEL</small><a href="dashboard.php">▦ Dashboard</a><a class="active" href="products.php">▣ Produk</a><a href="orders.php">▤ Transaksi</a><a href="users.php">♙ Pengguna</a><a href="logout.php">↪ Keluar</a></aside><main class="admin-main"><div class="admin-top"><div><span class="eyebrow">KATALOG</span><h1>Manajemen Produk</h1></div></div><?php if($msg): ?><div class="success"><?=e($msg)?></div><?php endif; ?>
<section class="panel"><h2><?=$edit?'Edit produk':'Tambah produk'?></h2><form method="post" class="form-grid"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?=e($edit['id']??'')?>"><label>Nama produk<input name="name" required value="<?=e($edit['name']??'')?>"></label><label>Kategori<input name="category" value="<?=e($edit['category']??'')?>" placeholder="Laptop, Smartphone..."></label><label>Harga (Rp)<input type="number" min="0" name="price" required value="<?=e($edit['price']??'')?>"></label><label>Stok<input type="number" min="0" name="stock" required value="<?=e($edit['stock']??'0')?>"></label><label class="wide">URL gambar<input name="image_url" value="<?=e($edit['image_url']??'')?>" placeholder="https://..."></label><label class="wide">Deskripsi<textarea name="description"><?=e($edit['description']??'')?></textarea></label><div class="wide"><button class="btn">Simpan Produk</button> <?php if($edit): ?><a class="btn secondary" href="products.php">Batal</a><?php endif; ?></div></form></section>
<section class="panel"><h2>Daftar produk</h2><div class="table-wrap"><table><thead><tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead><tbody><?php foreach($items as $p): ?><tr><td><?=e($p['name'])?></td><td><?=e($p['category'])?></td><td><?=rupiah($p['price'])?></td><td><?=$p['stock']?></td><td><a href="?edit=<?=$p['id']?>">Edit</a> · <form class="inline" method="post" onsubmit="return confirm('Hapus produk ini?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>"><button class="link-danger">Hapus</button></form></td></tr><?php endforeach; ?><?php if(!$items): ?><tr><td colspan="5">Belum ada produk.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>
