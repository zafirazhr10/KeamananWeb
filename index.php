<?php
// ============================================================
// index.php — Halaman Utama Demo SQL Injection
// ============================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Demo Lab</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            text-align: center;
            padding: 60px 20px 40px;
        }
        .hero h1 {
            font-size: 2.6rem;
            color: #fff;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        .hero h1 span { color: #e44d42; }
        .hero p {
            color: #888;
            font-size: 1rem;
            max-width: 560px;
            margin: 0 auto 36px;
            line-height: 1.7;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .demo-card {
            background: #1a1a2e;
            border: 1px solid #2a2a40;
            border-radius: 12px;
            padding: 28px;
            text-decoration: none;
            transition: border-color 0.2s, transform 0.2s;
            display: block;
        }
        .demo-card:hover { border-color: #e44d42; transform: translateY(-3px); }
        .demo-card.safe:hover { border-color: #27ae60; }
        .demo-card .icon { font-size: 2rem; margin-bottom: 14px; }
        .demo-card h3 { color: #fff; margin-bottom: 8px; font-size: 1.05rem; }
        .demo-card p { color: #777; font-size: 0.85rem; line-height: 1.6; }
        .warning-box {
            background: #2e1500;
            border: 1px solid #f39c12;
            border-radius: 10px;
            padding: 18px 22px;
            color: #f1c40f;
            font-size: 0.88rem;
            line-height: 1.7;
            max-width: 700px;
            margin: 0 auto 40px;
        }
        .warning-box strong { color: #f39c12; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="brand">⚠️ SQLi Demo Lab</div>
    <div class="nav-links">
        <a href="index.php" class="active">Home</a>
        <a href="vulnerable/login.php">Login Rentan</a>
        <a href="vulnerable/search.php">Pencarian Rentan</a>
        <a href="secure/login.php">Login Aman</a>
        <a href="secure/search.php">Pencarian Aman</a>
    </div>
</nav>

<div class="container">
    <div class="hero">
        <h1>SQL <span>Injection</span><br>Demo Lab</h1>
        <p>
            Lab pembelajaran interaktif untuk memahami bagaimana SQL Injection bekerja,
            dampaknya, dan cara mencegahnya — semuanya dalam lingkungan yang aman.
        </p>
    </div>

    <div class="warning-box">
        ⚠️ <strong>Perhatian:</strong> Seluruh demo ini dibuat untuk tujuan edukasi semata.
        Gunakan hanya di lingkungan lokal / lab. <strong>Jangan pernah</strong> mencoba
        teknik ini pada sistem nyata tanpa izin. Melakukannya adalah tindakan ilegal.
    </div>

    <div class="demo-grid">
        <a href="vulnerable/login.php" class="demo-card">
            <div class="icon">🔓</div>
            <span class="badge badge-danger">RENTAN</span>
            <h3>Demo 1: Bypass Login</h3>
            <p>Lihat bagaimana penyerang bisa masuk tanpa mengetahui password menggunakan payload sederhana.</p>
        </a>

        <a href="vulnerable/search.php" class="demo-card">
            <div class="icon">💾</div>
            <span class="badge badge-danger">RENTAN</span>
            <h3>Demo 2: Ekstraksi Data</h3>
            <p>Simulasi UNION-based injection untuk mengambil data dari tabel lain yang seharusnya tidak bisa diakses.</p>
        </a>

        <a href="secure/login.php" class="demo-card safe">
            <div class="icon">🔒</div>
            <span class="badge badge-success">AMAN</span>
            <h3>Demo 3: Login Aman</h3>
            <p>Versi yang sudah diperbaiki menggunakan Prepared Statements — payload yang sama tidak akan berhasil.</p>
        </a>

        <a href="secure/search.php" class="demo-card safe">
            <div class="icon">🛡️</div>
            <span class="badge badge-success">AMAN</span>
            <h3>Demo 4: Pencarian Aman</h3>
            <p>Pencarian dengan parameterized query dan validasi input — injeksi UNION tidak akan berfungsi.</p>
        </a>
    </div>

    <div class="info-box">
        <strong>💡 Cara Menggunakan Lab Ini:</strong><br><br>
        1. Jalankan <code>setup.sql</code> di MySQL untuk menyiapkan database.<br>
        2. Sesuaikan kredensial DB di <code>config.php</code>.<br>
        3. Letakkan folder ini di dalam direktori <code>htdocs</code> (XAMPP) atau <code>www</code> (WAMP).<br>
        4. Akses via browser: <code>http://localhost/sql-injection-demo/</code><br>
        5. Coba Demo 1 & 2 (versi rentan), lalu bandingkan dengan Demo 3 & 4 (versi aman).
    </div>
</div>

<footer class="footer">
    Dibuat untuk keperluan edukasi — Tugas UTS Pemrograman Web &nbsp;|&nbsp; SQL Injection Demo Lab
</footer>

</body>
</html>
