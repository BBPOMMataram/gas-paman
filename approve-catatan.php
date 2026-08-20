<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/log_laporan.php';
require_once 'core/notifikasi.php';

cek_login();
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $st = $pdo->prepare("SELECT c.*, u.nama as nama_agen FROM catatan_harian c JOIN users u ON u.id = c.user_id WHERE c.id = ?");
    $st->execute([$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("UPDATE catatan_harian SET status_review = 'approved' WHERE id = ?");
    if ($stmt->execute([$id])) {
        if ($row) {
            try {
                log_laporan($pdo, (int)$_SESSION['user_id'], 'approve', $id, 'Approve laporan ' . ($row['nama_konsumen'] ?? ''));
            } catch (Throwable $e) {}
            notifikasi_kirim(
                $pdo,
                (int)$row['user_id'],
                'laporan_approve',
                'Laporan disetujui',
                'Laporan untuk konsumen "' . ($row['nama_konsumen'] ?? '-') . '" telah disetujui admin.',
                'detail-catatan?id=' . $id,
                $id
            );
        }
        header("Location: riwayat?msg=approved");
        exit;
    }
    die("Gagal melakukan approve.");
}
header("Location: riwayat");
exit;
