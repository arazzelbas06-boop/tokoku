<?php
require 'config.php';
$title = 'Dashboard';

// Memakai Stored Procedure HitungPelanggan (OUT jumlah)
$pdo->exec('CALL HitungPelanggan(@jml)');
$jml_pelanggan = $pdo->query('SELECT @jml')->fetchColumn();

$jml_transaksi = $pdo->query('SELECT COUNT(*) FROM transaksi')->fetchColumn();
$total_penjualan = $pdo->query('SELECT COALESCE(SUM(total),0) FROM transaksi')->fetchColumn();
// Memakai Stored Function HitungPajak (11%)
$pajak = $pdo->query('SELECT HitungPajak(COALESCE(SUM(total),0)) FROM transaksi')->fetchColumn();

$top_pelanggan = $pdo->query(
    'SELECT nama, jumlah_transaksi, total_belanja, KategoriBelanja(total_belanja) AS kategori
     FROM v_total_belanja_pelanggan ORDER BY total_belanja DESC LIMIT 5'
)->fetchAll();

$terbaru = $pdo->query(
    'SELECT id_transaksi, tanggal, nama_pelanggan, nama_barang, total
     FROM v_laporan_transaksi ORDER BY tanggal DESC, id_transaksi DESC LIMIT 5'
)->fetchAll();

include 'includes/header.php';
?>
<h1>Dashboard</h1>
<p class="sub">Ringkasan data penjualan toko.</p>

<div class="grid">
    <div class="stat"><div class="label">Jumlah Pelanggan</div><div class="value"><?= e($jml_pelanggan) ?></div></div>
    <div class="stat"><div class="label">Jumlah Transaksi</div><div class="value"><?= e($jml_transaksi) ?></div></div>
    <div class="stat"><div class="label">Total Penjualan</div><div class="value"><?= rupiah($total_penjualan) ?></div></div>
    <div class="stat"><div class="label">Pajak 11%</div><div class="value"><?= rupiah($pajak) ?></div></div>
</div>

<div class="two">
    <div class="card">
        <h2>Pelanggan dengan Belanja Terbanyak</h2>
        <?php if ($top_pelanggan): ?>
        <div class="table-wrap"><table>
            <thead><tr><th>Nama</th><th>Transaksi</th><th>Total Belanja</th><th>Kategori</th></tr></thead>
            <tbody>
            <?php foreach ($top_pelanggan as $r): ?>
                <tr><td><?= e($r['nama']) ?></td><td><?= e($r['jumlah_transaksi']) ?></td>
                    <td class="num"><?= rupiah($r['total_belanja']) ?></td><td><?= badge($r['kategori']) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php else: ?><p class="empty">Belum ada data.</p><?php endif; ?>
    </div>

    <div class="card">
        <h2>Transaksi Terbaru</h2>
        <?php tbl($terbaru, ['tanggal' => 'Tanggal', 'nama_pelanggan' => 'Pelanggan', 'nama_barang' => 'Barang', 'total' => 'Total'], ['total']); ?>
        <p style="margin:12px 0 0"><a class="btn sec sm" href="transaksi.php">Lihat semua transaksi</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
