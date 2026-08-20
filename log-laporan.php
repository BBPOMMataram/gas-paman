<?php
require_once 'config/database.php';
require_once 'core/auth.php';

cek_login();
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard');
    exit;
}

// Helper log (aman jika file belum ter-deploy)
$logHelperOk = false;
if (is_file(__DIR__ . '/core/log_laporan.php')) {
    require_once __DIR__ . '/core/log_laporan.php';
    $logHelperOk = function_exists('log_laporan_ensure_table');
}

$dbError = null;
$logs = [];
$daftar_agen = [];
$flashMsg = '';
$flashType = '';

// === Hapus log (satu / multi / semua) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_hapus_log'])) {
    try {
        if ($logHelperOk) {
            log_laporan_ensure_table($pdo);
        }
        $mode = $_POST['aksi_hapus_log'];
        if ($mode === 'satu' && !empty($_POST['log_id'])) {
            $lid = (int)$_POST['log_id'];
            $pdo->prepare("DELETE FROM log_laporan WHERE id = ?")->execute([$lid]);
            $flashMsg = 'Log berhasil dihapus.';
            $flashType = 'ok';
        } elseif ($mode === 'pilih' && !empty($_POST['log_ids']) && is_array($_POST['log_ids'])) {
            $ids = array_map('intval', $_POST['log_ids']);
            $ids = array_filter($ids, fn($x) => $x > 0);
            if ($ids) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $pdo->prepare("DELETE FROM log_laporan WHERE id IN ($placeholders)")->execute(array_values($ids));
                $flashMsg = count($ids) . ' log berhasil dihapus.';
                $flashType = 'ok';
            } else {
                $flashMsg = 'Tidak ada log yang dipilih.';
                $flashType = 'warn';
            }
        } elseif ($mode === 'semua') {
            // Hapus semua sesuai filter saat ini (atau semua jika tanpa filter)
            $filter_agen = $_POST['filter_agen'] ?? '';
            $aksiFilter  = $_POST['filter_aksi'] ?? '';
            $tgl_mulai   = $_POST['filter_mulai'] ?? '';
            $tgl_selesai = $_POST['filter_selesai'] ?? '';
            $delQ = "DELETE l FROM log_laporan l LEFT JOIN catatan_harian c ON c.id = l.catatan_id WHERE 1=1";
            $delP = [];
            if ($filter_agen !== '') {
                $delQ .= " AND (c.user_id = ? OR (l.user_id = ? AND l.aksi IN ('buat','edit')))";
                $delP[] = $filter_agen;
                $delP[] = $filter_agen;
            }
            if (in_array($aksiFilter, ['buat', 'edit', 'approve', 'revisi', 'hapus'], true)) {
                $delQ .= " AND l.aksi = ?";
                $delP[] = $aksiFilter;
            }
            if ($tgl_mulai && $tgl_selesai) {
                $delQ .= " AND DATE(l.created_at) BETWEEN ? AND ?";
                $delP[] = $tgl_mulai;
                $delP[] = $tgl_selesai;
            }
            $st = $pdo->prepare($delQ);
            $st->execute($delP);
            $flashMsg = $st->rowCount() . ' log berhasil dihapus.';
            $flashType = 'ok';
        }
    } catch (Throwable $e) {
        $flashMsg = 'Gagal menghapus log: ' . $e->getMessage();
        $flashType = 'err';
    }
}

try {
    if ($logHelperOk) {
        log_laporan_ensure_table($pdo);
    } else {
        // Fallback: coba buat tabel langsung
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS log_laporan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                catatan_id INT NULL,
                user_id INT NOT NULL,
                aksi ENUM('buat','edit','approve','revisi','hapus') NOT NULL,
                keterangan TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_log_user (user_id),
                INDEX idx_log_catatan (catatan_id),
                INDEX idx_log_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    $filter_agen = $_GET['agen'] ?? '';
    $aksiFilter  = $_GET['aksi'] ?? '';
    $tgl_mulai   = $_GET['mulai'] ?? '';
    $tgl_selesai = $_GET['selesai'] ?? '';

    $query = "
        SELECT l.*, u.nama as nama_aktor, u.role as role_aktor,
               c.nama_konsumen, agen.nama as nama_agen
        FROM log_laporan l
        JOIN users u ON u.id = l.user_id
        LEFT JOIN catatan_harian c ON c.id = l.catatan_id
        LEFT JOIN users agen ON agen.id = c.user_id
        WHERE 1=1
    ";
    $params = [];

    if ($filter_agen !== '') {
        $query .= " AND (c.user_id = ? OR (l.user_id = ? AND l.aksi IN ('buat','edit')))";
        $params[] = $filter_agen;
        $params[] = $filter_agen;
    }
    if (in_array($aksiFilter, ['buat', 'edit', 'approve', 'revisi', 'hapus'], true)) {
        $query .= " AND l.aksi = ?";
        $params[] = $aksiFilter;
    }
    if ($tgl_mulai && $tgl_selesai) {
        $query .= " AND DATE(l.created_at) BETWEEN ? AND ?";
        $params[] = $tgl_mulai;
        $params[] = $tgl_selesai;
    }

    $query .= " ORDER BY l.created_at DESC LIMIT 300";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    $daftar_agen = $pdo->query("SELECT id, nama FROM users WHERE role = 'agen' ORDER BY nama ASC")->fetchAll();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
    $filter_agen = $_GET['agen'] ?? '';
    $aksiFilter  = $_GET['aksi'] ?? '';
    $tgl_mulai   = $_GET['mulai'] ?? '';
    $tgl_selesai = $_GET['selesai'] ?? '';
}

// ==== Rekap Edukasi Masyarakat (tabel masyarakat teredukasi, bisa dicetak) ====
require_once __DIR__ . '/views/includes/tabel-rekap-edukasi.php';
$re_agen   = (int)($_GET['re_agen'] ?? 0);
$re_mulai  = $_GET['re_mulai'] ?? '';
$re_selesai = $_GET['re_selesai'] ?? '';
$re_status = $_GET['re_status'] ?? '';
$re_mulai  = preg_match('/^\d{4}-\d{2}-\d{2}$/', $re_mulai) ? $re_mulai : '';
$re_selesai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $re_selesai) ? $re_selesai : '';
if (!in_array($re_status, ['', 'draft', 'pending', 'approved', 'revisi'], true)) $re_status = '';
$rekapEdukasi = ['rows' => [], 'files' => []];
try {
    $rekapEdukasi = rekap_edukasi_ambil($pdo, $re_agen, $re_mulai, $re_selesai, $re_status);
} catch (Throwable $e) {
    error_log('log-laporan rekap edukasi: ' . $e->getMessage());
}

// Link cetak mengikuti filter rekap saat ini
$reCetakParams = http_build_query(array_filter([
    'agen' => $re_agen ?: '',
    'mulai' => $re_mulai,
    'selesai' => $re_selesai,
    'status' => $re_status,
], fn($v) => $v !== ''));

$labelAksi = [
    'buat'    => ['Buat Laporan', 'bg-orange-100 text-orange-700'],
    'edit'    => ['Edit Laporan', 'bg-blue-100 text-blue-700'],
    'approve' => ['Approve', 'bg-green-100 text-green-700'],
    'revisi'  => ['Revisi', 'bg-red-100 text-red-700'],
    'hapus'   => ['Hapus', 'bg-gray-100 text-gray-600'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Laporan Agen | BBPOM GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css?v=4">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row min-h-screen">

    <?php include 'views/includes/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-12 overflow-y-auto">
        <header class="mb-8">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Log Laporan Agen</h2>
            <p class="text-sm text-gray-500 mt-1 font-medium italic">
                Jejak aktivitas: kapan agen membuat / mengedit laporan, dan kapan admin menyetujui.
            </p>
            <a href="#rekap-edukasi" class="inline-flex items-center gap-2 mt-4 text-[10px] font-black uppercase tracking-widest text-red-800 hover:text-orange-600 transition-colors">
                <i class="fas fa-arrow-down"></i> Lihat Rekap Edukasi Masyarakat
            </a>
        </header>

        <?php if ($dbError): ?>
        <div class="bg-red-50 border border-red-100 text-red-700 p-6 rounded-2xl mb-8 text-sm font-semibold leading-relaxed">
            <p class="font-black uppercase text-xs tracking-widest mb-2">Gagal memuat log</p>
            <p class="mb-3"><?= htmlspecialchars($dbError) ?></p>
            <p class="text-xs text-red-500">
                Pastikan file <code class="bg-red-100 px-1 rounded">core/log_laporan.php</code> sudah di-upload,
                lalu jalankan SQL migrasi di database:
            </p>
            <pre class="mt-3 bg-white border border-red-100 rounded-xl p-4 text-[11px] overflow-x-auto text-gray-700">CREATE TABLE IF NOT EXISTS log_laporan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  catatan_id INT NULL,
  user_id INT NOT NULL,
  aksi ENUM('buat','edit','approve','revisi','hapus') NOT NULL,
  keterangan TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);</pre>
        </div>
        <?php endif; ?>

        <form id="log-aktivitas" method="GET" class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 mb-8 flex flex-wrap gap-4 items-end">
            <input type="hidden" name="re_agen" value="<?= htmlspecialchars((string)$re_agen) ?>">
            <input type="hidden" name="re_mulai" value="<?= htmlspecialchars($re_mulai) ?>">
            <input type="hidden" name="re_selesai" value="<?= htmlspecialchars($re_selesai) ?>">
            <input type="hidden" name="re_status" value="<?= htmlspecialchars($re_status) ?>">
            <div>
                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Agen</label>
                <select name="agen" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                    <option value="">Semua Agen</option>
                    <?php foreach ($daftar_agen as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($filter_agen ?? '') == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Jenis Aksi</label>
                <select name="aksi" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                    <option value="">Semua Aksi</option>
                    <?php foreach ($labelAksi as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($aksiFilter ?? '') === $k ? 'selected' : '' ?>><?= $v[0] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Dari</label>
                <input type="date" name="mulai" value="<?= htmlspecialchars($tgl_mulai ?? '') ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
            </div>
            <div>
                <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Sampai</label>
                <input type="date" name="selesai" value="<?= htmlspecialchars($tgl_selesai ?? '') ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
            </div>
            <button type="submit" class="px-6 py-3 bg-red-800 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Filter</button>
            <a href="log-laporan" class="px-6 py-3 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Reset</a>
        </form>

        <?php if (!empty($flashMsg)): ?>
        <div class="mb-6 p-4 rounded-2xl font-bold text-center border <?= $flashType === 'ok' ? 'bg-orange-100 text-orange-700 border-orange-200' : ($flashType === 'warn' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-100 text-red-700 border-red-200') ?>">
            <?= htmlspecialchars($flashMsg) ?>
        </div>
        <?php endif; ?>

        <form id="formHapusLog" method="POST" class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
            <input type="hidden" name="aksi_hapus_log" id="aksiHapusLog" value="">
            <input type="hidden" name="filter_agen" value="<?= htmlspecialchars($filter_agen ?? '') ?>">
            <input type="hidden" name="filter_aksi" value="<?= htmlspecialchars($aksiFilter ?? '') ?>">
            <input type="hidden" name="filter_mulai" value="<?= htmlspecialchars($tgl_mulai ?? '') ?>">
            <input type="hidden" name="filter_selesai" value="<?= htmlspecialchars($tgl_selesai ?? '') ?>">

            <!-- Toolbar hapus -->
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3 bg-gray-50/50">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="checkAllLog" class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Pilih Semua</span>
                </label>
                <button type="button" onclick="hapusLogTerpilih()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                </button>
                <button type="button" onclick="hapusLogSemua()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                    <i class="fas fa-trash mr-1"></i> Hapus Semua (Filter Saat Ini)
                </button>
                <span class="text-[10px] text-gray-400 font-semibold ml-auto" id="countSelected">0 dipilih</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-10"></th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Waktu</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pelaku</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Konsumen</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Agen Laporan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($logs as $log):
                            $lbl = $labelAksi[$log['aksi']] ?? [$log['aksi'], 'bg-gray-100 text-gray-600'];
                        ?>
                        <tr class="hover:bg-orange-50/30 transition-all">
                            <td class="px-4 py-5">
                                <input type="checkbox" name="log_ids[]" value="<?= (int)$log['id'] ?>" class="log-check w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" onchange="updateCountSelected()">
                            </td>
                            <td class="px-6 py-5 text-xs font-semibold text-gray-600 whitespace-nowrap">
                                <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?= $lbl[1] ?>"><?= $lbl[0] ?></span>
                            </td>
                            <td class="px-6 py-5 text-sm font-bold text-gray-800">
                                <?= htmlspecialchars($log['nama_aktor']) ?>
                                <span class="block text-[10px] text-gray-400 font-semibold uppercase"><?= htmlspecialchars($log['role_aktor']) ?></span>
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-700"><?= htmlspecialchars($log['nama_konsumen'] ?? '—') ?></td>
                            <td class="px-6 py-5 text-xs font-bold text-orange-700"><?= htmlspecialchars($log['nama_agen'] ?? '—') ?></td>
                            <td class="px-6 py-5 text-xs text-gray-500 max-w-[220px]"><?= htmlspecialchars($log['keterangan'] ?? '—') ?></td>
                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                <?php if (!empty($log['catatan_id'])): ?>
                                <a href="detail-catatan?id=<?= (int)$log['catatan_id'] ?>" class="text-red-800 font-black text-[10px] uppercase tracking-widest hover:text-orange-600 mr-3">Lihat</a>
                                <?php endif; ?>
                                <button type="button" onclick="hapusLogSatu(<?= (int)$log['id'] ?>)" class="text-red-600 font-black text-[10px] uppercase tracking-widest hover:text-red-800">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs) && !$dbError): ?>
                        <tr>
                            <td colspan="8" class="px-8 py-16 text-center italic text-gray-400">
                                Belum ada log aktivitas. Log terisi otomatis saat agen membuat/mengedit laporan atau admin menyetujui.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- ============ Rekap Edukasi Masyarakat ============ -->
        <section id="rekap-edukasi" class="mt-14">
            <header class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Rekap Edukasi Masyarakat</h2>
                    <p class="text-sm text-gray-500 mt-1 font-medium italic">
                        Tabel masyarakat yang diedukasi seluruh agen, lengkap dengan foto bukti kegiatan.
                    </p>
                    <a href="#log-aktivitas" class="inline-flex items-center gap-2 mt-3 text-[10px] font-black uppercase tracking-widest text-red-800 hover:text-orange-600 transition-colors">
                        <i class="fas fa-arrow-up"></i> Kembali ke Log Aktivitas
                    </a>
                </div>
                <div class="export-actions">
                    <a href="export-log-admin<?= $reCetakParams !== '' ? '?' . htmlspecialchars($reCetakParams) : '' ?>"
                       class="export-button export-button-excel">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="cetak-log-laporan<?= $reCetakParams !== '' ? '?' . htmlspecialchars($reCetakParams) : '' ?>" target="_blank" rel="noopener"
                       class="export-button export-button-print">
                        <i class="fas fa-print"></i> Cetak PDF
                    </a>
                </div>
            </header>

            <form method="GET" action="log-laporan#rekap-edukasi" class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 mb-6 flex flex-wrap gap-4 items-end">
                <input type="hidden" name="agen" value="<?= htmlspecialchars($filter_agen ?? '') ?>">
                <input type="hidden" name="aksi" value="<?= htmlspecialchars($aksiFilter ?? '') ?>">
                <input type="hidden" name="mulai" value="<?= htmlspecialchars($tgl_mulai ?? '') ?>">
                <input type="hidden" name="selesai" value="<?= htmlspecialchars($tgl_selesai ?? '') ?>">
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Agen</label>
                    <select name="re_agen" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                        <option value="">Semua Agen</option>
                        <?php foreach ($daftar_agen as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $re_agen == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</label>
                    <select name="re_status" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                        <option value="">Semua (kecuali draft)</option>
                        <option value="approved" <?= $re_status === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="pending" <?= $re_status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="revisi" <?= $re_status === 'revisi' ? 'selected' : '' ?>>Revisi</option>
                        <option value="draft" <?= $re_status === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Dari</label>
                    <input type="date" name="re_mulai" value="<?= htmlspecialchars($re_mulai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Sampai</label>
                    <input type="date" name="re_selesai" value="<?= htmlspecialchars($re_selesai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <button type="submit" class="px-6 py-3 bg-red-800 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Filter</button>
                <a href="log-laporan#rekap-edukasi" class="px-6 py-3 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Reset</a>
                <span class="ml-auto text-sm font-bold text-gray-500">Total: <span class="text-red-800 text-lg"><?= count($rekapEdukasi['rows']) ?></span> data</span>
            </form>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <?php rekap_edukasi_tabel($rekapEdukasi['rows'], $rekapEdukasi['files'], true, 'screen'); ?>
                </div>
            </div>
        </section>
    </main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('checkAllLog')?.addEventListener('change', function() {
    document.querySelectorAll('.log-check').forEach(cb => { cb.checked = this.checked; });
    updateCountSelected();
});
function updateCountSelected() {
    const n = document.querySelectorAll('.log-check:checked').length;
    const el = document.getElementById('countSelected');
    if (el) el.textContent = n + ' dipilih';
}
function hapusLogSatu(id) {
    Swal.fire({
        title: 'Hapus log ini?',
        text: 'Log yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.getElementById('formHapusLog');
        document.getElementById('aksiHapusLog').value = 'satu';
        // hapus checkbox lain, buat hidden input log_id
        let h = document.createElement('input');
        h.type = 'hidden'; h.name = 'log_id'; h.value = id;
        f.appendChild(h);
        f.submit();
    });
}
function hapusLogTerpilih() {
    const n = document.querySelectorAll('.log-check:checked').length;
    if (n === 0) {
        Swal.fire({ icon: 'info', title: 'Belum ada yang dipilih', text: 'Centang log yang ingin dihapus terlebih dahulu.', confirmButtonColor: '#ea580c' });
        return;
    }
    Swal.fire({
        title: 'Hapus ' + n + ' log terpilih?',
        text: 'Log yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        document.getElementById('aksiHapusLog').value = 'pilih';
        document.getElementById('formHapusLog').submit();
    });
}
function hapusLogSemua() {
    Swal.fire({
        title: 'Hapus SEMUA log sesuai filter saat ini?',
        text: 'Tindakan ini menghapus semua log yang tampil (atau seluruh log jika tanpa filter). Tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        document.getElementById('aksiHapusLog').value = 'semua';
        document.getElementById('formHapusLog').submit();
    });
}
</script>
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
