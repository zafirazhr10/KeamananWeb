# 🔓 SQL Injection Demo Lab
> Proyek edukasi untuk memahami SQL Injection — Tugas UTS Pemrograman Web

---

## ⚠️ Disclaimer

Proyek ini **hanya untuk tujuan edukasi**. Gunakan **hanya di lingkungan lokal**.
Jangan pernah mencoba teknik ini pada sistem nyata tanpa izin.

---

## 📁 Struktur Proyek

```
sql-injection-demo/
├── index.php                  # Halaman utama / menu
├── config.php                 # Konfigurasi database
├── setup.sql                  # Script untuk membuat database
│
├── assets/
│   └── css/
│       └── style.css          # Stylesheet global
│
├── vulnerable/
│   ├── login.php              # Demo 1: Bypass login (RENTAN)
│   └── search.php             # Demo 2: Ekstraksi data UNION (RENTAN)
│
└── secure/
    ├── login.php              # Demo 3: Login aman (Prepared Statements)
    └── search.php             # Demo 4: Pencarian aman (Prepared Statements)
```

---

## 🚀 Cara Setup

### Prasyarat
- XAMPP / WAMP / Laragon (PHP + MySQL)
- Browser modern

### Langkah-langkah

**1. Copy folder ke htdocs**
```
C:\xampp\htdocs\sql-injection-demo\
```

**2. Setup database**

Buka phpMyAdmin → klik tab "SQL" → paste isi `setup.sql` → klik Go.

Atau via terminal MySQL:
```bash
mysql -u root -p < setup.sql
```

**3. Sesuaikan config.php**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');   // sesuaikan
define('DB_PASS', '');       // sesuaikan
define('DB_NAME', 'sqli_demo');
```

**4. Akses di browser**
```
http://localhost/sql-injection-demo/
```

---

## 🧪 Cara Bermain

### Demo 1 — Bypass Login (Rentan)
URL: `http://localhost/sql-injection-demo/vulnerable/login.php`

Coba payload berikut di kolom **Username** (password bisa apa saja):
```
admin' --
' OR '1'='1
' OR 1=1 --
```

### Demo 2 — Ekstraksi Data UNION (Rentan)
URL: `http://localhost/sql-injection-demo/vulnerable/search.php`

Coba payload berikut di kolom **Pencarian**:
```sql
-- Lihat semua tabel
%' UNION SELECT table_name,table_schema,NULL FROM information_schema.tables-- -

-- Eksfiltrasi data rahasia
%' UNION SELECT id,data_name,data_value FROM secret_data-- -

-- Ambil semua password user
%' UNION SELECT id,username,password FROM users-- -
```

### Demo 3 & 4 — Versi Aman
Coba payload yang **sama** — tidak akan berhasil!

---

## 🛡️ Perbedaan Kunci: Rentan vs Aman

| Aspek | ❌ Rentan | ✅ Aman |
|-------|----------|--------|
| Query | String langsung digabung | Prepared Statement (`?`) |
| Input | Tidak divalidasi | Whitelist regex |
| Resiko | Bypass login, ekstraksi data | Terlindungi |

---

## 📚 Referensi

- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [PHP PDO Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
- [PortSwigger SQL Injection Labs](https://portswigger.net/web-security/sql-injection)
