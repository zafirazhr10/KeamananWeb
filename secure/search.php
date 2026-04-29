<?php
// ============================================================
// secure/search.php — Pencarian AMAN menggunakan Prepared Statements
// ✅ Ini contoh yang BENAR — gunakan pola ini di produksi!
// ============================================================

require_once '../config.php';

$results   = [];
$query_log = '';
$search    = '';
$attempted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search    = $_POST['search'] ?? '';
    $attempted = true;

    // ✅ Validasi input: hanya huruf, angka, spasi, underscore
    $clean_search = preg_replace('/[^a-zA-Z0-9 _]/', '', $search);

    $conn = getConnection();

    // ✅ Prepared Statement dengan LIKE yang aman
    $param = "%" . $clean_search . "%";
    $stmt  = mysqli_prepare($conn, "SELECT id, username, email FROM users WHERE username LIKE ?");
    mysqli_stmt_bind_param($stmt, "s", $param);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $query_log = "SELECT id, username, email FROM users WHERE username LIKE ?\n"
               . "[Bind param] \"%" . htmlspecialchars($clean_search) . "%\"\n"
               . "[Input asli] \"" . htmlspecialchars($search) . "\"\n"
               . "[Setelah sanitasi] \"" . htmlspecialchars($clean_search) . "\"";

    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
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
    <title>Pencarian Aman — SQLi Demo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">⚠️ SQLi Demo Lab</div>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="../vulnerable/search.php">Pencarian Rentan</a>
        <a href="../secure/login.php">Login Aman</a>
        <a href="search.php" class="active">Pencarian Aman</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <span class="badge badge-success">AMAN — PREPARED STATEMENTS + VALIDASI</span>
        <h1>Demo 4: Pencarian Aman</h1>
        <p>Coba payload UNION injection yang sama — tidak akan berhasil mengekstrak data!</p>
    </div>

    <!-- ── FORM ── -->
    <div class="card success">
        <h2>🛡️ Pencarian User (Versi Aman)</h2>

        <form method="POST">
            <div class="form-group success">
                <label>Cari Username</label>
                <input type="text" name="search"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="Coba payload UNION — karakter spesial akan dihapus"
                       autocomplete="off">
            </div>
            <button type="submit" class="btn btn-success">Cari</button>
        </form>
    </div>

    <!-- ── HASIL ── -->
    <?php if ($attempted): ?>
    <div class="card">
        <h2>📊 Hasil Pencarian</h2>

        <?php if (empty($results)): ?>
            <div class="alert alert-info">
                <span class="icon">ℹ️</span>
                <div>Tidak ada user ditemukan. Payload SQL Injection tidak berpengaruh di sini.</div>
            </div>
        <?php else: ?>
            <div class="alert alert-success" style="margin-bottom:16px">
                <span class="icon">✅</span>
                <div><?= count($results) ?> user ditemukan — hanya dari tabel users, tidak ada kebocoran data!</div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Username</th><th>Email</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <td class="td-mono"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="td-mono"><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ── QUERY LOG ── -->
    <?php if ($query_log): ?>
    <div class="card">
        <h2>🔍 Proses Sanitasi & Query</h2>
        <div class="code-block" style="border-left-color:#27ae60"><?= $query_log ?></div>
        <div class="alert alert-success" style="margin-top:14px;margin-bottom:0">
            <span class="icon">🛡️</span>
            <div>
                Dua lapisan perlindungan diterapkan: <strong>(1)</strong> karakter spesial dihapus dari input,
                <strong>(2)</strong> nilai dikirim sebagai parameter terpisah via Prepared Statement.
                UNION injection tidak bisa lolos dari kedua lapisan ini.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── KODE PENJELASAN ── -->
    <div class="card">
        <h2>📖 Dua Lapisan Perlindungan</h2>
        <div class="info-box">
            <strong style="color:#27ae60">Lapisan 1 — Validasi Input (Whitelist):</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#2ecc71">
// Hanya izinkan huruf, angka, spasi, underscore
$clean_search = preg_replace('/[^a-zA-Z0-9 _]/', '', $search);
            </span>
            <br><br>
            <strong style="color:#27ae60">Lapisan 2 — Prepared Statement:</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#2ecc71">
$param = "%" . $clean_search . "%";
$stmt  = mysqli_prepare($conn, "SELECT id, username, email FROM users WHERE username LIKE ?");
mysqli_stmt_bind_param($stmt, "s", $param);
mysqli_stmt_execute($stmt);
            </span>
            <br><br>
            Dengan dua lapisan ini, karakter seperti <code style="color:#e44d42">'</code>, <code style="color:#e44d42">--</code>,
            dan perintah <code style="color:#e44d42">UNION</code> akan dihilangkan sebelum bahkan sampai ke query.
        </div>
    </div>

    <div style="text-align:center;margin-top:10px">
        <a href="../vulnerable/search.php" style="color:#e44d42;text-decoration:none;font-size:0.9rem">
            ← Kembali ke versi RENTAN untuk perbandingan
        </a>
    </div>
</div>

<footer class="footer">
    Dibuat untuk keperluan edukasi — SQL Injection Demo Lab
</footer>

</body>
</html>
