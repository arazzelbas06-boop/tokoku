<?php
require 'config.php';
$title = 'Transaksi';

$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$idp = (int)($_GET['pelanggan'] ?? 0);

$where = [];
$params = [];
if ($dari !== '')   { $where[] = 't.tanggal >= ?'; $params[] = $dari; }
if ($sampai !== '') { $where[] = 't.tanggal <= ?'; $params[] = $sampai; }
if ($idp)           { $where[] = 't.id_pelanggan = ?'; $params[] = $idp; }
$sql = 'SELECT t.id_transaksi, t.tanggal, p.nama AS nama_pelanggan, t.nama_barang, t.jumlah, t.harga_satuan, t.total
        FROM transaksi t JOIN pelanggan p ON p.id_pelanggan = t.id_pelanggan'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
     . ' ORDER BY t.tanggal DESC, t.id_transaksi DESC';
$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();
$jumlah_total = array_sum(array_column($rows, 'total'));

$pelanggan = $pdo->query('SELECT id_pelanggan, nama FROM pelanggan ORDER BY nama')->fetchAll();

include 'includes/header.php';
?>
<h1>Data Transaksi</h1>
<p class="sub">Riwayat transaksi penjualan. Transaksi dengan total negatif akan ditolak oleh trigger.</p>

<div class="card">
    <div class="toolbar">
        <form method="get">
            <select name="pelanggan">
                <option value="0">Semua pelanggan</option>
                <?php foreach ($pelanggan as $p): ?>
                    <option value="<?= (int)$p['id_pelanggan'] ?>" <?= $idp === (int)$p['id_pelanggan'] ? 'selected' : '' ?>><?= e($p['nama']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="dari" value="<?= e($dari) ?>" title="Dari tanggal">
            <input type="date" name="sampai" value="<?= e($sampai) ?>" title="Sampai tanggal">
            <button class="btn" type="submit">Filter</button>
            <a class="btn sec" href="transaksi.php">Reset</a>
        </form>
        <a class="btn" href="transaksi_form.php">+ Tambah Transaksi</a>
    </div>

    <?php if (!$rows): ?>
        <p class="empty">Tidak ada transaksi.</p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Barang</th><th>Jumlah</th><th>Harga Satuan</th><th>Total</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['id_transaksi']) ?></td>
                <td><?= e($r['tanggal']) ?></td>
                <td><?= e($r['nama_pelanggan']) ?></td>
                <td><?= e($r['nama_barang']) ?></td>
                <td><?= e($r['jumlah']) ?></td>
                <td class="num"><?= rupiah($r['harga_satuan']) ?></td>
                <td class="num"><?= rupiah($r['total']) ?></td>
                <td><div class="actions">
                    <a class="btn sm" href="transaksi_form.php?id=<?= (int)$r['id_transaksi'] ?>">Edit</a>
                    <form method="post" action="transaksi_hapus.php" onsubmit="return confirm('Hapus transaksi ini?')">
                        <input type="hidden" name="id" value="<?= (int)$r['id_transaksi'] ?>">
                        <button class="btn danger sm" type="submit">Hapus</button>
                    </form>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr><th colspan="6" style="text-align:right">Total (<?= count($rows) ?> transaksi)</th><th class="num"><?= rupiah($jumlah_total) ?></th><th></th></tr></tfoot>
    </table></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
