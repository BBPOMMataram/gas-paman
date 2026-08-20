<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();
cek_staff_atau_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: daftar-soal"); exit; }

$stmt = $pdo->prepare("SELECT id FROM bank_soal WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) { header("Location: daftar-soal"); exit; }

try {
    $pdo->beginTransaction();

    // Hapus riwayat test yang pernah dikerjakan untuk paket soal ini.
    // FK hasil_test.bank_soal_id tidak pakai ON DELETE CASCADE, jadi tanpa
    // langkah ini penghapusan selalu gagal begitu ada agen yang sudah
    // mengerjakan testnya.
    $stmtPids = $pdo->prepare("SELECT id FROM pertanyaan WHERE bank_soal_id = ?");
    $stmtPids->execute([$id]);
    $pids = $stmtPids->fetchAll(PDO::FETCH_COLUMN);

    if ($pids) {
        $ph = implode(',', array_fill(0, count($pids), '?'));
        $pdo->prepare("DELETE FROM detail_jawaban WHERE pertanyaan_id IN ($ph)")->execute($pids);
    }

    // Hapus set soal tersimpan milik hasil_test paket ini sebelum hasil_test dihapus
    require_once __DIR__ . '/core/soal_parser.php';
    hasil_test_soal_ensure_table($pdo);
    $stmtHtIds = $pdo->prepare("SELECT id FROM hasil_test WHERE bank_soal_id = ?");
    $stmtHtIds->execute([$id]);
    $htIds = $stmtHtIds->fetchAll(PDO::FETCH_COLUMN);
    if ($htIds) {
        $phHt = implode(',', array_fill(0, count($htIds), '?'));
        $pdo->prepare("DELETE FROM hasil_test_soal WHERE hasil_test_id IN ($phHt)")->execute($htIds);
    }

    $pdo->prepare("DELETE FROM hasil_test WHERE bank_soal_id = ?")->execute([$id]);

    // pertanyaan & opsi_jawaban terhapus otomatis via ON DELETE CASCADE
    $pdo->prepare("DELETE FROM bank_soal WHERE id = ?")->execute([$id]);

    $pdo->commit();
    $_SESSION['flash_message'] = 'Paket soal beserta riwayat test terkait berhasil dihapus.';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['flash_message'] = 'Gagal menghapus: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
}

header("Location: daftar-soal");
exit;
