<?php
require 'config.php';
$title = 'Log Aktivitas';

$log_baru = $pdo->query(
    'SELECT l.id_log, l.aktivitas, l.id_pelanggan, p.nama AS nama_sekarang, l.waktu
     FROM log_pelanggan l LEFT JOIN pelanggan p ON p.id_pelanggan = l.id_pelanggan
     ORDER BY l.id_log DESC LIMIT 50'
)->fetchAll();

$log_ubah = $pdo->query(
    'SELECT id_log, id_pelanggan, nama_lama, nama_baru, waktu
     FROM log_perubahan_pelanggan ORDER BY id_log DESC LIMIT 50'
)->fetchAll();

include 'includes/header.php';
?>
<h1>Log Aktivitas</h1>
<p class="sub">Dicatat otomatis oleh trigger <code>after_insert_pelanggan</code> dan <code>after_update_pelanggan</code>.</p>

<div class="card">
    <h2>Pelanggan Baru</h2>
    <?php tbl($log_baru, ['id_log' => 'ID Log', 'aktivitas' => 'Aktivitas', 'id_pelanggan' => 'ID Pelanggan', 'nama_sekarang' => 'Nama Saat Ini', 'waktu' => 'Waktu']); ?>
</div>
<div class="card">
    <h2>Perubahan Data Pelanggan</h2>
    <?php tbl($log_ubah, ['id_log' => 'ID Log', 'id_pelanggan' => 'ID Pelanggan', 'nama_lama' => 'Nama Lama', 'nama_baru' => 'Nama Baru', 'waktu' => 'Waktu']); ?>
</div>
<?php include 'includes/footer.php'; ?>
