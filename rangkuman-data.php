<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/ntb_helper.php';

cek_login();
cek_admin_atau_kabalai();

$tgl_mulai   = $_GET['mulai'] ?? date('Y-m-d', strtotime('-6 months'));
$tgl_selesai = $_GET['selesai'] ?? date('Y-m-d');
$filter_agen = $_GET['agen'] ?? '';

$params = [$tgl_mulai, $tgl_selesai];
$whereAgen = '';
if ($filter_agen !== '') {
    $whereAgen = ' AND c.user_id = ?';
    $params[] = (int)$filter_agen;
}

// Ringkasan global
$stmt = $pdo->prepare("
    SELECT
        COUNT(*) as total_laporan,
        COUNT(DISTINCT c.user_id) as total_agen_lapor,
        COUNT(DISTINCT c.nama_konsumen) as total_konsumen,
        SUM(CASE WHEN c.status_review = 'approved' THEN 1 ELSE 0 END) as total_approved,
        SUM(CASE WHEN c.status_review = 'pending' THEN 1 ELSE 0 END) as total_pending
    FROM catatan_harian c
    WHERE c.tanggal BETWEEN ? AND ? AND c.status_review != 'draft' $whereAgen
");
$stmt->execute($params);
$ringkas = $stmt->fetch();

// Per agen: jumlah laporan, konsumen, materi, lokasi
$stmt = $pdo->prepare("
    SELECT
        u.id as agen_uid,
        u.nama as nama_agen,
        u.agen_id as kode_agen,
        u.alamat as alamat_agen,
        COUNT(c.id) as jml_laporan,
        COUNT(DISTINCT c.nama_konsumen) as jml_konsumen,
        SUM(CASE WHEN c.status_review = 'approved' THEN 1 ELSE 0 END) as jml_approved,
        GROUP_CONCAT(DISTINCT NULLIF(c.lokasi, '') ORDER BY c.lokasi SEPARATOR ' | ') as lokasi_list,
        GROUP_CONCAT(DISTINCT LEFT(NULLIF(c.informasi, ''), 80) ORDER BY c.tanggal DESC SEPARATOR ' || ') as materi_sample
    FROM users u
    INNER JOIN catatan_harian c ON c.user_id = u.id
        AND c.tanggal BETWEEN ? AND ?
        AND c.status_review != 'draft'
        " . ($filter_agen !== '' ? ' AND c.user_id = ?' : '') . "
    WHERE u.role = 'agen'
    GROUP BY u.id
    ORDER BY jml_laporan DESC, u.nama ASC
");
$stmt->execute($params);
$perAgen = $stmt->fetchAll();

// Detail laporan (terbaru) untuk tabel rinci
$paramsDetail = [$tgl_mulai, $tgl_selesai];
$whereDetail = "c.tanggal BETWEEN ? AND ? AND c.status_review != 'draft'";
if ($filter_agen !== '') {
    $whereDetail .= ' AND c.user_id = ?';
    $paramsDetail[] = (int)$filter_agen;
}
$stmt = $pdo->prepare("
    SELECT c.id, c.tanggal, c.nama_konsumen, c.usia, c.jenis_kelamin, c.pekerjaan,
           c.informasi, c.lokasi, c.alamat, c.status_review,
           u.nama as nama_agen, u.agen_id as kode_agen
    FROM catatan_harian c
    JOIN users u ON u.id = c.user_id
    WHERE $whereDetail
    ORDER BY c.tanggal DESC, c.id DESC
    LIMIT 200
");
$stmt->execute($paramsDetail);
$detailLaporan = $stmt->fetchAll();

// Bukti kegiatan untuk setiap laporan pada tabel detail. Diambil sekaligus agar
// tidak membuat query baru untuk setiap baris laporan.
$buktiByLaporan = [];
if ($detailLaporan) {
    $idsLaporan = array_column($detailLaporan, 'id');
    $placeholderIds = implode(',', array_fill(0, count($idsLaporan), '?'));
    $stmtBukti = $pdo->prepare("SELECT catatan_id, file_path FROM catatan_files WHERE catatan_id IN ($placeholderIds) ORDER BY id ASC");
    $stmtBukti->execute($idsLaporan);
    foreach ($stmtBukti->fetchAll() as $bukti) {
        $buktiByLaporan[(int)$bukti['catatan_id']][] = $bukti['file_path'];
    }
}

// Sebaran lokasi (dari alamat/lokasi laporan)
$sebaran = [];
foreach ($detailLaporan as $d) {
    $key = trim($d['lokasi'] ?: ($d['alamat'] ?: 'Tidak diisi'));
    if ($key === '') $key = 'Tidak diisi';
    if (!isset($sebaran[$key])) $sebaran[$key] = 0;
    $sebaran[$key]++;
}
arsort($sebaran);
$sebaran = array_slice($sebaran, 0, 15, true);

// Materi edukasi (informasi) paling sering
$materiCount = [];
foreach ($detailLaporan as $d) {
    $m = trim($d['informasi'] ?? '');
    if ($m === '') continue;
    $short = mb_substr($m, 0, 60);
    $materiCount[$short] = ($materiCount[$short] ?? 0) + 1;
}
arsort($materiCount);
$materiCount = array_slice($materiCount, 0, 12, true);

$daftarAgen = $pdo->query("SELECT id, nama, agen_id FROM users WHERE role = 'agen' ORDER BY nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rangkuman Data | BBPOM GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css?v=4">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="bg-gray-50 flex flex-col md:flex-row min-h-screen">
    <?php include 'views/includes/sidebar.php'; ?>

    <main class="flex-1 p-4 md:p-10 overflow-y-auto">
        <div class="max-w-6xl mx-auto">
            <header class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Rangkuman Data</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">
                        Ringkasan laporan agen, sebaran lokasi, masyarakat yang diedukasi, dan materi edukasi.
                    </p>
                </div>
            </header>

            <form method="GET" class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 mb-8 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Dari</label>
                    <input type="date" name="mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Sampai</label>
                    <input type="date" name="selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none">
                </div>
                <div>
                    <label class="block text-[9px] font-black text-gray-400 uppercase mb-1">Agen</label>
                    <select name="agen" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none min-w-[180px]">
                        <option value="">Semua Agen</option>
                        <?php foreach ($daftarAgen as $a): ?>
                        <option value="<?= (int)$a['id'] ?>" <?= $filter_agen == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(($a['agen_id'] ? $a['agen_id'].' — ' : '') . $a['nama']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-6 py-3 bg-red-800 hover:bg-black text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Filter</button>
                <a href="rangkuman-data" class="px-6 py-3 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-xl">Reset</a>
            </form>

            <!-- Kartu ringkas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Laporan</p>
                    <p class="text-3xl font-black text-gray-900 mt-2"><?= (int)($ringkas['total_laporan'] ?? 0) ?></p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Agen Aktif Lapor</p>
                    <p class="text-3xl font-black text-orange-600 mt-2"><?= (int)($ringkas['total_agen_lapor'] ?? 0) ?></p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Masyarakat Diedukasi</p>
                    <p class="text-3xl font-black text-red-800 mt-2"><?= (int)($ringkas['total_konsumen'] ?? 0) ?></p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Approved / Pending</p>
                    <p class="text-3xl font-black text-gray-900 mt-2">
                        <span class="text-green-600"><?= (int)($ringkas['total_approved'] ?? 0) ?></span>
                        <span class="text-gray-300 text-xl">/</span>
                        <span class="text-orange-500"><?= (int)($ringkas['total_pending'] ?? 0) ?></span>
                    </p>
                </div>
            </div>

            <!-- Per agen -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-50">
                    <h2 class="font-black text-gray-900">Rangkuman per Agen</h2>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Jumlah laporan, masyarakat, lokasi, dan cuplikan materi edukasi</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase">Agen</th>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Laporan</th>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Masyarakat</th>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase text-center">Approved</th>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase">Lokasi Kegiatan</th>
                                <th class="px-5 py-4 text-[10px] font-black text-gray-400 uppercase">Materi (cuplikan)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($perAgen)): ?>
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data di periode ini.</td></tr>
                            <?php else: foreach ($perAgen as $a): ?>
                            <tr class="hover:bg-orange-50/20">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($a['nama_agen']) ?></p>
                                    <p class="text-[10px] text-orange-600 font-bold"><?= htmlspecialchars($a['kode_agen'] ?: '—') ?></p>
                                </td>
                                <td class="px-5 py-4 text-center font-black text-gray-900"><?= (int)$a['jml_laporan'] ?></td>
                                <td class="px-5 py-4 text-center font-black text-red-800"><?= (int)$a['jml_konsumen'] ?></td>
                                <td class="px-5 py-4 text-center font-bold text-green-600"><?= (int)$a['jml_approved'] ?></td>
                                <td class="px-5 py-4 text-xs text-gray-600 max-w-[200px]">
                                    <?= htmlspecialchars(mb_substr($a['lokasi_list'] ?? '—', 0, 120)) ?>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 max-w-[240px]">
                                    <?= htmlspecialchars(mb_substr(str_replace(' || ', '; ', $a['materi_sample'] ?? '—'), 0, 140)) ?>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-6">
                    <h3 class="font-black text-gray-900 mb-4">Sebaran Lokasi Kegiatan</h3>
                    <ul class="space-y-2">
                        <?php if (empty($sebaran)): ?>
                        <li class="text-sm text-gray-400 italic">Belum ada data</li>
                        <?php else: foreach ($sebaran as $lok => $cnt): ?>
                        <li class="flex justify-between gap-3 text-sm">
                            <span class="font-semibold text-gray-700 truncate"><?= htmlspecialchars($lok) ?></span>
                            <span class="font-black text-orange-600 shrink-0"><?= $cnt ?></span>
                        </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
                <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-6">
                    <h3 class="font-black text-gray-900 mb-4">Materi Edukasi Terbanyak</h3>
                    <ul class="space-y-2">
                        <?php if (empty($materiCount)): ?>
                        <li class="text-sm text-gray-400 italic">Belum ada data</li>
                        <?php else: foreach ($materiCount as $mat => $cnt): ?>
                        <li class="flex justify-between gap-3 text-sm">
                            <span class="font-semibold text-gray-700 truncate"><?= htmlspecialchars($mat) ?></span>
                            <span class="font-black text-red-800 shrink-0"><?= $cnt ?></span>
                        </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Detail laporan -->
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-50 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div>
                        <h2 class="font-black text-gray-900">Detail Laporan</h2>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Maks. 200 data terbaru sesuai filter</p>
                    </div>
                    <div class="export-actions">
                        <a href="export-laporan?mulai=<?= urlencode($tgl_mulai) ?>&selesai=<?= urlencode($tgl_selesai) ?><?= $filter_agen ? '&agen='.(int)$filter_agen : '' ?>"
                           class="export-button export-button-excel">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="cetak-laporan?mulai=<?= urlencode($tgl_mulai) ?>&selesai=<?= urlencode($tgl_selesai) ?><?= $filter_agen ? '&agen='.(int)$filter_agen : '' ?>" target="_blank" rel="noopener"
                           class="export-button export-button-print">
                            <i class="fas fa-print"></i> Cetak PDF
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Agen</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Konsumen</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Tempat</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Materi Edukasi</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Bukti Kegiatan</th>
                                <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($detailLaporan)): ?>
                            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">Belum ada laporan.</td></tr>
                            <?php else: foreach ($detailLaporan as $d): ?>
                            <tr class="hover:bg-orange-50/20">
                                <td class="px-4 py-3 text-xs font-semibold text-gray-600 whitespace-nowrap"><?= htmlspecialchars($d['tanggal']) ?></td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="font-bold text-gray-800"><?= htmlspecialchars($d['nama_agen']) ?></span>
                                    <span class="block text-[10px] text-orange-600"><?= htmlspecialchars($d['kode_agen'] ?: '') ?></span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-700">
                                    <?= htmlspecialchars($d['nama_konsumen']) ?>
                                    <span class="block text-[10px] text-gray-400"><?= htmlspecialchars(trim(($d['jenis_kelamin'] ?? '').' '.($d['usia'] ?? '').' '.($d['pekerjaan'] ?? ''))) ?></span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 max-w-[160px]">
                                    <?= htmlspecialchars($d['lokasi'] ?: ($d['alamat'] ?: '—')) ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px]">
                                    <?= htmlspecialchars(mb_substr($d['informasi'] ?? '—', 0, 100)) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php $buktiLaporan = $buktiByLaporan[(int)$d['id']] ?? []; ?>
                                    <?php if ($buktiLaporan): ?>
                                    <div class="report-proof-list">
                                        <?php foreach ($buktiLaporan as $nomorBukti => $fileBukti):
                                            $isVideo = preg_match('/\.mp4$/i', $fileBukti);
                                            $namaUnduh = basename($fileBukti);
                                        ?>
                                        <a href="uploads/<?= rawurlencode($fileBukti) ?>" download="<?= htmlspecialchars($namaUnduh) ?>"
                                           class="report-proof-download" title="Unduh <?= $isVideo ? 'video' : 'gambar' ?> bukti <?= $nomorBukti + 1 ?>">
                                            <?php if ($isVideo): ?>
                                            <i class="fas fa-video"></i>
                                            <?php else: ?>
                                            <img src="uploads/<?= rawurlencode($fileBukti) ?>" alt="Bukti kegiatan <?= $nomorBukti + 1 ?>" loading="lazy">
                                            <?php endif; ?>
                                            <span><?= $isVideo ? 'Video' : 'Gambar' ?> <?= $nomorBukti + 1 ?></span>
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest
                                        <?= $d['status_review']==='approved'?'bg-green-100 text-green-700':($d['status_review']==='revisi'?'bg-red-100 text-red-700':'bg-orange-100 text-orange-700') ?>">
                                        <?= htmlspecialchars($d['status_review']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mb-8">
                Untuk peta sebaran & grafik demografi, buka juga menu <a href="analytics" class="text-red-800 font-bold hover:underline">Analytics</a>.
            </p>
        </div>
    </main>
    <script src="assets/js/futuristik.js?v=2"></script>
</body>
</html>
