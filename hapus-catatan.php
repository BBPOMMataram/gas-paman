<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/log_laporan.php';

cek_login();
$role = $_SESSION['role'] ?? '';

// Admin boleh hapus semua; agen hanya boleh hapus catatannya sendiri
if (!in_array($role, ['admin', 'agen'], true)) {
    header("Location: dashboard");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Tujuan kembali setelah hapus — hanya halaman rekap yang diizinkan (anti open redirect)
$kembali = '';
if (isset($_GET['kembali'])) {
    $calon = rawurldecode((string)$_GET['kembali']);
    if (preg_match('/^(log-laporan-agen|log-laporan)(?:\?[^\s#]*)?(?:#[^\s]*)?$/', $calon)
        && strpos($calon, '://') === false
        && strpos($calon, '//') === false) {
        $kembali = $calon;
    }
}
$redirect = $kembali !== '' ? $kembali : ($role === 'admin' ? 'riwayat?msg=success_delete' : 'log-laporan-agen');

if ($id > 0) {
    try {
        // Agen hanya boleh hapus miliknya sendiri
        if ($role === 'agen') {
            $stmtOwn = $pdo->prepare("SELECT id FROM catatan_harian WHERE id = ? AND user_id = ?");
            $stmtOwn->execute([$id, (int)$_SESSION['user_id']]);
            if (!$stmtOwn->fetch()) {
                $_SESSION['flash_message'] = 'Anda hanya bisa menghapus data edukasi milik Anda sendiri.';
                $_SESSION['flash_type'] = 'error';
                header("Location: " . $redirect);
                exit;
            }
        }

        $pdo->beginTransaction();

        // 1. Ambil nama file foto agar bisa dihapus dari folder
        $stmtFile = $pdo->prepare("SELECT file_path FROM catatan_files WHERE catatan_id = ?");
        $stmtFile->execute([$id]);
        $files = $stmtFile->fetchAll();

        foreach ($files as $f) {
            $path = "uploads/" . $f['file_path'];
            if (file_exists($path)) {
                unlink($path); // Hapus file fisik
            }
        }

        // 2. Hapus data di database (Cascade akan menghapus catatan_files otomatis jika FK diset)
        $stmtDelete = $pdo->prepare("DELETE FROM catatan_harian WHERE id = ?");
        $stmtDelete->execute([$id]);

        // 3. Bersihkan baris terkait yang tidak punya FK cascade:
        //    log_laporan dan notifikasi yang mereferensikan catatan ini,
        //    supaya tidak ada referensi yatim ke catatan yang sudah hilang.
        $pdo->prepare("DELETE FROM log_laporan WHERE catatan_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM notifikasi WHERE ref_id = ? AND tipe IN ('laporan_baru', 'laporan_approve', 'laporan_revisi')")->execute([$id]);

        $pdo->commit();

        // 4. Catat aksi hapus di log SETELAH commit (tanpa referensi catatan_id
        //    karena sudah terhapus). Dipanggil di luar transaksi karena
        //    log_laporan() menjalankan DDL (CREATE TABLE IF NOT EXISTS) yang
        //    memicu implicit commit di MySQL — kalau dipanggil di dalam
        //    transaksi, commit() berikutnya akan melempar error.
        try {
            log_laporan($pdo, (int)$_SESSION['user_id'], 'hapus', null, 'Menghapus laporan #' . $id . ($role === 'agen' ? ' (oleh agen)' : ''));
        } catch (Throwable $e) { /* jangan gagalkan hapus karena log */ }

        $_SESSION['flash_message'] = 'Data edukasi berhasil dihapus.';
        $_SESSION['flash_type'] = 'success';
        header("Location: " . $redirect);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Gagal menghapus data: " . $e->getMessage());
    }
} else {
    header("Location: " . $redirect);
}
exit;
