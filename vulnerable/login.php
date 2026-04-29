<?php
// ============================================================
// vulnerable/login.php — Demo Login RENTAN SQL Injection
// ⚠️  JANGAN gunakan kode seperti ini di produksi!
// ============================================================

require_once '../config.php';

$result    = null;
$query_log = '';
$error_msg = '';
$username  = '';
$password  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = getConnection();

    // ❌ KODE RENTAN: input langsung digabung ke SQL tanpa sanitasi
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    // Simpan query untuk ditampilkan (tujuan edukasi)
    $query_log = $sql;

    $res = mysqli_query($conn, $sql);

    if ($res === false) {
        $error_msg = "Query error: " . mysqli_error($conn);
    } elseif (mysqli_num_rows($res) > 0) {
        $result = mysqli_fetch_assoc($res);
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Rentan — SQLi Demo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">⚠️ SQLi Demo Lab</div>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="login.php" class="active">Login Rentan</a>
        <a href="search.php">Pencarian Rentan</a>
        <a href="../secure/login.php">Login Aman</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <span class="badge badge-danger">RENTAN — SQL INJECTION</span>
        <h1>Demo 1: Bypass Login</h1>
        <p>Halaman login ini rentan terhadap SQL Injection. Coba payload di bawah!</p>
    </div>

    <!-- ── KARTU FORM ── -->
    <div class="card danger">
        <h2>🔓 Form Login (Versi Rentan)</h2>

        <?php if ($result): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span>
                <div>
                    <strong>Login BERHASIL sebagai: <?= htmlspecialchars($result['username']) ?></strong><br>
                    Role: <strong><?= htmlspecialchars($result['role']) ?></strong> |
                    Email: <?= htmlspecialchars($result['email']) ?>
                </div>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error_msg): ?>
            <div class="alert alert-danger">
                <span class="icon">❌</span>
                <div>Login gagal. Username atau password salah.</div>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-warning">
                <span class="icon">⚠️</span>
                <div><strong>SQL Error:</strong> <?= htmlspecialchars($error_msg) ?></div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username) ?>"
                       placeholder="Coba: admin' --"
                       autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="(bisa diisi apa saja)">
            </div>
            <button type="submit" class="btn btn-danger">Login</button>
        </form>
    </div>

    <!-- ── QUERY LOG ── -->
    <?php if ($query_log): ?>
    <div class="card">
        <h2>🔍 Query yang Dieksekusi</h2>
        <div class="code-block"><?= htmlspecialchars($query_log) ?></div>
        <?php if ($result): ?>
        <div class="alert alert-warning" style="margin-top:14px;margin-bottom:0">
            <span class="icon">💡</span>
            <div>Perhatikan bagaimana <strong>'--</strong> membuat sisa query (pengecekan password) menjadi komentar yang diabaikan!</div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── PAYLOAD SUGGESTIONS ── -->
    <div class="card">
        <h2>🎯 Coba Payload Ini (isi di kolom Username)</h2>
        <p style="color:#888;font-size:0.85rem;margin-bottom:12px">
            Klik untuk menyalin, lalu paste ke kolom username di atas. Password bisa diisi apa saja.
        </p>
        <div class="payload-list">
            <span class="payload-chip" onclick="copyPayload(this, &quot;admin' --&quot;)">admin' --</span>
            <span class="payload-chip" onclick="copyPayload(this, &quot;admin'-- -&quot;)">admin'-- -</span>
            <span class="payload-chip" onclick="copyPayload(this, &quot;' OR '1'='1&quot;)">' OR '1'='1</span>
            <span class="payload-chip" onclick="copyPayload(this, &quot;' OR 1=1 --&quot;)">' OR 1=1 --</span>
            <span class="payload-chip" onclick="copyPayload(this, &quot;admin'/*&quot;)">admin'/*</span>
        </div>
    </div>

    <!-- ── PENJELASAN ── -->
    <div class="card">
        <h2>📖 Kenapa Ini Bisa Terjadi?</h2>
        <div class="info-box">
            <strong>Kode rentan di server (vulnerable/login.php):</strong><br><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#f1c40f">
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            </span><br><br>
            Ketika kamu memasukkan <code style="color:#e44d42">admin' --</code> sebagai username, query menjadi:<br><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;">
                <span style="color:#ccc">SELECT * FROM users WHERE username = '</span><span style="color:#e44d42">admin' --</span><span style="color:#555"> ' AND password = '...'</span>
            </span><br><br>
            Tanda <code style="color:#e44d42">'</code> menutup string username lebih awal.
            Tanda <code style="color:#e44d42">--</code> membuat sisa baris menjadi komentar.
            Database hanya mengeksekusi: <code style="color:#f1c40f">SELECT * FROM users WHERE username = 'admin'</code>
        </div>
    </div>

    <div style="text-align:center;margin-top:10px">
        <a href="../secure/login.php" style="color:#27ae60;text-decoration:none;font-size:0.9rem">
            → Lihat versi AMAN menggunakan Prepared Statements
        </a>
    </div>
</div>

<footer class="footer">
    Dibuat untuk keperluan edukasi — SQL Injection Demo Lab
</footer>

<script>
function copyPayload(el, text) {
    // Isi otomatis ke input username
    document.querySelector('input[name="username"]').value = text;
    el.style.background = '#1a0a1a';
    el.style.borderColor = '#e44d42';
    setTimeout(() => {
        el.style.background = '';
        el.style.borderColor = '';
    }, 600);
}
</script>

</body>
</html>
