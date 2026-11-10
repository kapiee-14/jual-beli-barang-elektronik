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
6. Buka http://localhost/ElectroStore/install.php
7. Buat akun admin. Gunakan password minimal 8 karakter.
8. HAPUS file install.php setelah akun admin dibuat.
9. Buka toko: http://localhost/ElectroStore/
10. Buka admin: http://localhost/ElectroStore/admin/login.php

FITUR YANG SUDAH ADA
- Halaman katalog publik dan pencarian produk.
- Login administrator dengan password_hash/password_verify.
- Dashboard statistik produk, pesanan, pengguna, dan pendapatan pesanan selesai.
- Tambah, edit, dan hapus produk.
- Melihat daftar transaksi dan memperbarui status.
- Melihat daftar pengguna.

CATATAN
- Ini adalah fondasi website/prototipe lokal. Modul checkout pelanggan, keranjang, gateway pembayaran, upload file gambar, dan integrasi kurir belum diaktifkan.
- URL gambar produk dapat diisi dengan tautan gambar yang dapat diakses browser.
- Jangan gunakan konfigurasi bawaan untuk server publik. Gunakan HTTPS, password database kuat, validasi tambahan, backup, dan audit keamanan sebelum dipublikasikan.
- Jika localhost belum terbuka, pastikan Apache dan MySQL berstatus Running. Jika Apache gagal, periksa konflik port 80/443.
