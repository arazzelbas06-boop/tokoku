<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $st = $pdo->prepare('DELETE FROM transaksi WHERE id_transaksi = ?');
        $st->execute([$id]);
        flash($st->rowCount() ? 'Transaksi berhasil dihapus.' : 'Transaksi tidak ditemukan.', $st->rowCount() ? 'ok' : 'err');
    }
}
go('transaksi.php');
