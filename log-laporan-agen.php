<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();

if ($_SESSION['role'] !== 'agen') {
    header("Location: dashboard");
    exit;
}

$userId = $_SESSION['user_id'];

$status = $_GET['status'] ?? '';
$mulai  = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';
if (!in_array($status, ['', 'draft', 'pending', 'approved', 'revisi'], true)) $status = '';
$mulai  = preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) ? $mulai : '';
$selesai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) ? $selesai : '';

require_once __DIR__ . '/views/includes/tabel-rekap-edukasi.php';
$rekap = rekap_edukasi_ambil($pdo, $userId, $mulai, $selesai, $status);
$rows  = $rekap['rows'];
$files = $rekap['files'];

// Link cetak & export mengikuti filter saat ini
$filterParams = http_build_query(array_filter(['mulai' => $mulai, 'selesai' => $selesai, 'status' => $status], fn($v) => $v !== ''));
$cetakUrl  = 'cetak-log-laporan' . ($filterParams !== '' ? '?' . $filterParams : '');
$excelUrl  = 'export-log-agen' . ($filterParams !== '' ? '?' . $filterParams : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Laporan | BBPOM GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css?v=4">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row min-h-screen">

    <?php include 'views/includes/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-10 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <header class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Log Laporan</h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium italic">
                        Rekap semua masyarakat yang sudah Anda edukasi, lengkap dengan foto bukti kegiatan.
                    </p>
                </div>
                <div class="export-actions">
                    <a href="<?= htmlspecialchars($excelUrl) ?>"
                       class="export-button export-button-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="<?= htmlspecialchars($cetakUrl) ?>" target="_blank" rel="noopener"
                       class="export-button export-button-print">
                        <i class="fas fa-print"></i> Cetak PDF
                    </a>
                </div>
            </header>

            <form method="GET" class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 mb-6 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                        <option value="">Semua (kecuali draft)</option>
                        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="revisi" <?= $status === 'revisi' ? 'selected' : '' ?>>Revisi</option>
                        <option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Dari</label>
                    <input type="date" name="mulai" value="<?= htmlspecialchars($mulai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Sampai</label>
                    <input type="date" name="selesai" value="<?= htmlspecialchars($selesai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <button type="submit" class="px-6 py-3 bg-red-800 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Filter</button>
                <a href="log-laporan-agen" class="px-6 py-3 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Reset</a>
                <span class="ml-auto text-sm font-bold text-gray-500">Total: <span class="text-red-800 text-lg"><?= count($rows) ?></span> data</span>
            </form>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <?php rekap_edukasi_tabel($rows, $files, false, 'screen'); ?>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/futuristik.js?v=2"></script>
    <?php if (!empty($_SESSION['flash_message'])): ?>
    <script>
    Swal.fire({
        icon: '<?= addslashes($_SESSION['flash_type'] ?? 'success') ?>',
        title: '<?= ($_SESSION['flash_type'] ?? '') === 'error' ? 'Gagal' : 'Berhasil' ?>',
        text: '<?= addslashes($_SESSION['flash_message']) ?>',
        confirmButtonColor: '#991b1b',
        customClass: { popup: 'rounded-[40px]' }
    });
    </script>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>
</body>
</html>
