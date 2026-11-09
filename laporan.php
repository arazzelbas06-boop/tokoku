<?php
require 'config.php';
$title = 'Laporan';

// 1. Total belanja per pelanggan (View + Function)
$total_belanja = $pdo->query(
    'SELECT nama, jumlah_transaksi, total_belanja, KategoriBelanja(total_belanja) AS kategori
     FROM v_total_belanja_pelanggan ORDER BY total_belanja DESC'
)->fetchAll();

// 2. Pelanggan yang belum pernah bertransaksi (Subquery NOT EXISTS)
$belum = $pdo->query(
    'SELECT p.id_pelanggan, p.nama, p.kota, p.no_hp
     FROM pelanggan p
     WHERE NOT EXISTS (SELECT 1 FROM transaksi t WHERE t.id_pelanggan = p.id_pelanggan)'
)->fetchAll();

// 3. Transaksi di atas rata-rata (Subquery AVG)
$rata = (float)$pdo->query('SELECT COALESCE(AVG(total),0) FROM transaksi')->fetchColumn();
$atas = $pdo->query(
    'SELECT t.id_transaksi, p.nama AS nama_pelanggan, t.tanggal, t.nama_barang, t.total
     FROM transaksi t JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan
     WHERE t.total > (SELECT AVG(total) FROM transaksi)
     ORDER BY t.total DESC'
)->fetchAll();

// 4. Pelanggan dengan transaksi terbanyak (Nested Query)
$terbanyak = $pdo->query(
    'SELECT p.nama, COUNT(t.id_transaksi) AS jumlah_transaksi
     FROM pelanggan p JOIN transaksi t ON t.id_pelanggan = p.id_pelanggan
     GROUP BY p.id_pelanggan, p.nama
     HAVING COUNT(t.id_transaksi) = (
         SELECT MAX(jml) FROM (SELECT COUNT(*) AS jml FROM transaksi GROUP BY id_pelanggan) AS hitung
     )'
)->fetchAll();

// 5. Lima transaksi terbesar
$top5 = $pdo->query(
    'SELECT id_transaksi, tanggal, nama_pelanggan, nama_barang, total
     FROM v_laporan_transaksi ORDER BY total DESC LIMIT 5'
)->fetchAll();

include 'includes/header.php';
?>
<h1>Laporan</h1>
<p class="sub">Laporan penjualan berdasarkan View, JOIN, dan Subquery.</p>

<div class="card">
    <h2>1. Total Belanja Setiap Pelanggan</h2>
    <?php if ($total_belanja): ?>
    <div class="table-wrap"><table>
        <thead><tr><th>Nama</th><th>Jumlah Transaksi</th><th>Total Belanja</th><th>Kategori</th></tr></thead>
        <tbody>
        <?php foreach ($total_belanja as $r): ?>
            <tr><td><?= e($r['nama']) ?></td><td><?= e($r['jumlah_transaksi']) ?></td>
                <td class="num"><?= rupiah($r['total_belanja']) ?></td><td><?= badge($r['kategori']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php else: ?><p class="empty">Tidak ada data.</p><?php endif; ?>
</div>

<div class="two">
    <div class="card">
        <h2>2. Pelanggan Belum Pernah Bertransaksi</h2>
        <?php tbl($belum, ['id_pelanggan' => 'ID', 'nama' => 'Nama', 'kota' => 'Kota', 'no_hp' => 'No HP']); ?>
    </div>
    <div class="card">
        <h2>3. Pelanggan dengan Transaksi Terbanyak</h2>
        <?php tbl($terbanyak, ['nama' => 'Nama', 'jumlah_transaksi' => 'Jumlah Transaksi']); ?>
    </div>
</div>

<div class="card">
    <h2>4. Transaksi di Atas Rata-rata <small style="color:#6b7280;font-weight:400">(rata-rata <?= rupiah($rata) ?>)</small></h2>
    <?php tbl($atas, ['id_transaksi' => 'ID', 'tanggal' => 'Tanggal', 'nama_pelanggan' => 'Pelanggan', 'nama_barang' => 'Barang', 'total' => 'Total'], ['total']); ?>
</div>

<div class="card">
    <h2>5. Lima Transaksi dengan Nilai Terbesar</h2>
    <?php tbl($top5, ['id_transaksi' => 'ID', 'tanggal' => 'Tanggal', 'nama_pelanggan' => 'Pelanggan', 'nama_barang' => 'Barang', 'total' => 'Total'], ['total']); ?>
</div>
<?php include 'includes/footer.php'; ?>
