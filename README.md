# SIMOTB - Sistem Informasi Manajemen Operasional Toko Bahan Bangunan

<p align="center">
  <strong>Sistem Informasi Manajemen Operasional Toko Bahan Bangunan Berbasis Website</strong><br>
  <em>Studi Kasus: Toko Bangunan Sumber Alam Jaya</em>
</p>

---

## 📋 Tentang Proyek

**SIMOTB** adalah sistem informasi berbasis web yang dirancang khusus untuk mengelola operasional toko bahan bangunan. Aplikasi ini membantu pemilik dan karyawan toko dalam mengelola inventori, penjualan, pembelian, dan laporan keuangan secara terintegrasi dan efisien.

Proyek ini dikembangkan sebagai studi kasus untuk **Toko Bangunan Sumber Alam Jaya** dengan tujuan meningkatkan efisiensi operasional dan manajemen data toko.

---

## ✨ Fitur Utama

- 📦 **Manajemen Inventori** - Kelola stok barang, kategori produk, dan harga
- 🛒 **Sistem Penjualan** - Proses penjualan, keranjang belanja, dan invoice
- 📥 **Manajemen Pembelian** - Kelola pembelian dari supplier dan pesanan
- 💰 **Laporan Keuangan** - Dashboard keuangan, laporan penjualan, dan profit/loss
- 👥 **Manajemen User** - Role-based access control (Admin, Penjual, Kasir)
- 📊 **Dashboard & Analytics** - Visualisasi data penjualan dan statistik bisnis
- 🧾 **Export & Print** - Export laporan ke PDF dan Excel

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend Framework** | Laravel 10.x |
| **Frontend Templating** | Blade Templates |
| **Database** | MySQL / MariaDB |
| **PHP Version** | ^8.1 |
| **Dependencies** |  |
| | • Laravel Sanctum (Autentikasi) |
| | • Maatwebsite Excel (Export Excel) |
| | • Laravel DomPDF (Export PDF) |
| | • Guzzle HTTP (HTTP Client) |

**Language Composition:**
- Blade: 52%
- PHP: 47%
- Other: 1%

---

## 💻 Cara Penggunaan

### Login
- Buka aplikasi di browser
- Masukkan username dan password sesuai role Anda
- Pilih role: Admin, Penjual, atau Kasir

### Menu Utama

#### Admin
- Dashboard - Melihat overview bisnis
- Manajemen Barang - Tambah/edit/hapus produk
- Manajemen Kategori - Kelola kategori barang
- Laporan - Lihat laporan penjualan, keuangan, dan stok
- Manajemen User - Tambah/edit pengguna sistem

#### Penjual/Kasir
- Dashboard - Ringkasan transaksi harian
- Penjualan - Proses penjualan baru
- Riwayat Penjualan - Lihat history transaksi
- Laporan - Laporan harian/mingguan
- Profil - Manage akun pengguna

---

## 📁 Struktur Proyek

```
simotb/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controller aplikasi
│   │   └── Middleware/      # Middleware custom
│   ├── Models/              # Eloquent Models
│   └── ...
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheet
│   └── js/                  # JavaScript files
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes (jika ada)
├── public/
│   ├── css/                 # Compiled CSS
│   ├── js/                  # Compiled JS
│   └── images/              # Gambar & aset
├── storage/                 # File uploads & logs
├── tests/                   # Unit & Feature tests
├── config/                  # Konfigurasi aplikasi
├── .env.example             # Environment template
└── composer.json            # Project dependencies
```

---

## 👤 Kontributor / Author

| Nama | Role | Kontak |
|------|------|--------|
| **aku3aji** | Developer | [@aku3aji](https://github.com/aku3aji) |

---

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Dibuat dengan ❤️ untuk Toko Bangunan Sumber Alam Jaya
</p>
