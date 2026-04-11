# 📋 SCRIPT DEMO SISTEM MANAGEMENT GUDANG
**AMitra Furniture - Progress Meeting**  
**Durasi Demo: 15-20 menit**

---

## 🎯 PERSIAPAN SEBELUM DEMO (5 menit sebelum meeting)

### Checklist Teknis:
- [ ] Aplikasi sudah running: `php artisan serve`
- [ ] Database sudah terisi sample data yang realistis
- [ ] Browser sudah login sebagai Admin
- [ ] Tab browser siap di halaman Dashboard Admin
- [ ] Screenshot backup (jika ada masalah koneksi)
- [ ] Postman/API testing tool (jika perlu tunjukkan API)

### Data Sample yang Diperlukan:
- [ ] Minimal 5-10 produk dengan stok bervariasi
- [ ] Beberapa record barang masuk dari supplier berbeda
- [ ] Beberapa record barang keluar dengan tujuan berbeda
- [ ] Stok movement history yang cukup untuk ditampilkan

---

## 📝 SKENARIO DEMO

### **[0-2 menit] PEMBUKAAN**

**Script:**
> "Halo semuanya, terima kasih sudah hadir di progress meeting ini. Hari ini saya akan mendemonstrasikan **sistem management gudang** yang sudah saya develop untuk AMitra Furniture.
>
> Sistem ini dibuat dengan **Laravel framework** dan mencakup fitur-fitur:
> - ✅ Pencatatan barang masuk dari supplier
> - ✅ Pencatatan barang keluar
> - ✅ Auto-update stok produk
> - ✅ Tracking riwayat pergerakan stok
> - ✅ Filtering & search yang powerful
> - ✅ Export laporan ke Excel dan PDF
>
> Mari kita lihat langsung sistemnya bekerja."

---

### **[2-4 menit] DASHBOARD OVERVIEW**

**Aksi:**
1. Tunjukkan halaman Admin Dashboard
2. Highlight section statistik gudang

**Script:**
> "Ini adalah **Dashboard Admin** yang menjadi central monitoring.
>
> Di sini kita bisa lihat overview:
> - Total produk yang ada di gudang
> - Total stok keseluruhan
> - Statistik barang masuk & keluar
> - [Jika ada] Grafik pergerakan stok bulanan
>
> Semua data ini **real-time**, otomatis terupdate setiap ada transaksi gudang."

**Talking Points:**
- Dashboard responsive dan user-friendly
- Data aggregation menggunakan Eloquent ORM
- Query optimization untuk performa

---

### **[4-9 menit] FITUR BARANG MASUK (CORE DEMO)**

**Aksi:**
1. Navigasi ke menu "Barang Masuk"
2. Tunjukkan halaman list/riwayat barang masuk

**Script:**
> "Sekarang kita masuk ke fitur utama pertama: **Barang Masuk**.
>
> Di halaman ini, kita bisa lihat:
> - Riwayat semua barang yang masuk ke gudang
> - Informasi lengkap: produk, jumlah, supplier, tanggal
> - Total barang masuk & barang masuk hari ini"

**Aksi - Filter & Search:**
3. Demo filter berdasarkan produk
4. Demo filter berdasarkan supplier
5. Demo filter berdasarkan range tanggal

**Script:**
> "Yang menarik, sistem ini punya **filtering yang powerful**:
> - Filter by produk - misalnya saya mau lihat hanya barang masuk untuk 'Kursi Kayu'
> - Filter by supplier - untuk tracking supplier tertentu
> - Filter by tanggal - untuk laporan periode tertentu
> - **Kombinasi filter** - semua filter bisa dikombinasikan
>
> [Tunjukkan filter bekerja]
>
> Lihat, data langsung ter-filter sesuai kriteria. Dan URL-nya tetap preserve query string, jadi bisa di-bookmark atau di-share."

**Aksi - Tambah Barang Masuk:**
6. Klik tombol "Tambah Barang Masuk"
7. Isi form dengan data realistic:
   - Pilih produk: mis. "Meja Makan Minimalis"
   - Jumlah: mis. 50 unit
   - Tanggal masuk: hari ini
   - Supplier: mis. "PT Kayu Jati Sentosa"
   - Catatan: mis. "Pesanan bulanan Maret 2026"

**Script:**
> "Sekarang saya akan demo **mencatat barang masuk baru**.
>
> Form ini ada validasi lengkap:
> - Produk wajib dipilih dari dropdown
> - Jumlah harus angka positif
> - Tanggal masuk tidak boleh kosong
> - Supplier dan catatan optional untuk fleksibilitas
>
> [Isi form]
>
> Saya input 50 unit Meja Makan dari supplier PT Kayu Jati Sentosa..."

**Aksi:**
8. Submit form
9. Lihat success message
10. **PENTING**: Tunjukkan data baru muncul di list

**Script:**
> "Submit... dan **berhasil!**
>
> Lihat pesan sukses muncul, dan data barang masuk langsung tercatat di list.
>
> Sekarang yang **sangat penting**, mari kita cek apa yang terjadi di background..."

**Aksi - Tunjukkan Auto Stock Update:**
11. Navigasi ke menu "Kelola Produk" atau "Gudang"
12. Cari produk "Meja Makan Minimalis"
13. **Tunjukkan stok bertambah 50 unit**

**Script:**
> "**Ini fitur powerful-nya**: Stok produk **otomatis bertambah**!
>
> Sebelumnya stok Meja Makan adalah [X], sekarang jadi [X+50].
>
> Sistem menggunakan **database transaction** untuk memastikan:
> 1. Record barang masuk tersimpan
> 2. Stok produk terupdate
> 3. History movement tercatat
>
> Jika salah satu gagal, **semua akan di-rollback**. Jadi data consistency terjaga."

**Talking Points Technical:**
- Laravel DB Transaction untuk ACID compliance
- Eloquent relationship (BarangMasuk belongsTo Product)
- StockMovement model untuk audit trail
- Form validation menggunakan Laravel Request Validation

---

### **[9-13 menit] FITUR BARANG KELUAR**

**Script:**
> "Okay, sekarang kebalikannya: **Barang Keluar**.
>
> Konsepnya mirip, tapi untuk tracking barang yang keluar dari gudang - bisa untuk penjualan, retur, atau keperluan lain."

**Aksi:**
1. Navigasi ke menu "Barang Keluar"
2. Tunjukkan list dengan filter yang sama

**Script:**
> "Same thing, kita punya:
> - Riwayat barang keluar lengkap
> - Filter by produk, tujuan, dan tanggal
> - Total statistik"

**Aksi - Tambah Barang Keluar:**
3. Klik "Tambah Barang Keluar"
4. Isi form:
   - Produk: "Meja Makan Minimalis" (yang tadi kita tambah)
   - Jumlah: 10 unit
   - Tujuan: "Penjualan Toko Jakarta"
   - Tanggal: hari ini
   - Catatan: "Order #12345"

**Script:**
> "Saya akan catat barang keluar 10 unit untuk penjualan di toko Jakarta..."

**Aksi:**
5. Submit form
6. Tunjukkan stok produk berkurang 10 unit

**Script:**
> "Submit... berhasil!
>
> Dan **check stok lagi**: sekarang berkurang 10 unit dari tadi.
>
> Dari 50 unit yang masuk, keluar 10, jadi net increase 40 unit. **Perfect!**"

**Talking Points:**
- Validasi stok mencegah overselling (optional: tunjukkan error jika stok tidak cukup)
- Tujuan fleksibel (penjualan, retur, damaged goods, dll)
- Same transaction mechanism untuk data integrity

---

### **[13-16 menit] WAREHOUSE MANAGEMENT & STOCK MOVEMENT**

**Script:**
> "Selain barang masuk-keluar, ada juga fitur **Warehouse Management** yang lebih detail."

**Aksi:**
1. Navigasi ke "Kelola Gudang" atau "Warehouse"
2. Tunjukkan list produk dengan stok

**Script:**
> "Di sini kita bisa:
> - Lihat semua produk dengan stok current
> - Aksi langsung per produk: Stok Masuk, Stok Keluar, atau Adjustment"

**Aksi - Tunjukkan Riwayat Stok:**
3. Klik "Riwayat" atau "Stock Movement" pada salah satu produk
4. Tunjukkan detail movement history

**Script:**
> "**Ini yang powerful**: setiap perubahan stok tercatat detail di **Stock Movement**.
>
> Kita bisa lihat:
> - Siapa yang melakukan perubahan (user_id)
> - Tipe transaksi (in/out/adjustment)
> - Jumlah perubahan
> - **Stock before & after**
> - Reference & note untuk audit trail
> - Timestamp lengkap
>
> Ini penting untuk **audit**, troubleshooting, dan compliance."

**Aksi - Stock Adjustment (Optional):**
5. Demo adjustment jika ada waktu

**Script:**
> "Ada juga **Stock Adjustment** untuk koreksi:
> - Selisih fisik vs sistem (stock opname)
> - Barang rusak/hilang
> - Koreksi data
>
> Semua tetap tercatat di movement dengan note yang jelas."

---

### **[16-19 menit] EXPORT & REPORTING**

**Script:**
> "Fitur terakhir yang saya tunjukkan: **Export & Reporting**."

**Aksi:**
1. Navigasi ke halaman List Barang Masuk/Keluar
2. Tunjukkan tombol Export Excel dan Export PDF

**Script:**
> "Sistem support export laporan ke:
> - **Excel** - untuk analisis lebih lanjut
> - **PDF** - untuk printout atau archiving"

**Aksi - Demo Export:**
3. Set filter tanggal (mis. 1 bulan terakhir)
4. Klik "Export to Excel"
5. File otomatis download
6. **Buka file Excel** dan tunjukkan isinya

**Script:**
> "Saya filter data 1 bulan terakhir, lalu export...
>
> [File downloading]
>
> Dan ini hasilnya: **Excel file** dengan data lengkap:
> - Product name
> - Quantity
> - Supplier/Tujuan
> - Tanggal
> - Catatan
>
> Format-nya rapi, siap untuk analisis atau presentasi ke management."

**Aksi (Optional):**
7. Demo export PDF juga

**Script:**
> "PDF export juga sama, tapi format sudah siap print dengan header, footer, dan styling yang proper."

**Talking Points Technical:**
- Menggunakan Maatwebsite/Laravel-Excel package
- DomPDF untuk PDF generation
- Filter parameter passed ke export class
- Memory-efficient untuk large dataset

---

### **[19-20 menit] CLOSING & Q&A**

**Script:**
> "Okay, itu demo lengkap sistem Management Gudang yang sudah saya develop.
>
> **Recap fitur backend yang sudah berfungsi:**
> - ✅ Barang Masuk dengan auto stock update
> - ✅ Barang Keluar dengan stock validation
> - ✅ Stock Movement tracking untuk audit trail
> - ✅ Powerful filtering & search
> - ✅ Excel & PDF export dengan custom date range
> - ✅ Database transactions untuk data integrity
> - ✅ Authentication & authorization (Admin only)
>
> **Technical Stack:**
> - Laravel 11 Framework
> - MySQL Database
> - Eloquent ORM
> - DB Transactions
> - Laravel Excel & DomPDF
> - Validation & Middleware
>
> **Next Steps** (jika ditanya):
> - [Sebutkan fitur yang masih akan dikembangkan]
> - [Integrasi dengan modul lain jika ada]
> - [Testing & optimization]
>
> Ada pertanyaan?"

---

## 💡 TIPS PRESENTASI

### Do's ✅
- **Bicara dengan Percaya Diri** - Anda sudah develop sistem yang solid
- **Tunjukkan Antusiasme** - Highlight fitur yang Anda bangga
- **Jelaskan "Why"** - Kenapa fitur ini penting untuk bisnis
- **Siap dengan Data** - Gunakan angka realistic untuk demo
- **Slow & Clear** - Jangan terburu-buru, pastikan audience mengikuti
- **Interactive** - Tanya "Ada yang ingin saya tunjukkan lebih detail?"

### Don'ts ❌
- **Jangan Apologize** - Hindari "maaf ini masih kurang" di awal
- **Jangan Terlalu Technical** - Sesuaikan dengan audience
- **Jangan Skip Error** - Jika ada error, jelaskan dengan tenang
- **Jangan Overclaim** - Jujur tentang apa yang sudah dan belum jadi
- **Jangan Baca Script** - Gunakan script ini sebagai guide, bukan dibaca

---

## 🚨 TROUBLESHOOTING SAAT DEMO

### Jika Ada Error:
1. **Tetap Tenang** - "Okay, ada issue di sini, let me check..."
2. **Check Cepat** - Lihat error message
3. **Explain** - "Ini validation error yang memang saya set untuk prevent invalid data"
4. **Fallback** - Punya screenshot backup

### Jika Internet/Server Lambat:
1. **Fill the Time** - Jelaskan technical details sambil loading
2. **Use Backup** - Switch ke screenshot atau video recording
3. **Honest** - "Demo environment agak lambat, tapi in production akan lebih cepat"

### Jika Ada Pertanyaan Sulit:
1. **Acknowledge** - "Good question..."
2. **Be Honest** - Jika tidak tahu, "Belum saya explore, tapi bisa saya research"
3. **Redirect** - "Untuk detail itu, mungkin bisa kita discuss setelah demo"

---

## 📊 METRICS TO HIGHLIGHT

Jika ditanya performa atau scale:

- **Database Transactions**: Ensure ACID compliance
- **Query Optimization**: Eager loading untuk relationship (N+1 problem solved)
- **Pagination**: 20 items per page untuk performa
- **Validation**: Server-side & client-side validation
- **Security**: Admin middleware, CSRF protection, SQL injection prevention
- **Scalability**: Codebase modular, easy to extend

---

## 🎬 POST-DEMO

Setelah demo selesai:

1. **Kirim Summary**:
   - Link ke repository (jika diminta)
   - Screenshot key features
   - Technical documentation (jika ada)

2. **Follow Up**:
   - Note down semua pertanyaan/feedback
   - Buat action items untuk improvement
   - Timeline untuk next update

3. **Celebrate** 🎉:
   - Anda sudah build sistem yang functional dan professional
   - Demo yang baik adalah bukti kerja keras Anda

---

**Good luck dengan demo-nya! 💪**

**Catatan**: Script ini adalah panduan, feel free untuk adjust sesuai style Anda dan situasi meeting. Yang penting tunjukkan **confidence** dan **value** dari sistem yang sudah Anda build!
