# 🚀 Quick Setup Guide - AMitra Furniture

Panduan cepat untuk setup proyek setelah clone dari GitHub.

## ⚡ Setup Cepat (Copy-Paste Semua Perintah)

### Windows (PowerShell/CMD):
```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
type nul > database\database.sqlite
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

### Linux/Mac (Terminal):
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

## ✅ Checklist Setup

- [ ] Clone repository
- [ ] `composer install` - Install PHP dependencies
- [ ] `npm install` - Install Node dependencies
- [ ] Copy `.env.example` ke `.env`
- [ ] `php artisan key:generate` - Generate app key
- [ ] Buat file `database/database.sqlite`
- [ ] `php artisan migrate` - Buat tabel database
- [ ] **`php artisan db:seed`** - **PENTING! Isi data produk**
- [ ] `php artisan storage:link` - Link storage folder
- [ ] `php artisan serve` - Jalankan aplikasi

## ⚠️ Yang Paling Sering Lupa

### 1. Data Produk Tidak Muncul?
**SOLUSI**: Jalankan seeder!
```bash
php artisan db:seed
```

### 2. Error "No application encryption key"?
**SOLUSI**: Generate key!
```bash
php artisan key:generate
```

### 3. Database Error?
**SOLUSI**: Pastikan file database sudah dibuat!
```bash
# Windows:
type nul > database\database.sqlite

# Linux/Mac:
touch database/database.sqlite
```

## 🔄 Reset Database (Jika Ada Masalah)

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
1. Drop semua tabel
2. Buat ulang semua tabel
3. Isi data awal (produk, user, dll)

## 👤 Login Default

- Email: `test@example.com`
- Password: `password`

## 📞 Masih Error?

Lihat dokumentasi lengkap di [README.md](README.md)
