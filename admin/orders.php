<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';
require_admin();

$statuses = ['Menunggu', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$status = $_POST['status'] ?? '';
	if (in_array($status, $statuses, true)) {
		$stmt = $pdo->prepare('UPDATE orders SET status=? WHERE id=?');
		$stmt->execute([$status, (int)($_POST['id'] ?? 0)]);
	}
	header('Location: orders.php');
	exit;
}

$orders = $pdo->query("SELECT o.*,
	(SELECT GROUP_CONCAT(CONCAT(oi.product_name, ' × ', oi.quantity) ORDER BY oi.id SEPARATOR ', ')
	 FROM order_items oi WHERE oi.order_id=o.id) AS items
	FROM orders o ORDER BY o.created_at DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>Transaksi | ElectroStore</title>
	<link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
<aside class="sidebar">
	<a class="brand" href="../index.php">⚡ ElectroStore</a>
	<small>ADMIN PANEL</small>
	<a href="dashboard.php">▦ Dashboard</a>
	<a href="products.php">▣ Produk</a>
	<a class="active" href="orders.php">▤ Transaksi</a>
	<a href="users.php">♙ Pengguna</a>
	<a href="logout.php">↪ Keluar</a>
</aside>
<main class="admin-main">
	<div class="admin-top"><div><span class="eyebrow">PENJUALAN</span><h1>Manajemen Transaksi</h1></div></div>
	<section class="panel">
		<div class="table-wrap">
			<table>
				<thead><tr><th>Kode</th><th>Pelanggan</th><th>Kontak</th><th>Alamat</th><th>Barang</th><th>Total</th><th>Status</th><th>Perbarui</th></tr></thead>
				<tbody>
				<?php foreach ($orders as $order): ?>
					<tr>
						<td><?=e($order['order_code'])?></td>
						<td><?=e($order['customer_name'])?></td>
						<td><?=e($order['phone'])?></td>
						<td><?=e($order['shipping_address'] ?? '')?></td>
						<td><?=e($order['items'] ?? 'Rincian tidak tersedia')?></td>
						<td><?=rupiah($order['total'])?></td>
						<td><?=e($order['status'])?></td>
						<td>
							<form method="post" class="inline">
								<?=csrf_field()?>
								<input type="hidden" name="id" value="<?=$order['id']?>">
								<select name="status" aria-label="Status pesanan <?=e($order['order_code'])?>">
									<?php foreach ($statuses as $status): ?>
										<option value="<?=e($status)?>" <?=$order['status'] === $status ? 'selected' : ''?>><?=e($status)?></option>
									<?php endforeach; ?>
								</select>
								<button class="btn small">Simpan</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if (!$orders): ?><tr><td colspan="8">Belum ada pesanan.</td></tr><?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</main>
</body>
</html>
