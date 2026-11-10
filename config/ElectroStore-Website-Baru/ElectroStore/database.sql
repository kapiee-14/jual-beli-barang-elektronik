CREATE DATABASE IF NOT EXISTS electrostore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE electrostore;

CREATE TABLE IF NOT EXISTS admins (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(180) NOT NULL,
 category VARCHAR(100) DEFAULT '',
 price DECIMAL(14,2) NOT NULL DEFAULT 0,
 stock INT NOT NULL DEFAULT 0,
 image_url VARCHAR(500) DEFAULT '',
 description TEXT,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 phone VARCHAR(30) DEFAULT '',
 password_hash VARCHAR(255) DEFAULT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT DEFAULT NULL,
 order_code VARCHAR(40) NOT NULL UNIQUE,
 customer_name VARCHAR(120) NOT NULL,
 phone VARCHAR(30) DEFAULT '',
 shipping_address TEXT,
 total DECIMAL(14,2) NOT NULL DEFAULT 0,
 status ENUM('Menunggu','Diproses','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT DEFAULT NULL,
 product_name VARCHAR(180) NOT NULL,
 quantity INT UNSIGNED NOT NULL,
 unit_price DECIMAL(14,2) NOT NULL,
 line_total DECIMAL(14,2) NOT NULL,
 CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Laptop Pro 14','Laptop',12500000,8,'assets/products/laptop.jpg','Laptop untuk belajar dan produktivitas.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Laptop Pro 14' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartphone Nova','Smartphone',4200000,15,'assets/products/smartphone.jpg','Smartphone dengan fitur modern.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartphone Nova' AND category='Smartphone');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Headphone Wireless','Aksesori',650000,25,'assets/products/headphones.jpg','Headphone nirkabel untuk aktivitas harian.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Headphone Wireless' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Laptop Air 13','Laptop',9499000,6,'assets/products/laptop.jpg','Laptop ringan untuk belajar dan bekerja.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Laptop Air 13' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartphone Nova Pro','Smartphone',7899000,10,'assets/products/smartphone.jpg','Smartphone dengan kamera dan performa lebih tinggi.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartphone Nova Pro' AND category='Smartphone');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Tablet Tab 11','Tablet',5499000,12,'assets/products/tablet.jpg','Tablet layar lebar untuk hiburan dan produktivitas.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Tablet Tab 11' AND category='Tablet');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartwatch Active','Smartwatch',1299000,20,'assets/products/smartwatch.jpg','Jam pintar untuk aktivitas dan notifikasi harian.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartwatch Active' AND category='Smartwatch');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Monitor View 27','Monitor',3899000,8,'assets/products/monitor.jpg','Monitor 27 inci untuk kerja dan hiburan.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Monitor View 27' AND category='Monitor');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Kamera Mirrorless M1','Kamera',8999000,4,'assets/products/camera.jpg','Kamera mirrorless ringkas untuk foto dan video.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Kamera Mirrorless M1' AND category='Kamera');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Mouse Ergonomic Wireless','Aksesori',275000,30,'assets/products/mouse.jpg','Mouse nirkabel ergonomis untuk kerja harian.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Mouse Ergonomic Wireless' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Speaker Bluetooth Mini','Aksesori',799000,18,'assets/products/speaker.jpg','Speaker portabel dengan suara jernih untuk rumah dan perjalanan.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Speaker Bluetooth Mini' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Keyboard Mechanical K87','Aksesori',899000,12,'assets/products/keyboard.jpg','Keyboard mekanis nyaman untuk mengetik dan bermain.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Keyboard Mechanical K87' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Printer Inkjet Color','Printer',1899000,7,'assets/products/printer.jpg','Printer warna untuk dokumen dan kebutuhan rumah.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Printer Inkjet Color' AND category='Printer');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Router WiFi 6','Jaringan',699000,15,'assets/products/router.jpg','Router WiFi untuk koneksi stabil di rumah.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Router WiFi 6' AND category='Jaringan');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'PC Gaming RGB','Komputer',18999000,3,'assets/products/gaming-pc.jpg','Komputer gaming dengan komponen berperforma tinggi.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='PC Gaming RGB' AND category='Komputer');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Laptop Office 15','Laptop',8799000,5,'assets/products/laptop.jpg','Laptop 15 inci untuk pekerjaan kantor dan belajar.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Laptop Office 15' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Laptop Creator Pro','Laptop',18499000,3,'assets/products/gaming-pc.jpg','Laptop performa tinggi untuk desain dan kreasi konten.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Laptop Creator Pro' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Laptop Gaming Vortex','Laptop',22999000,4,'assets/products/gaming-pc.jpg','Laptop gaming untuk bermain dan streaming.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Laptop Gaming Vortex' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Notebook Travel 13','Laptop',10299000,7,'assets/products/laptop.jpg','Notebook tipis dan ringan untuk mobilitas.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Notebook Travel 13' AND category='Laptop');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartphone Nova Lite','Smartphone',2799000,14,'assets/products/smartphone.jpg','Smartphone praktis untuk kebutuhan sehari-hari.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartphone Nova Lite' AND category='Smartphone');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartphone Titan 5G','Smartphone',6999000,8,'assets/products/smartphone.jpg','Smartphone 5G dengan layar tajam dan baterai tahan lama.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartphone Titan 5G' AND category='Smartphone');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartphone Flip X','Smartphone',11999000,4,'assets/products/smartphone.jpg','Smartphone lipat dengan desain ringkas dan modern.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartphone Flip X' AND category='Smartphone');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Tablet Study 10','Tablet',3499000,12,'assets/products/tablet.jpg','Tablet 10 inci untuk belajar dan membaca.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Tablet Study 10' AND category='Tablet');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Tablet Pro Max','Tablet',8799000,6,'assets/products/tablet.jpg','Tablet layar besar untuk kerja dan hiburan.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Tablet Pro Max' AND category='Tablet');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Headphone Studio Max','Aksesori',1299000,11,'assets/products/headphones.jpg','Headphone nyaman dengan suara detail.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Headphone Studio Max' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Earbuds Air Mini','Aksesori',549000,24,'assets/products/headphones.jpg','Earbuds ringan untuk musik dan panggilan.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Earbuds Air Mini' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Smartwatch Fit Pro','Smartwatch',1899000,9,'assets/products/smartwatch.jpg','Jam pintar untuk memantau aktivitas dan notifikasi.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Smartwatch Fit Pro' AND category='Smartwatch');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Monitor Gaming 32','Monitor',5499000,5,'assets/products/monitor.jpg','Monitor gaming 32 inci untuk tampilan lebih luas.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Monitor Gaming 32' AND category='Monitor');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Monitor Office 24','Monitor',2499000,9,'assets/products/monitor.jpg','Monitor 24 inci untuk meja kerja.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Monitor Office 24' AND category='Monitor');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Camera Action 4K','Kamera',3299000,7,'assets/products/camera.jpg','Kamera aksi 4K untuk merekam perjalanan dan olahraga.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Camera Action 4K' AND category='Kamera');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Keyboard RGB 104','Aksesori',1199000,10,'assets/products/keyboard.jpg','Keyboard full-size dengan lampu RGB.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Keyboard RGB 104' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Mouse Gaming RGB','Aksesori',499000,16,'assets/products/mouse.jpg','Mouse gaming presisi dengan pencahayaan RGB.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Mouse Gaming RGB' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Speaker PartyBox Mini','Aksesori',1499000,8,'assets/products/speaker.jpg','Speaker portabel untuk musik dan acara kecil.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Speaker PartyBox Mini' AND category='Aksesori');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Printer EcoTank Home','Printer',2899000,6,'assets/products/printer.jpg','Printer rumah dengan tangki tinta hemat.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Printer EcoTank Home' AND category='Printer');

INSERT INTO products(name,category,price,stock,image_url,description)
SELECT 'Router Mesh AX3000','Jaringan',1199000,10,'assets/products/router.jpg','Router mesh untuk jangkauan WiFi rumah yang luas.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Router Mesh AX3000' AND category='Jaringan');
