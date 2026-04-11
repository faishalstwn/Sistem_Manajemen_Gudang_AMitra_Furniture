# AMitra Furniture - Sistem Manajemen Inventory & E-Commerce

Aplikasi web untuk manajemen inventory furniture dan e-commerce berbasis Laravel.

## 🚀 Fitur Utama

- **Manajemen Produk**: CRUD produk furniture lengkap dengan kategori, harga, dan stok
- **Inventory Management**: Barang Masuk & Barang Keluar dengan tracking lengkap
- **E-Commerce**: Shopping cart, checkout dengan multiple payment methods
- **Payment Gateway**: Integrasi Midtrans Snap untuk pembayaran online (Credit Card, E-Wallet, Bank Transfer)
- **Export Data**: Export laporan ke Excel dan PDF
- **Dashboard Admin**: Statistik penjualan dan inventory real-time

## 📋 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (atau database lain sesuai preference)

## 🔧 Instalasi & Setup

Ikuti langkah-langkah berikut **dengan urutan yang benar** setelah clone repository:

### 1. Clone Repository
```bash
git clone <repository-url>
cd AMitraFurniture
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### 3. Setup Environment
```bash
# Copy file .env.example menjadi .env
cp .env.example .env
# Untuk Windows gunakan:
# copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Setup Database
```bash
# Buat file database SQLite (jika menggunakan SQLite)
# Untuk Windows:
type nul > database/database.sqlite

# Untuk Linux/Mac:
touch database/database.sqlite

# Jalankan migration untuk membuat tabel
php artisan migrate

# Jalankan seeder untuk mengisi data awal (PENTING!)
php artisan db:seed
```

> ⚠️ **PENTING**: Langkah `php artisan db:seed` **WAJIB** dijalankan untuk mengisi data produk awal. Tanpa ini, aplikasi akan kosong tanpa produk.

### 5. Setup Storage & Assets
```bash
# Link storage folder
php artisan storage:link

# Build assets (jika ada)
npm run build
# atau untuk development:
npm run dev
```

### 6. Konfigurasi Midtrans Payment Gateway (Opsional)

Untuk mengaktifkan pembayaran online dengan Midtrans:

**Quick Setup:**
```env
# Edit file .env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxx
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**📖 Panduan Lengkap:** Lihat [MIDTRANS_SETUP.md](MIDTRANS_SETUP.md) untuk:
- Cara daftar akun Midtrans (gratis)
- Mendapatkan credentials Sandbox
- Testing payment dengan test card
- Setup production
- Troubleshooting

> 💡 Tanpa setup Midtrans, aplikasi tetap bisa digunakan dengan metode pembayaran COD dan Transfer Bank manual.

### 7. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Akun Default

Setelah menjalankan seeder, Anda dapat login dengan:
- **Email**: test@example.com
- **Password**: password

## 📦 Data Seeder

Seeder akan mengisi:
- 1 User test account
- Data produk furniture (Sofa, Meja, Kursi, Lemari, dll)

## 🛠️ Perintah Berguna

```bash
# Reset database dan jalankan ulang seeder
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate IDE helper (untuk development)
php artisan ide-helper:generate
```

## 📝 Troubleshooting

### Data Produk Tidak Muncul?
Pastikan Anda sudah menjalankan:
```bash
php artisan db:seed
```

### Error "No application encryption key"?
Jalankan:
```bash
php artisan key:generate
```

### Error Database?
Pastikan file `database/database.sqlite` sudah dibuat dan migration sudah dijalankan.

### Error Permission (Linux/Mac)?
```bash
chmod -R 775 storage bootstrap/cache
```

## 📚 Struktur Proyek

```
AMitraFurniture/
├── app/
│   ├── Http/Controllers/  # Controllers
│   ├── Models/            # Eloquent Models
│   ├── Exports/           # Excel Export Classes
│   └── Helpers/           # Helper Classes (Midtrans, dll)
├── database/
│   ├── migrations/        # Database Migrations
│   └── seeders/           # Database Seeders
├── resources/
│   └── views/             # Blade Templates
├── routes/
│   └── web.php            # Web Routes
└── public/
    └── assets/            # Images & Static Files
```

## 💳 Metode Pembayaran

Aplikasi mendukung berbagai metode pembayaran:

### 1. Midtrans Payment Gateway (Online)
- **Credit/Debit Card** (Visa, Mastercard, JCB)
- **E-Wallet** (GoPay, OVO, DANA, ShopeePay, LinkAja)
- **Bank Transfer** (BCA, Mandiri, BNI, BRI, Permata)
- **Indomaret/Alfamart** (Over the counter)

**Cara Menggunakan:**
1. Checkout dan pilih "Midtrans" sebagai payment method
2. Klik "Bayar Sekarang"
3. Pilih metode pembayaran di popup Midtrans Snap
4. Selesaikan pembayaran
5. Otomatis redirect ke halaman sukses

**Setup:** Lihat [MIDTRANS_SETUP.md](MIDTRANS_SETUP.md)

### 2. Transfer Bank Manual
- Transfer BCA
- Transfer BRI  
- Transfer Mandiri

**Flow:**
1. Customer pilih bank transfer saat checkout
2. Sistem tampilkan nomor rekening
3. Customer transfer manual
4. Customer konfirmasi pembayaran di halaman order
5. Admin verifikasi dan update status

### 3. Cash on Delivery (COD)
Pembayaran dilakukan saat barang diterima.

## 🤝 Contributing

Untuk berkontribusi:
1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
