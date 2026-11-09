<?php
// ===== Konfigurasi database (sesuaikan jika perlu) =====
$DB_HOST = '127.0.0.1';
$DB_NAME = 'toko';
$DB_USER = 'root';
$DB_PASS = '';

session_start();

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()) .
        '<br>Pastikan MySQL menyala dan database "toko" sudah diimpor.');
}

// ===== Fungsi bantu =====
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function rupiah($n) { return 'Rp' . number_format((float)$n, 0, ',', '.'); }

function flash($msg, $type = 'ok') { $_SESSION['flash'] = [$msg, $type]; }

function go($url) { header("Location: $url"); exit; }

// Menampilkan tabel. $cols = ['kolom' => 'Label'], $money = kolom berformat rupiah
function tbl(array $rows, array $cols, array $money = []) {
    if (!$rows) { echo '<p class="empty">Tidak ada data.</p>'; return; }
    echo '<div class="table-wrap"><table><thead><tr>';
    foreach ($cols as $label) echo '<th>' . e($label) . '</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        foreach ($cols as $key => $label) {
            $v = $r[$key] ?? '';
            if (in_array($key, $money, true)) echo '<td class="num">' . rupiah($v) . '</td>';
            else echo '<td>' . e($v) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

function badge($kategori) {
    $cls = ['Pelanggan Gold' => 'gold', 'Pelanggan Silver' => 'silver'][$kategori] ?? 'reguler';
    return '<span class="badge ' . $cls . '">' . e($kategori) . '</span>';
}
