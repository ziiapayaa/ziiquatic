# ziiquatic — Studio Dekorasi Aquarium Air Laut & Biota Laut

Website profil usaha dan portofolio interaktif untuk dekorasi aquarium air laut, terumbu karang, dan biota laut. Dilengkapi dengan Panel Admin mandiri berbasis PHP & JSON tanpa perlu database server SQL terpisah.

---

## 🌊 Fitur Utama

- **Public Landing Page**:
  - Tampilan responsif dan minimalis dengan estetika *Ivory & Moss Reef Atelier*.
  - Galeri portofolio interaktif (maksimal 6 model awal dengan tombol ekspansi *"Lihat Semua Koleksi"*).
  - Modal pratinjau media resolusi tinggi (foto & video).
  - Integrasi konsultasi & pemesanan instan ke WhatsApp.

- **Admin Dashboard**:
  - Kelola portofolio aquarium (Tambah, Edit, Hapus).
  - Dukungan unggah file foto & klip video langsung ke server, atau sematkan tautan URL eksternal.
  - Custom delete confirmation modal yang bersih dan aman.
  - Auto-dismiss flash notifications.
  - Fitur ganti username & password admin mandiri.

- **Keamanan & Hardening (OWASP Compliant)**:
  - Proteksi CSRF berbasis token kriptografis.
  - Anti Brute-Force Rate Limiting (pemblokiran otomatis IP setelah 5x gagal login).
  - Sanitasi file upload ketat (verifikasi ekstensi, MIME type biner, dan getimagesize).
  - Anti Path-Traversal file deletion.
  - Sanitasi URL eksternal & output escaping (Anti-XSS).
  - HTTP Security Headers (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`).

---

## 🛠️ Persyaratan Sistem

- PHP 7.4 / PHP 8.x (dengan ekstensi `fileinfo` dan `json` aktif).
- Web Server Apache / LiteSpeed (mendukung `.htaccess` dan `mod_rewrite`).

---

## 🚀 Instalasi & Menjalankan

1. Clone repositori ini:
   ```bash
   git clone https://github.com/ziiapayaa/ziiquatic.git
   ```
2. Pindahkan folder ke direktori web server (misalnya `htdocs` untuk XAMPP atau `public_html` pada cPanel hosting).
3. Akses website melalui browser:
   - **Halaman Utama**: `http://localhost/ziiquatic/`
   - **Panel Admin**: `http://localhost/ziiquatic/admin/`
4. **Login Default Admin**:
   - Username: `admin`
   - Password: `admin123`
   *(Sangat disarankan untuk segera mengganti password di menu "Ganti Password" setelah login).*

---

## 📄 Lisensi

© 2026 ziiquatic Atelier. Hak cipta dilindungi.
