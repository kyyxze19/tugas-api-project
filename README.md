# 📦 API Project — Laravel Sanctum Authentication & Role-Based Access

> **Sistem Terdistribusi — Part 7a & 7b**  
> Implementasi keamanan API menggunakan Laravel Sanctum dengan sistem role dan ability berbasis database.

---

## 📋 Deskripsi

Project ini merupakan RESTful API berbasis **Laravel 13** yang mengimplementasikan:

- ✅ Autentikasi token menggunakan **Laravel Sanctum**
- ✅ Sistem **register** dan **login** user
- ✅ Manajemen **product** (list & tambah)
- ✅ Proteksi endpoint dengan **middleware auth:sanctum**
- ✅ Pembatasan akses berbasis **ability token** (`product-list`, `product-store`)
- ✅ Sistem **role terstruktur** via tabel `roles` dan `user_role`
- ✅ Token yang dibuat saat login **otomatis membawa ability** sesuai role user

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| Autentikasi | Laravel Sanctum v4.3 |
| Database | MySQL |
| PHP | ^8.3 |

---

## 🗂️ Struktur Endpoint API

| Method | Endpoint | Middleware | Keterangan |
|--------|----------|------------|------------|
| `POST` | `/api/registerUser` | — | Daftar user baru |
| `POST` | `/api/loginUser` | — | Login & dapatkan token |
| `GET` | `/api/products` | `auth:sanctum` + `ability:product-list` | Tampilkan semua produk |
| `POST` | `/api/products` | `auth:sanctum` + `ability:product-store` | Tambah produk baru |

---

## 🗄️ Struktur Database

```
users               → data user terdaftar
roles               → daftar role/ability (product-list, product-store)
user_role           → relasi user dengan role (pivot)
products            → data produk
personal_access_tokens → token Sanctum beserta abilities
```

### Relasi
```
User ──< user_role >── Role
User ──< personal_access_tokens
```

---

## 👤 Data User Default (Seeder)

| Nama | Email | Password | Role | Bisa Akses |
|------|-------|----------|------|------------|
| Andre | `andre@mail.com` | `password123` | `product-list` | GET /api/products |
| Admin | `admin@mail.com` | `password123` | `product-store` | POST /api/products |

> ℹ️ User dapat didaftarkan secara mandiri via `/api/registerUser`. User yang baru didaftar tidak memiliki role, sehingga tidak dapat mengakses endpoint yang dilindungi ability.

---

## 🚀 Cara Instalasi & Menjalankan

### 1. Clone / Siapkan Project
```bash
cd api-project
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan konfigurasi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_project
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database
Buka MySQL (Laragon/XAMPP/phpMyAdmin) dan buat database:
```sql
CREATE DATABASE api_project;
```

### 5. Jalankan Migration & Seeder
```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
- Membuat semua tabel
- Mengisi 2 role default (`product-list`, `product-store`)
- Membuat 2 user contoh (Andre & Admin) beserta assignment rolenya
- Mengisi 10 data produk dummy

### 6. Jalankan Server
```bash
php artisan serve
```

Server berjalan di: `http://127.0.0.1:8000`

---

## 🧪 Panduan Testing dengan Postman

> **Penting:** Selalu sertakan header `Accept: application/json` di setiap request.

---

### 1. Register User Baru
```
Method  : POST
URL     : http://127.0.0.1:8000/api/registerUser
Headers : Accept: application/json
Body    : (raw JSON)
```
```json
{
    "name": "Budi",
    "email": "budi@mail.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```
**Expected Response → 201 Created**
```json
{
    "success": true,
    "message": "User berhasil didaftarkan.",
    "data": {
        "user": { ... }
    }
}
```

---

### 2. Login User Biasa (Andre)
```
Method  : POST
URL     : http://127.0.0.1:8000/api/loginUser
Headers : Accept: application/json
Body    : (raw JSON)
```
```json
{
    "email": "andre@mail.com",
    "password": "password123"
}
```
**Expected Response → 200 OK**
```json
{
    "success": true,
    "message": "Login berhasil.",
    "data": {
        "user": { ... },
        "abilities": ["product-list"],
        "access_token": "1|xxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```
> 📋 Copy nilai `access_token` untuk digunakan di request selanjutnya.

---

### 3. GET Products (User Biasa) — ✅ Berhasil
```
Method  : GET
URL     : http://127.0.0.1:8000/api/products
Headers :
  Accept: application/json
  Authorization: Bearer <token_andre>
```
**Expected Response → 200 OK** (daftar 10 produk)

---

### 4. POST Products (User Biasa) — ❌ Ditolak
```
Method  : POST
URL     : http://127.0.0.1:8000/api/products
Headers :
  Accept: application/json
  Authorization: Bearer <token_andre>
Body    : (raw JSON)
```
```json
{
    "name": "Produk Test",
    "price": 50000,
    "description": "Deskripsi produk",
    "stock": 10
}
```
**Expected Response → 403 Forbidden**
```json
{
    "success": false,
    "message": "Forbidden. Anda tidak memiliki hak akses untuk endpoint ini."
}
```

---

### 5. Login Admin
```
Method  : POST
URL     : http://127.0.0.1:8000/api/loginUser
Headers : Accept: application/json
Body    : (raw JSON)
```
```json
{
    "email": "admin@mail.com",
    "password": "password123"
}
```
**Expected Response → 200 OK** dengan `abilities: ["product-store"]`

---

### 6. GET Products (Admin) — ❌ Ditolak
```
Method  : GET
URL     : http://127.0.0.1:8000/api/products
Headers :
  Accept: application/json
  Authorization: Bearer <token_admin>
```
**Expected Response → 403 Forbidden**

> ℹ️ Admin hanya memiliki ability `product-store`, bukan `product-list`.

---

### 7. POST Products (Admin) — ✅ Berhasil
```
Method  : POST
URL     : http://127.0.0.1:8000/api/products
Headers :
  Accept: application/json
  Authorization: Bearer <token_admin>
Body    : (raw JSON)
```
```json
{
    "name": "Laptop ASUS",
    "price": 12500000,
    "description": "Laptop gaming terbaru dengan spesifikasi tinggi",
    "stock": 25
}
```
**Expected Response → 201 Created**

---

### 8. Akses Tanpa Token — ❌ Ditolak
```
Method  : GET
URL     : http://127.0.0.1:8000/api/products
Headers : Accept: application/json
(tanpa Authorization header)
```
**Expected Response → 401 Unauthorized**
```json
{
    "success": false,
    "message": "Unauthenticated. Token tidak ditemukan atau tidak valid."
}
```

---

## 📁 Struktur File Penting

```
app/
├── Http/Controllers/
│   ├── AuthController.php       ← Register & Login
│   └── ProductController.php    ← Index & Store product
└── Models/
    ├── User.php                 ← HasApiTokens + roles()
    ├── Role.php                 ← role_name + users()
    └── Product.php              ← fillable + casts

database/
├── migrations/
│   ├── ..._create_users_table.php
│   ├── ..._create_products_table.php
│   ├── ..._create_roles_table.php
│   ├── ..._create_user_role_table.php
│   └── ..._create_personal_access_tokens_table.php
├── factories/
│   └── ProductFactory.php
└── seeders/
    ├── DatabaseSeeder.php       ← Orchestrator
    ├── RoleSeeder.php           ← Seed: product-list, product-store
    ├── UserSeeder.php           ← Seed: Andre, Admin + assign role
    └── ProductSeeder.php        ← Seed: 10 produk dummy

routes/
└── api.php                      ← Semua API routes

bootstrap/
└── app.php                      ← Middleware alias + exception handler JSON

config/
└── sanctum.php                  ← Konfigurasi Sanctum
```

---

## 🔐 Konsep Ability & Role

```
Login Request
     │
     ▼
Ambil roles user dari tabel user_role + roles
     │
     ▼
createToken('auth_token', ['product-list'])  ← abilities dari role
     │
     ▼
Token disimpan di personal_access_tokens (field: abilities)
     │
     ▼
Request ke endpoint protected
     │
     ├─ middleware auth:sanctum     → verifikasi token valid
     └─ middleware ability:xxx      → cek apakah token punya ability yang sesuai
```

| Role | Ability Token | Bisa GET /products | Bisa POST /products |
|------|---------------|--------------------|--------------------|
| `product-list` | `["product-list"]` | ✅ Ya | ❌ Tidak |
| `product-store` | `["product-store"]` | ❌ Tidak | ✅ Ya |
| (tanpa role) | `[]` | ❌ Tidak | ❌ Tidak |

---

## 📝 Validasi Endpoint

### POST /api/registerUser
| Field | Aturan |
|-------|--------|
| `name` | required, string, max 255 |
| `email` | required, email, unik di tabel users |
| `password` | required, min 8 karakter |
| `password_confirmation` | required, harus sama dengan password |

### POST /api/products
| Field | Aturan |
|-------|--------|
| `name` | required, string, max 255 |
| `price` | required, numeric, min 0 |
| `description` | required, string |
| `stock` | required, integer, min 0 |

---

## 👨‍💻 Author

**Tugas Sistem Terdistribusi — Part 7a & 7b**  
Laravel 13 × Sanctum × MySQL × Role-Based API Access
