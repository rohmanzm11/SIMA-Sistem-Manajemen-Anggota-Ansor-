<p align="center">
    <a href="https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

# SIMA - Sistem Manajemen Anggota Ansor

**Platform database terpusat untuk manajemen organisasi GP Ansor dengan teknologi modern**

<p align="center">
    <a href="https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor">
        <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 12">
    </a>
    <a href="https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor">
        <img src="https://img.shields.io/badge/Filament-v3-FBCFE8?style=for-the-badge&logo=laravel" alt="Filament v3">
    </a>
    <a href="https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor">
        <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql" alt="MySQL">
    </a>
    <a href="https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor/blob/main/LICENSE">
        <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
    </a>
</p>

---

## 📋 Tentang SIMA

**SIMA** (Sistem Manajemen Anggota Ansor) adalah platform database terpusat yang dirancang khusus untuk manajemen data organisasi **GP Ansor** secara digital dan terstruktur. Aplikasi ini dibangun menggunakan **Laravel 12** dan **Filament v3**, menyediakan solusi modern untuk pendataan anggota dari tingkat Cabang hingga Ranting dengan otomasi penuh.

---

## ⭐ Fitur Utama

### 🔧 Otomatisasi Administrasi
- **Generate Nomor Induk Anggota (NIA)** - Sistem penomoran otomatis yang terstruktur
- **Generate Kartu Tanda Anggota (KTA)** - Pembuatan kartu dengan template dinamis
- **Generate Sertifikat Pelatihan** - Sertifikat otomatis untuk kader yang lulus pelatihan

### 👥 Manajemen Keanggotaan
- Database anggota lengkap dengan detail profesi, jabatan, afiliasi politik
- Integrasi akun media sosial dan kontak
- Tracking status dan riwayat keanggotaan

### 🏛️ Hierarki Organisasi
- Pengelolaan struktur bertingkat: PC (Pengurus Cabang), PAC (Pengurus Anak Cabang), Ranting
- Mapping hubungan antar struktur organisasi
- Dashboard overview per level

### 📚 Sistem Pelatihan & Sertifikasi
- Manajemen materi pelatihan
- Pencatatan pelaksanaan pelatihan
- Sistem absensi peserta
- Log cetak dokumen untuk audit trail

### 📊 Export & Laporan
- **Export PDF** - Laporan dan dokumen dalam format PDF menggunakan DomPDF
- **Export Excel** - Data dapat diexport ke format Excel dengan Maatwebsite Laravel Excel
- **Import Excel** - Bulk import data dari file Excel

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| **Backend Framework** | [Laravel 12](https://laravel.com) |
| **Admin Panel** | [Filament v3](https://filamentphp.com) |
| **Database** | MySQL / MariaDB |
| **Frontend** | Tailwind CSS, Alpine.js, Livewire |
| **PDF Generation** | [DomPDF](https://github.com/dompdf/dompdf) |
| **Excel Export/Import** | [Laravel Excel (Maatwebsite)](https://github.com/SpartnerNL/Laravel-Excel) |
| **Server** | Apache/Nginx |
| **PHP Version** | 8.2+ |

---

## 🚀 Panduan Instalasi

### Prasyarat
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & npm
- MySQL 5.7 atau MariaDB 10.4+
- Git

### Langkah-Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor.git
cd SIMA-Sistem-Manajemen-Anggota-Ansor
```

#### 2. Instal Dependensi PHP
```bash
composer install
```

Dependensi penting yang termasuk:
- **barryvdh/laravel-dompdf** - Untuk generate PDF
- **maatwebsite/excel** - Untuk export/import Excel

Jika belum terinstal, instal secara manual:
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

Publish konfigurasi:
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

#### 3. Instal Dependensi Frontend
```bash
npm install
npm run build
```

#### 4. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

#### 5. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sima_db
DB_USERNAME=root
DB_PASSWORD=
```

#### 6. Jalankan Migrasi Database
```bash
php artisan migrate --seed
```

#### 7. Generate Aplikasi Key
```bash
php artisan storage:link
```

#### 8. Jalankan Aplikasi
```bash
php artisan serve
```

Aplikasi dapat diakses di `http://localhost:8000`

---

## 📂 Struktur Menu Aplikasi

Berikut adalah hierarki menu yang tersedia di sidebar aplikasi:

### 📍 Data Wilayah
- **Desa** - Manajemen data tingkat desa
- **Kecamatan** - Manajemen data tingkat kecamatan

### 🗂️ Data Master
- **Jabatan** - Daftar jabatan dalam organisasi
- **Organisasi** - Struktur dan informasi organisasi
- **Pekerjaan** - Master data profesi/pekerjaan
- **Afiliasi Politik** - Data afiliasi partai anggota
- **Media Sosial** - Platform media sosial yang terdaftar

### 📚 Pelatihan & Sertifikasi
- **Materi Pelatihan** - Pengelolaan materi training
- **Pelatihan** - Pencatatan acara pelatihan
- **Absensi** - Tracking kehadiran peserta
- **Log Cetak** - Audit trail pencetakan dokumen

### 🏢 Organisasi
- **Pengurus Cabang (PC)** - Manajemen level cabang
- **Pengurus Anak Cabang (PAC)** - Manajemen level anak cabang
- **Ranting** - Manajemen level ranting

### 📄 Template Dokumen
- **Template Sertifikat** - Desain dan manajemen template sertifikat
- **Template KTA** - Template kartu tanda anggota

---

## 🔐 Manajemen Pengguna & Autentikasi

Aplikasi dilengkapi dengan sistem autentikasi berbasis role dan permission:
- **Admin** - Akses penuh ke semua fitur
- **Operator** - Akses terbatas untuk entry data
- **Viewer** - Hanya bisa melihat laporan (read-only)

---

## 📊 Laporan & Analisis

SIMA menyediakan berbagai laporan otomatis:
- Laporan keanggotaan per wilayah
- Laporan pelatihan dan sertifikasi
- Laporan struktur organisasi
- Export data ke format Excel/PDF

---

## 🤝 Kontribusi

Kami menerima kontribusi dari komunitas. Untuk berkontribusi:

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan Anda (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buka Pull Request

Pastikan code Anda mengikuti standar yang sama dengan project.

---

## 📝 Lisensi

Proyek ini bersifat open-source dan tersedia di bawah lisensi **MIT License**. Lihat file [LICENSE](LICENSE) untuk detail lengkap.

---

## 📞 Dukungan & Kontak

Jika Anda memiliki pertanyaan, saran, atau menemukan bug, silakan:
- Buka [GitHub Issues](https://github.com/rohmanzm11/SIMA-Sistem-Manajemen-Anggota-Ansor/issues)
- Hubungi via [WhatsApp](https://wa.me/6285711317104)
- Hubungi melalui email atau media sosial

---

## 🙏 Doa

**Wallahul Muwaffiq ila Aqwamit Tharieq**  
*(Semoga Allah memberi taufik dalam kebaikan)*

---

## 📌 Tags & Topics

Untuk menemukan project ini lebih mudah, gunakan tags berikut:
- `laravel-12`
- `filament-v3`
- `ansor`
- `database-management`
- `membership-system`
- `php`
- `indonesia`

---

---

Dibuat dengan ❤️ untuk organisasi GP Ansor

© 2024 SIMA - All Rights Reserved