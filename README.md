## 🔗 Demo Aplikasi
restopos-production.up.railway.app

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
  - Melihat laporan transaksi dan memantau user/karyawan
-**Karyawan**
  - Mengelola menu, bahan baku, stok, dan menyetujui pengajuan menu.
- **Member**:
  - Mengajukan menu baru.
- **Admin**
  - Mengakses Semua
    
## 🛠️ Teknologi yang Digunakan
- **Backend**: Laravel 11
- **Database**: MySQL
- **Frontend**: Blade
- **Styling**: Bootstrap 

## 📷 Screenshot
Absensi
![Screenshot 2025-04-17 133512](https://github.com/user-attachments/assets/0cd91b7d-9223-45b9-b540-6c6a9bedfc14)
Grafik Transaksi
![Screenshot 2025-04-21 184134](https://github.com/user-attachments/assets/6c625a5c-4258-474b-ad9e-5a8aba38ae31)
Dashboard Karyawan
![Screenshot 2025-04-21 184329](https://github.com/user-attachments/assets/57900b40-b8d5-41a7-b715-263884b1088c)
Pembelian
![Screenshot 2025-04-21 184412](https://github.com/user-attachments/assets/00e6c774-ded1-4c4a-8220-cd315c7121e7)
Penjualan
![Screenshot 2025-04-21 184512](https://github.com/user-attachments/assets/f1c8b904-96ff-47f9-8e24-3d7e510f9052)



## 💾 Instalasi

1. Clone repository ini:
   git clone https://github.com/Egaxyz/RestoPOS.git
   > cd RestoPOS
   
2. Install Depedensi Laravel
   > composer install

3. Copy file .env dan buat konfigurasi
   > cp .env.example .env
   > 
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


