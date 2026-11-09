<?php
require 'config.php';
$title = 'Pelanggan';
$q = trim($_GET['q'] ?? '');

$st = $pdo->prepare(
    'SELECT p.id_pelanggan, p.nama, p.kota, p.no_hp, p.email,
            v.jumlah_transaksi, v.total_belanja,
            KategoriBelanja(v.total_belanja) AS kategori
     FROM pelanggan p
     JOIN v_total_belanja_pelanggan v ON v.id_pelanggan = p.id_pelanggan
     WHERE p.nama LIKE :q OR p.kota LIKE :q
     ORDER BY p.id_pelanggan'
);
$st->execute([':q' => "%$q%"]);
$rows = $st->fetchAll();

include 'includes/header.php';
?>
<h1>Data Pelanggan</h1>
<p class="sub">Kelola data pelanggan toko.</p>

<div class="card">
    <div class="toolbar">
        <form method="get">
            <input type="text" name="q" placeholder="Cari nama atau kota..." value="<?= e($q) ?>">
            <button class="btn" type="submit">Cari</button>
            <?php if ($q !== ''): ?><a class="btn sec" href="pelanggan.php">Reset</a><?php endif; ?>
        </form>
        <a class="btn" href="pelanggan_form.php">+ Tambah Pelanggan</a>
    </div>

    <?php if (!$rows): ?>
        <p class="empty">Tidak ada pelanggan ditemukan.</p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>ID</th><th>Nama</th><th>Kota</th><th>No HP</th><th>Transaksi</th><th>Total Belanja</th><th>Kategori</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['id_pelanggan']) ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['kota']) ?></td>
                <td><?= e($r['no_hp']) ?></td>
                <td><?= e($r['jumlah_transaksi']) ?></td>
                <td class="num"><?= rupiah($r['total_belanja']) ?></td>
                <td><?= badge($r['kategori']) ?></td>
                <td><div class="actions">
                    <a class="btn sec sm" href="pelanggan_detail.php?id=<?= (int)$r['id_pelanggan'] ?>">Detail</a>
                    <a class="btn sm" href="pelanggan_form.php?id=<?= (int)$r['id_pelanggan'] ?>">Edit</a>
                    <form method="post" action="pelanggan_hapus.php" onsubmit="return confirm('Hapus pelanggan ini beserta seluruh transaksinya?')">
                        <input type="hidden" name="id" value="<?= (int)$r['id_pelanggan'] ?>">
                        <button class="btn danger sm" type="submit">Hapus</button>
                    </form>
                </div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
