<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);
$data = [
    'id_pelanggan' => (int)($_GET['pelanggan'] ?? 0),
    'tanggal' => date('Y-m-d'),
    'nama_barang' => '',
    'jumlah' => 1,
    'harga_satuan' => '',
];

if ($id) {
    $st = $pdo->prepare('SELECT * FROM transaksi WHERE id_transaksi = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('Transaksi tidak ditemukan.', 'err'); go('transaksi.php'); }
    $data = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['id_pelanggan'] = (int)($_POST['id_pelanggan'] ?? 0);
    $data['tanggal']      = trim($_POST['tanggal'] ?? '');
    $data['nama_barang']  = trim($_POST['nama_barang'] ?? '');
    $data['jumlah']       = trim($_POST['jumlah'] ?? '');
    $data['harga_satuan'] = trim($_POST['harga_satuan'] ?? '');

    if (!$data['id_pelanggan']) $errors[] = 'Pilih pelanggan.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['tanggal'])) $errors[] = 'Tanggal tidak valid.';
    if ($data['nama_barang'] === '') $errors[] = 'Nama barang wajib diisi.';
    if (!ctype_digit((string)$data['jumlah']) || (int)$data['jumlah'] <= 0) $errors[] = 'Jumlah harus bilangan bulat lebih dari 0.';
    if (!is_numeric($data['harga_satuan']) || (float)$data['harga_satuan'] < 0) $errors[] = 'Harga satuan tidak valid (tidak boleh negatif).';

    if (!$errors) {
        try {
            // Total dihitung oleh Stored Function HitungTotal
            $st = $pdo->prepare('SELECT HitungTotal(?, ?)');
            $st->execute([$data['harga_satuan'], (int)$data['jumlah']]);
            $total = $st->fetchColumn();

            if ($id) {
                $st = $pdo->prepare('UPDATE transaksi SET id_pelanggan=?, tanggal=?, nama_barang=?, jumlah=?, harga_satuan=?, total=? WHERE id_transaksi=?');
                $st->execute([$data['id_pelanggan'], $data['tanggal'], $data['nama_barang'], (int)$data['jumlah'], $data['harga_satuan'], $total, $id]);
                flash('Transaksi berhasil diperbarui.');
            } else {
                // Trigger before_insert_transaksi akan memvalidasi data ini
                $st = $pdo->prepare('INSERT INTO transaksi (id_pelanggan, tanggal, nama_barang, jumlah, harga_satuan, total) VALUES (?,?,?,?,?,?)');
                $st->execute([$data['id_pelanggan'], $data['tanggal'], $data['nama_barang'], (int)$data['jumlah'], $data['harga_satuan'], $total]);
                flash('Transaksi berhasil ditambahkan.');
            }
            go('transaksi.php');
        } catch (PDOException $ex) {
            $errors[] = 'Database menolak data: ' . ($ex->errorInfo[2] ?? $ex->getMessage());
        }
    }
}

$pelanggan = $pdo->query('SELECT id_pelanggan, nama FROM pelanggan ORDER BY nama')->fetchAll();
$title = $id ? 'Edit Transaksi' : 'Tambah Transaksi';
include 'includes/header.php';
?>
<h1><?= e($title) ?></h1>
<p class="sub">Total dihitung otomatis dengan function <code>HitungTotal(harga, jumlah)</code>.</p>

<?php foreach ($errors as $er): ?><div class="alert err"><?= e($er) ?></div><?php endforeach; ?>

<div class="card form-card">
    <form method="post">
        <div class="form-row"><label>Pelanggan *</label>
            <select name="id_pelanggan" required>
                <option value="">-- Pilih pelanggan --</option>
                <?php foreach ($pelanggan as $p): ?>
                    <option value="<?= (int)$p['id_pelanggan'] ?>" <?= (int)$data['id_pelanggan'] === (int)$p['id_pelanggan'] ? 'selected' : '' ?>><?= e($p['nama']) ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="form-row"><label>Tanggal *</label><input type="date" name="tanggal" value="<?= e($data['tanggal']) ?>" required></div>
        <div class="form-row"><label>Nama Barang *</label><input type="text" name="nama_barang" maxlength="100" value="<?= e($data['nama_barang']) ?>" required></div>
        <div class="form-row"><label>Jumlah *</label><input type="number" id="jumlah" name="jumlah" min="1" step="1" value="<?= e($data['jumlah']) ?>" required></div>
        <div class="form-row"><label>Harga Satuan (Rp) *</label><input type="number" id="harga" name="harga_satuan" step="0.01" value="<?= e($data['harga_satuan']) ?>" required></div>
        <div class="total-box">Total: <span id="total">Rp0</span></div>
        <button class="btn" type="submit">Simpan</button>
        <a class="btn sec" href="transaksi.php">Batal</a>
    </form>
</div>
<script>
function hitung() {
    var t = (parseFloat(document.getElementById('harga').value) || 0) * (parseInt(document.getElementById('jumlah').value) || 0);
    document.getElementById('total').textContent = 'Rp' + Math.round(t).toLocaleString('id-ID');
}
document.getElementById('harga').addEventListener('input', hitung);
document.getElementById('jumlah').addEventListener('input', hitung);
hitung();
</script>
<?php include 'includes/footer.php'; ?>
