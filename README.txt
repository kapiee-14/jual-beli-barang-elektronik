ELECTROSTORE - PHP + MySQL untuk XAMPP

KEBUTUHAN
- XAMPP dengan Apache, MySQL, dan PHP.
- Browser modern.

CARA MENJALANKAN
1. Ekstrak folder ElectroStore ke C:\xampp\htdocs\ sehingga menjadi C:\xampp\htdocs\ElectroStore.
2. Buka XAMPP Control Panel, klik Start pada Apache dan MySQL.
3. Buka http://localhost/phpmyadmin
4. Pilih tab Import, pilih file database.sql dari folder ElectroStore, lalu klik Import/Go.
   Jika import tidak tersedia, buka tab SQL dan tempel isi database.sql.
5. Periksa config/database.php. Pengaturan bawaan XAMPP: host localhost, user root, password kosong.
6. Untuk database lama, jalankan commerce-migration.sql dan customer-auth-migration.sql lewat tab Import atau SQL.
7. Buka http://localhost/ElectroStore/install.php
8. Buat akun admin dengan username dan password pilihan Anda (minimal 8 karakter).
9. HAPUS file install.php setelah akun admin dibuat.
10. Pelanggan dapat membuat akun di http://localhost/ElectroStore/register.php.
11. Buka toko: http://localhost/ElectroStore/
12. Buka admin: http://localhost/ElectroStore/admin/login.php

FITUR YANG SUDAH ADA
- Katalog publik dengan pencarian dan filter kategori.
- Favorit dan keranjang belanja berbasis sesi.
- Registrasi/login pelanggan dan riwayat pesanan akun.
- Checkout pelanggan atau tamu dengan validasi stok dan biaya pengiriman lokal.
- Pelacakan pesanan memakai kode pesanan dan nomor telepon.
- Login administrator dengan password_hash/password_verify.
- Dashboard statistik produk, pesanan, pengguna, dan pendapatan pesanan selesai.
- Tambah, edit, dan hapus produk.
- Melihat rincian pesanan, alamat kirim, dan memperbarui status.
- Melihat daftar pengguna.

DATABASE CHECKOUT
- Untuk database lama, jalankan commerce-migration.sql satu kali lewat phpMyAdmin.
- Jalankan juga customer-auth-migration.sql untuk password pelanggan dan relasi pesanan ke akun.
- Kedua skrip menambah struktur tanpa menghapus data lama dan aman dijalankan ulang.

CATATAN
- Ini adalah fondasi website/prototipe lokal. Pembayaran online dan integrasi kurir belum diaktifkan; checkout menggunakan simulasi COD dengan biaya kirim tetap Rp15.000.
- URL gambar produk dapat diisi dengan tautan gambar yang dapat diakses browser.
- Jangan gunakan konfigurasi bawaan untuk server publik. Gunakan HTTPS, password database kuat, validasi tambahan, backup, dan audit keamanan sebelum dipublikasikan.
- Jika localhost belum terbuka, pastikan Apache dan MySQL berstatus Running. Jika Apache gagal, periksa konflik port 80/443.
