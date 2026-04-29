<?php
// ============================================================
// secure/login.php — Login AMAN menggunakan Prepared Statements
// ✅ Ini contoh yang BENAR — gunakan pola ini di produksi!
// ============================================================

require_once '../config.php';

$result    = null;
$query_log = '';
$username  = '';
$attempted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = $_POST['username'] ?? '';
    $password  = $_POST['password'] ?? '';
    $attempted = true;

    $conn = getConnection();

    // ✅ PREPARED STATEMENT: query dan data diproses terpisah
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    // Log untuk edukasi (tampilkan query tanpa nilai asli)
    $query_log = "SELECT * FROM users WHERE username = ? AND password = ?\n"
               . "[Bind params] username = \"" . htmlspecialchars($username) . "\", password = \"" . str_repeat('*', strlen($password)) . "\"";

    if (mysqli_num_rows($res) > 0) {
        $result = mysqli_fetch_assoc($res);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Aman — SQLi Demo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">⚠️ SQLi Demo Lab</div>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="../vulnerable/login.php">Login Rentan</a>
        <a href="login.php" class="active">Login Aman</a>
        <a href="search.php">Pencarian Aman</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <span class="badge badge-success">AMAN — PREPARED STATEMENTS</span>
        <h1>Demo 3: Login Aman</h1>
        <p>Coba payload yang sama seperti di halaman rentan — tidak akan berhasil di sini!</p>
    </div>

    <!-- ── FORM ── -->
    <div class="card success">
        <h2>🔒 Form Login (Versi Aman)</h2>

        <?php if ($result): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span>
                <div>
                    <strong>Login berhasil sebagai: <?= htmlspecialchars($result['username']) ?></strong><br>
                    Role: <?= htmlspecialchars($result['role']) ?> | Email: <?= htmlspecialchars($result['email']) ?>
                </div>
            </div>
        <?php elseif ($attempted): ?>
            <div class="alert alert-danger">
                <span class="icon">🚫</span>
                <div>
                    <strong>Login gagal.</strong> Username atau password salah.<br>
                    <small style="color:#888">Payload SQL Injection tidak berpengaruh di sini.</small>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group success">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username) ?>"
                       placeholder="Coba payload: admin' --  (tidak akan berhasil)"
                       autocomplete="off">
            </div>
            <div class="form-group success">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Password asli: admin123">
            </div>
            <button type="submit" class="btn btn-success">Login</button>
        </form>
    </div>

    <!-- ── QUERY LOG ── -->
    <?php if ($query_log): ?>
    <div class="card">
        <h2>🔍 Cara Query Diproses</h2>
        <div class="code-block" style="border-left-color:#27ae60"><?= $query_log ?></div>
        <div class="alert alert-success" style="margin-top:14px;margin-bottom:0">
            <span class="icon">🛡️</span>
            <div>
                Input pengguna diperlakukan sebagai <strong>data murni</strong>, bukan bagian dari perintah SQL.
                Karakter seperti <code>'</code> dan <code>--</code> akan di-escape secara otomatis.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── PERBANDINGAN ── -->
    <div class="card">
        <h2>⚖️ Perbandingan: Rentan vs Aman</h2>
        <div class="info-box">
            <strong style="color:#e44d42">❌ Kode RENTAN (jangan dipakai):</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#e44d42">
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$res = mysqli_query($conn, $sql);
            </span>
            <br><br>
            <strong style="color:#27ae60">✅ Kode AMAN (gunakan ini):</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#2ecc71">
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND password = ?");
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
            </span>
            <br><br>
            Tanda tanya <code style="color:#27ae60">?</code> adalah placeholder. Database memproses query-nya terlebih dahulu,
            baru kemudian memasukkan nilai dari bind_param — sehingga nilai tersebut <strong>tidak bisa diinterpretasikan sebagai perintah SQL</strong>.
        </div>
    </div>

    <!-- ── AKUN VALID ── -->
    <div class="card">
        <h2>👤 Akun Valid untuk Login Normal</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Username</th><th>Password</th><th>Role</th></tr>
                </thead>
                <tbody>
                    <tr><td class="td-mono">admin</td><td class="td-mono">admin123</td><td>admin</td></tr>
                    <tr><td class="td-mono">budi</td><td class="td-mono">budi456</td><td>user</td></tr>
                    <tr><td class="td-mono">siti</td><td class="td-mono">siti789</td><td>user</td></tr>
                </tbody>
            </table>
        </div>
        <p style="color:#555;font-size:0.8rem;margin-top:10px">* Di aplikasi nyata, password harus di-hash menggunakan password_hash() + password_verify()</p>
    </div>

</div>

<footer class="footer">
    Dibuat untuk keperluan edukasi — SQL Injection Demo Lab
</footer>

</body>
</html>
