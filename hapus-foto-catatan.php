<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();

$file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
$catatan_id = isset($_GET['catatan_id']) ? (int)$_GET['catatan_id'] : 0;

if ($file_id > 0) {
    // Ambil nama file + pemilik catatan untuk cek otorisasi.
    // Hanya pemilik laporan (agen) atau admin yang boleh menghapus bukti.
    $stmt = $pdo->prepare("
        SELECT cf.file_path, ch.user_id
        FROM catatan_files cf
        JOIN catatan_harian ch ON ch.id = cf.catatan_id
        WHERE cf.id = ?
    ");
    $stmt->execute([$file_id]);
    $file = $stmt->fetch();

    $isOwner = $file && (int)$file['user_id'] === (int)$_SESSION['user_id'];
    $isAdmin = ($_SESSION['role'] ?? '') === 'admin';

    if ($file && ($isOwner || $isAdmin)) {
        // Hapus file dari folder
        if (file_exists("uploads/" . $file['file_path'])) {
            unlink("uploads/" . $file['file_path']);
        }
        // Hapus record dari database
        $pdo->prepare("DELETE FROM catatan_files WHERE id = ?")->execute([$file_id]);
    }
}
header("Location: edit-catatan?id=" . $catatan_id);
exit;
