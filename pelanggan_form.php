<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);
$data = ['nama' => '', 'alamat' => '', 'kota' => '', 'no_hp' => '', 'email' => ''];

if ($id) {
    $st = $pdo->prepare('SELECT * FROM pelanggan WHERE id_pelanggan = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('Pelanggan tidak ditemukan.', 'err'); go('pelanggan.php'); }
    $data = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['nama', 'alamat', 'kota', 'no_hp', 'email'] as $f) $data[$f] = trim($_POST[$f] ?? '');

    if ($data['nama'] === '') $errors[] = 'Nama wajib diisi.';
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if ($data['no_hp'] !== '' && !preg_match('/^[0-9+]{6,15}$/', $data['no_hp'])) $errors[] = 'No HP hanya angka, 6-15 digit.';

    if (!$errors) {
        $v = array_map(fn($x) => $x === '' ? null : $x, $data);
        if ($id) {
            $st = $pdo->prepare('UPDATE pelanggan SET nama=?, alamat=?, kota=?, no_hp=?, email=? WHERE id_pelanggan=?');
            $st->execute([$v['nama'], $v['alamat'], $v['kota'], $v['no_hp'], $v['email'], $id]);
            flash('Data pelanggan berhasil diperbarui.');
        } else {
            $st = $pdo->prepare('INSERT INTO pelanggan (nama, alamat, kota, no_hp, email) VALUES (?,?,?,?,?)');
            $st->execute([$v['nama'], $v['alamat'], $v['kota'], $v['no_hp'], $v['email']]);
            flash('Pelanggan baru berhasil ditambahkan.');
        }
        go('pelanggan.php');
    }
}

$title = $id ? 'Edit Pelanggan' : 'Tambah Pelanggan';
include 'includes/header.php';
?>
<h1><?= e($title) ?></h1>
<p class="sub">Perubahan data akan tercatat otomatis oleh trigger di tabel log.</p>

<?php foreach ($errors as $er): ?><div class="alert err"><?= e($er) ?></div><?php endforeach; ?>

<div class="card form-card">
    <form method="post">
        <div class="form-row"><label>Nama *</label><input type="text" name="nama" maxlength="100" value="<?= e($data['nama']) ?>" required></div>
        <div class="form-row"><label>Alamat</label><input type="text" name="alamat" maxlength="150" value="<?= e($data['alamat']) ?>"></div>
        <div class="form-row"><label>Kota</label><input type="text" name="kota" maxlength="50" value="<?= e($data['kota']) ?>"></div>
        <div class="form-row"><label>No HP</label><input type="text" name="no_hp" maxlength="15" value="<?= e($data['no_hp']) ?>"></div>
        <div class="form-row"><label>Email</label><input type="text" name="email" maxlength="100" value="<?= e($data['email']) ?>"></div>
        <button class="btn" type="submit">Simpan</button>
        <a class="btn sec" href="pelanggan.php">Batal</a>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
