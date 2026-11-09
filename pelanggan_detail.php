<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);

// Memakai Stored Procedure CariPelanggan dan LaporanTransaksi
$st = $pdo->prepare('CALL CariPelanggan(?)');
$st->execute([$id]);
$p = $st->fetch();
$st->closeCursor();
if (!$p) { flash('Pelanggan tidak ditemukan.', 'err'); go('pelanggan.php'); }

$st = $pdo->prepare('CALL LaporanTransaksi(?)');
$st->execute([$id]);
$rows = $st->fetchAll();
$st->closeCursor();

$total = array_sum(array_column($rows, 'total'));
$st = $pdo->prepare('SELECT KategoriBelanja(?)');
$st->execute([$total]);
$kategori = $st->fetchColumn();

$title = 'Detail Pelanggan';
include 'includes/header.php';
?>
<h1><?= e($p['nama']) ?></h1>
<p class="sub">Detail pelanggan dan riwayat transaksinya.</p>

<div class="two">
    <div class="card">
        <h2>Data Pelanggan</h2>
        <table>
            <tr><th>ID</th><td><?= e($p['id_pelanggan']) ?></td></tr>
            <tr><th>Alamat</th><td><?= e($p['alamat'] ?: '-') ?></td></tr>
            <tr><th>Kota</th><td><?= e($p['kota'] ?: '-') ?></td></tr>
            <tr><th>No HP</th><td><?= e($p['no_hp'] ?: '-') ?></td></tr>
            <tr><th>Email</th><td><?= e($p['email'] ?: '-') ?></td></tr>
        </table>
    </div>
    <div class="card">
        <h2>Ringkasan Belanja</h2>
        <div class="stat" style="margin-bottom:12px"><div class="label">Total Belanja</div><div class="value"><?= rupiah($total) ?></div></div>
        <p>Kategori: <?= badge($kategori) ?></p>
        <p>Jumlah transaksi: <strong><?= count($rows) ?></strong></p>
    </div>
</div>

<div class="card">
    <div class="toolbar"><h2 style="margin:0">Riwayat Transaksi</h2>
        <a class="btn" href="transaksi_form.php?pelanggan=<?= (int)$p['id_pelanggan'] ?>">+ Tambah Transaksi</a></div>
    <?php tbl($rows, ['id_transaksi' => 'ID', 'tanggal' => 'Tanggal', 'nama_barang' => 'Barang', 'jumlah' => 'Jumlah', 'harga_satuan' => 'Harga Satuan', 'total' => 'Total'], ['harga_satuan', 'total']); ?>
</div>
<a class="btn sec" href="pelanggan.php">&larr; Kembali</a>
<?php include 'includes/footer.php'; ?>
