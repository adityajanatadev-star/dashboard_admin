# AdminDash — PHP Native Dashboard

Dashboard admin ringan berbasis PHP Native + TailwindCSS + Glassmorphism.

---

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

---

## ⚙️ Setup

### 1. Buat Database

```sql
-- Import file database.sql ke MySQL:
mysql -u root -p < database.sql

-- Atau buka di phpMyAdmin dan import database.sql
```

### 2. Konfigurasi DB

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // ← sesuaikan
define('DB_PASS', '');            // ← sesuaikan
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
