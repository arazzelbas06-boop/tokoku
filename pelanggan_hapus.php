<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $st = $pdo->prepare('DELETE FROM pelanggan WHERE id_pelanggan = ?');
        $st->execute([$id]);
        flash($st->rowCount() ? 'Pelanggan berhasil dihapus.' : 'Pelanggan tidak ditemukan.', $st->rowCount() ? 'ok' : 'err');
    }
}
go('pelanggan.php');
