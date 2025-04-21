# 🍽️ RestoPOS

RestoPOS adalah aplikasi Point of Sale berbasis web yang dibuat menggunakan Laravel, dirancang untuk mengelola transaksi restoran secara efisien dan real-time.

## 🚀 Fitur Utama

### 📦 Transaksi & Stok
- **Transaksi Pembelian**:
  - Menambah stok bahan baku sesuai pembelian.
- **Transaksi Penjualan**:
  - Mengurangi stok bahan baku sesuai menu yang dijual.
- **Stok Menu Otomatis**:
  - Stok dan harga menu otomatis berubah sesuai dengan stok dan harga bahan baku yang dipilih.

### 🍳 Manajemen Menu
- **Pengajuan Menu oleh Member**:
  - Role "member" bisa mengajukan menu baru.
  - Jika disetujui oleh karyawan, menu otomatis ditambahkan ke daftar menu aktif.

### 🧾 Receipt & Pembayaran
- Terdapat **nota (receipt)** setiap transaksi penjualan.
- Metode pembayaran yang didukung:
  - **Cash**: Memasukkan jumlah uang yang diberikan customer dan menghitung kembalian.
  - **QRIS / Bank**: Tanpa input uang tunai, langsung selesai setelah pembayaran dikonfirmasi.
    
### Absensi
  **Role Manager melakukan absensi kepada karyawan**

## 🔐 Role & Hak Akses
- **Manager**:
  - Melihat riwayat transaksi dan memantau user/karyawan
-**Karyawan**
  - Mengelola menu, bahan baku, stok, dan menyetujui pengajuan menu.
- **Member**:
  - Mengajukan menu baru.

## 🛠️ Teknologi yang Digunakan
- **Backend**: Laravel 11
- **Database**: MySQL
- **Frontend**: Blade
- **Styling**: Bootstrap 

## 📷 Screenshot
Absensi
![Screenshot 2025-04-17 133512](https://github.com/user-attachments/assets/0cd91b7d-9223-45b9-b540-6c6a9bedfc14)

> *(Tambahkan di sini gambar tampilan aplikasi atau form transaksi, menu, receipt, dll.)*

## 💾 Instalasi

1. Clone repository ini:
   git clone https://github.com/Egaxyz/RestoPOS.git
   > cd RestoPOS
   
2. Install Depedensi Laravel
   > composer install

3. Copy file .env dan buat konfigurasi
   > cp .env.example .env
   > php artisan key:generate

4. Atur koneksi database di file .env, lalu jalankan migrasi:
   > php artisan migrate --seed

5. Jalankan Aplikasi:
   > php artisan serve

✨ Catatan Tambahan
Proyek ini masih bisa dikembangkan lebih lanjut, misalnya dengan fitur laporan bulanan, analitik penjualan, atau integrasi dengan printer struk.

🤝 Kontribusi
Pull Request sangat terbuka untuk perbaikan bug, penambahan fitur, atau dokumentasi.

🧑‍💻 Developer
Made with ❤️ by Egaxyz


