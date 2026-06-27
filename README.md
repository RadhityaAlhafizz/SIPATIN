# SIPATIN | Sistem Pakar Diagnosa Penyakit Ikan Patin

> Aplikasi web berbasis PHP/MySQL untuk mendiagnosa penyakit ikan patin (*Pangasianodon hypophthalmus*) menggunakan metode **Forward Chaining**.

---

## 📌 Tentang Proyek

**SIPATIN** dikembangkan sebagai Tugas Akhir (Skripsi) untuk membantu pembudidaya ikan patin mengenali dan menangani penyakit secara mandiri tanpa harus bergantung pada pakar perikanan.

Sistem menggunakan metode **Forward Chaining** — penelusuran dimulai dari fakta berupa gejala yang dipilih pengguna, kemudian dicocokkan dengan basis aturan hingga ditemukan diagnosis penyakit beserta solusi penanganannya.

---

## ✨ Fitur

- **Diagnosa Interaktif** — Pengguna memilih gejala yang diamati, sistem menelusuri basis aturan dan menghasilkan diagnosis secara otomatis
- **Hasil Diagnosa Lengkap** — Menampilkan nama penyakit, persentase kesesuaian gejala, deskripsi penyakit, dan langkah penanganan
- **Visualisasi Donut Chart** — Persentase kesesuaian ditampilkan dalam grafik SVG interaktif
- **Cetak / Export PDF** — Hasil diagnosa dapat dicetak dengan format laporan resmi berkop surat
- **Panel Admin** — CRUD lengkap untuk manajemen penyakit, gejala, solusi, dan basis aturan
- **Histori Laporan** — Riwayat seluruh diagnosa tersimpan dan dapat dikelola admin

---

## 🦠 Basis Pengetahuan

Sistem mencakup **6 penyakit**, **18 gejala**, dan **36 aturan**:

| Kode | Nama Penyakit | Penyebab |
|------|---------------|----------|
| P001 | Motile Aeromonad Septicemia (MAS) | *Aeromonas hydrophila* |
| P002 | Saprolegniasis (Penyakit Jamur) | *Saprolegnia* sp. |
| P003 | Dactylogyrosis | *Dactylogyrus* sp. |
| P004 | Epistyliasis | *Epistylis* sp. |
| P005 | Ichthyophthiriasis (White Spot) | *Ichthyophthirius multifiliis* |
| P006 | Columnaris Disease | *Flavobacterium columnare* |

**Formula persentase diagnosis:**
```
Persentase = (Jumlah Gejala Cocok / Total Gejala Penyakit) × 100%
```

---

## 🛠️ Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP (Native) |
| Database | MySQL |
| Frontend | HTML5, Tailwind CSS, JavaScript |
| Font | Inter, Rubik |
| Icon | Font Awesome 6 |

---

## 📁 Struktur Direktori

```
SistemPakar/
├── assets/
│   ├── hero-patin.jpg
│   └── logo-patin-SIPATIN.webp
├── config/
│   └── database.php          # Konfigurasi koneksi database
├── database/
│   └── db_sistem_pakar.sql   # File SQL untuk import database
├── includes/
│   ├── auth.php              # Middleware autentikasi admin
│   ├── get_kode.php          # Endpoint JSON untuk form diagnosa
│   ├── icons.php             # Kumpulan helper icon SVG
│   ├── layout.php            # Layout head, body, dan foot global
│   └── sidebar.php           # Komponen sidebar admin
└── pages/
    ├── index.php             # Landing page (publik)
    ├── diagnosa.php          # Form pemilihan gejala (publik)
    ├── hasil_diagnosa.php    # Hasil diagnosis & visualisasi (publik)
    ├── dashboard.php         # Dashboard admin
    ├── penyakit.php          # CRUD data penyakit
    ├── gejala.php            # CRUD data gejala
    ├── solusi.php            # CRUD data solusi
    ├── basis_aturan.php      # CRUD basis aturan (rule base)
    ├── laporan.php           # Histori & laporan diagnosa
    ├── pengaturan.php        # Pengaturan sistem
    ├── login.php             # Halaman login admin
    ├── register.php          # Halaman registrasi admin
    └── logout.php            # Proses logout
```

---

## ⚙️ Instalasi

### Prasyarat
- PHP >= 7.4
- MySQL / MariaDB
- Web server — disarankan [XAMPP](https://www.apachefriends.org/)

### Langkah-langkah

**1. Clone repositori**
```bash
git clone https://github.com/username/SistemPakar.git
```
Taruh di folder `htdocs` (XAMPP) atau `www` (Laragon).

**2. Import database**

Buka phpMyAdmin, buat database baru bernama `db_sistem_pakar`, lalu import:
```
database/db_sistem_pakar.sql
```

**3. Konfigurasi database**

Sesuaikan `config/database.php` jika diperlukan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_sistem_pakar');
```

**4. Jalankan aplikasi**
```
http://localhost/SistemPakar/pages/index.php
```

---

## 🖥️ Alur Penggunaan

### Publik (Pengguna)
```
Landing Page (index.php)
    ↓
Form Diagnosa (diagnosa.php)
  → Pilih gejala yang diamati pada ikan
    ↓
Hasil Diagnosa (hasil_diagnosa.php)
  → Nama penyakit, persentase, deskripsi, penanganan
  → Cetak / simpan sebagai PDF
```

### Admin
```
Login (login.php)
    ↓
Dashboard (dashboard.php)
    ↓
Kelola Data:
  ├── Penyakit     → penyakit.php
  ├── Gejala       → gejala.php
  ├── Solusi       → solusi.php
  ├── Basis Aturan → basis_aturan.php
  └── Laporan      → laporan.php
```

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik (Tugas Akhir/Skripsi). Penggunaan ulang untuk keperluan non-akademik harap menghubungi pengembang.
