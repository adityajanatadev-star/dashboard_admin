# Dashboard Admin

Dashboard admin sederhana untuk mengelola pengguna (users) dengan fitur CRUD (Create, Read, Update, Delete). Aplikasi ini dirancang untuk admin yang ingin mengelola data pengguna secara mudah melalui interface web yang modern dan responsif.

## Fitur Utama
- **Login & Logout**: Sistem autentikasi sederhana dengan session.
- **User Management**: Tambah, edit, hapus, dan lihat daftar pengguna.
- **Dark Mode**: Toggle antara mode terang dan gelap.
- **Responsive Design**: UI yang responsif dengan efek glassmorphism dan animasi.
- **API REST**: Endpoint untuk operasi CRUD users (khusus admin).

## Tech Stack
- **Backend**: PHP Native (tanpa framework)
- **Database**: MySQL
- **Frontend**: HTML, CSS (TailwindCSS), JavaScript
- **Styling**: TailwindCSS dengan efek glassmorphism
- **Icons**: Lucide Icons
- **Animations**: GSAP (GreenSock Animation Platform)
- **Server**: Apache (via XAMPP)

## 📁 Struktur File

```
dashboard/
├── .htaccess               # Apache rules & security
├── login.php               # Halaman login (glassmorphism + ocean bg)
├── logout.php              # Handler logout
├── index.php               # Dashboard utama (sidebar + content panel)
├── database.sql            # Schema & seed data MySQL
│
├── includes/
│   ├── config.php          # Konfigurasi DB & app
│   └── auth.php            # Helper login/logout/session
│
├── api/
│   └── users.php           # REST API: CRUD users (admin only)
│
└── pages/
    ├── main.php            # Halaman welcome
    ├── menu1.php           # Placeholder untuk tools/app kamu
    └── users.php           # UI User Management
```

## ⚙️ Setup & Instalasi

### Persyaratan
- XAMPP (atau server Apache + PHP + MySQL)
- PHP 7.4+
- MySQL 5.7+

### Langkah Instalasi

1. **Clone atau Download Repository**
   ```
   git clone https://github.com/adityajanatadev-star/dashboard_admin.git
   ```

2. **Pindahkan ke Folder XAMPP**
   - Copy folder `dashboard` ke `C:\xampp\htdocs\`

3. **Buat Database**
   - Jalankan XAMPP, start Apache dan MySQL.
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Buat database baru, misalnya `dashboard_admin`
   - Import file `database.sql` dari folder project.

4. **Konfigurasi Database**
   - Edit file `includes/config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');        // Ganti dengan username MySQL kamu
     define('DB_PASS', '');            // Ganti dengan password MySQL kamu
     define('DB_NAME', 'dashboard_admin'); // Nama database yang dibuat
     ```

5. **Akses Aplikasi**
   - Buka browser: `http://localhost/dashboard/login.php`
   - Login dengan akun default: username `admin`, password `admin123`

## 🚀 Penggunaan

- **Login**: Masuk dengan kredensial admin.
- **Dashboard**: Navigasi melalui sidebar untuk melihat halaman utama atau user management.
- **User Management**: Tambah user baru, edit data, atau hapus user.
- **Dark Mode**: Klik toggle di header untuk mengubah tema.

## 📡 API Endpoints

- `GET /api/users.php` - Ambil semua users (JSON)
- `POST /api/users.php` - Tambah user baru
- `PUT /api/users.php?id=1` - Update user
- `DELETE /api/users.php?id=1` - Hapus user

API hanya bisa diakses jika login sebagai admin.

## 🔧 Troubleshooting

- **Error koneksi DB**: Pastikan konfigurasi di `config.php` benar.
- **Halaman tidak load**: Pastikan Apache dan MySQL running di XAMPP.
- **Login gagal**: Cek data di tabel `users` di database.

## 📝 Lisensi

Proyek ini open-source, gunakan sesuai kebutuhan.

## 👨‍💻 Kontribusi

Pull request welcome! Untuk perubahan besar, buat issue dulu.

---

Dibuat dengan ❤️ menggunakan PHP Native & TailwindCSS.
define('DB_NAME', 'dashboard_admin');
```

### 3. HTTPS (Production)

Kalau udah pakai HTTPS, aktifkan cookie secure di `includes/config.php`:

```php
define('COOKIE_SECURE', true);   // ← ubah ke true
```

### 4. Jalankan di Web Server

- Taruh folder `dashboard/` di `htdocs/` (XAMPP) atau `www/` (WAMP/Laragon)
- Akses via `http://localhost/dashboard/`
- Login dengan:
  - **admin** / **Password123!** (administrator)
  - **editor01** / **Password123!** (editor)
  - **viewer01** / **Password123!** (viewer)

> ⚠️ **Penting**: Ganti password default setelah pertama login!

---

## 🚀 Cara Tambah Menu Baru

### 1. Tambah halaman baru

Buat file `pages/nahamenu.php`

### 2. Daftarkan di `index.php`

Di bagian `$allowedPages`:
```php
$allowedPages = ['main', 'menu1', 'users', 'nahamenu'];
```

Di bagian `$titles`:
```php
$titles = [..., 'nahamenu' => 'Nama Menu'];
```

Di navigasi sidebar:
```php
<a class="nav-item" href="?page=nahamenu" data-tooltip="Nama Menu" data-page="nahamenu">
  <i data-lucide="icon-name" class="nav-icon"></i>
  <span class="menu-label">Nama Menu</span>
</a>
```

### 3. Embed tools PHP di Menu 1

Edit `pages/menu1.php`, di dalam div `#menu1-content`:

```php
<?php include __DIR__ . '/../tools/kalkulator.php'; ?>
```

---

## 🔐 Role & Akses

| Role          | Dashboard | Menu 1 | User Management |
|---------------|:---------:|:------:|:---------------:|
| Administrator | ✅        | ✅     | ✅              |
| Editor        | ✅        | ✅     | ❌              |
| Viewer        | ✅        | ✅     | ❌              |

---

## 🛠️ Tech Stack

- **Backend**: PHP 8+ Native (no framework)
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: TailwindCSS CDN + Lucide Icons + GSAP + Animate.css
- **Security**: HttpOnly & SameSite cookies, bcrypt password hash, PDO prepared statements
