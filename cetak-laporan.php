<?php
/**
 * Halaman cetak Laporan Edukasi Masyarakat — rangkuman 8 kolom (GAS-PAMAN).
 * Admin & kabalai. Dipanggil dari analytics.php (param tgl_mulai/tgl_selesai)
 * atau rangkuman-data.php (param mulai/selesai/agen).
 * Cetak via window.print() → browser "Simpan sebagai PDF" (A4 landscape).
 */
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();
cek_admin_atau_kabalai();

// Filter: analytics.php mengirim tgl_mulai/tgl_selesai,
// rangkuman-data.php mengirim mulai/selesai (+ agen). Terima keduanya.
$tglMulai   = $_GET['tgl_mulai'] ?? $_GET['mulai'] ?? '';
$tglSelesai = $_GET['tgl_selesai'] ?? $_GET['selesai'] ?? '';
$agenId     = isset($_GET['agen']) ? (int)$_GET['agen'] : 0;

$where = [];
$params = [];
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglMulai)) {
    $where[] = 'ch.tanggal >= ?';
    $params[] = $tglMulai;
}
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglSelesai)) {
    $where[] = 'ch.tanggal <= ?';
    $params[] = $tglSelesai;
}
if ($agenId > 0) {
    $where[] = 'ch.user_id = ?';
    $params[] = $agenId;
}

$namaAgen = '';
if ($agenId > 0) {
    $stmtA = $pdo->prepare("SELECT nama FROM users WHERE id = ?");
    $stmtA->execute([$agenId]);
    $namaAgen = (string)($stmtA->fetchColumn() ?: '');
}

$query = "SELECT ch.id, ch.tanggal, u.nama as nama_agen, ch.nama_konsumen, ch.jumlah_peserta, ch.informasi, ch.lokasi, ch.status_review
          FROM catatan_harian ch
          JOIN users u ON ch.user_id = u.id";
if ($where) {
    $query .= " WHERE " . implode(' AND ', $where);
}
$query .= " ORDER BY ch.tanggal DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$periode = 'Semua Periode';
if ($tglMulai && $tglSelesai) {
    $periode = date('d/m/Y', strtotime($tglMulai)) . ' s/d ' . date('d/m/Y', strtotime($tglSelesai));
} elseif ($tglMulai) {
    $periode = 'Dari ' . date('d/m/Y', strtotime($tglMulai));
} elseif ($tglSelesai) {
    $periode = 'Sampai ' . date('d/m/Y', strtotime($tglSelesai));
}

$labelStatus = [
    'approved' => 'Disetujui',
    'revisi'   => 'Revisi',
    'draft'    => 'Draft',
    'pending'  => 'Pending',
];

// Kembali ke halaman asal: analytics kirim tgl_mulai/tgl_selesai, rangkuman-data kirim mulai/selesai/agen
$dariAnalytics = isset($_GET['tgl_mulai']) || isset($_GET['tgl_selesai']);
if ($dariAnalytics) {
    $backParams = http_build_query(array_filter(['mulai' => $tglMulai, 'selesai' => $tglSelesai], fn($v) => $v !== ''));
    $backHref = 'analytics' . ($backParams !== '' ? '?' . $backParams : '');
} else {
    $backParams = http_build_query(array_filter(['mulai' => $tglMulai, 'selesai' => $tglSelesai, 'agen' => $agenId ?: ''], fn($v) => $v !== ''));
    $backHref = 'rangkuman-data' . ($backParams !== '' ? '?' . $backParams : '');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan | BBPOM GAS-PAMAN</title>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #eef0f3; color: #111; margin: 0; }
        .cetak-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 60px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .toolbar a, .toolbar button {
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
            background: #991b1b; color: #fff; border: 0; cursor: pointer;
            font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
            padding: 14px 24px; border-radius: 14px;
        }
        .toolbar a { background: #f3f4f6; color: #374151; }
        .lembar { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .kepala { display: flex; gap: 16px; align-items: center; border-bottom: 3px double #991b1b; padding-bottom: 16px; margin-bottom: 16px; }
        .kepala img { width: 64px; height: 64px; object-fit: contain; }
        .kepala h1 { margin: 0; font-size: 16px; letter-spacing: 0.02em; }
        .kepala .sub { font-size: 11px; color: #444; margin-top: 2px; }
        .meta { display: flex; flex-wrap: wrap; gap: 8px 32px; font-size: 11px; margin: 12px 0 16px; }
        .meta b { display: block; font-size: 8px; text-transform: uppercase; letter-spacing: 0.15em; color: #6b7280; }
        .rekap-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .rekap-table th, .rekap-table td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; vertical-align: top; }
        .rekap-table th { background: #f8fafc; font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: #6b7280; }
        @page { size: A4 landscape; margin: 10mm 12mm; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .cetak-wrap { max-width: none; padding: 0; }
            .lembar { box-shadow: none; border-radius: 0; padding: 0; }
            .kepala { padding-bottom: 8px; margin-bottom: 10px; }
            .kepala img { width: 44px; height: 44px; }
            .kepala h1 { font-size: 14px; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            .rekap-table th, .rekap-table td { border: 0.5pt solid #444; font-size: 9.5pt; padding: 3mm 2mm; }
        }
    </style>
</head>
<body>
    <div class="cetak-wrap">
        <div class="toolbar no-print">
            <a href="<?= htmlspecialchars($backHref) ?>">&larr; Kembali</a>
            <button onclick="window.print()">Cetak / Simpan PDF</button>
        </div>

        <div class="lembar">
            <div class="kepala">
                <img src="views/gas-paman.png" alt="Logo">
                <div>
                    <h1>BALAI BESAR PENGAWAS OBAT DAN MAKANAN DI MATARAM</h1>
                    <div class="sub">Keluarga Sadar Obat dan Makanan Aman (GAS-PAMAN)</div>
                    <h1 style="margin-top:6px">LAPORAN EDUKASI MASYARAKAT</h1>
                </div>
            </div>

            <div class="meta">
                <div><b>Periode</b><?= htmlspecialchars($periode) ?></div>
                <div><b>Jumlah Data</b><?= count($rows) ?> data</div>
                <div><b>Agen</b><?= $namaAgen !== '' ? htmlspecialchars($namaAgen) : 'Semua Agen' ?></div>
                <div><b>Tanggal Cetak</b><?= date('d/m/Y H:i') ?></div>
            </div>

            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Agen</th>
                        <th>Komunitas</th>
                        <th>Jumlah Peserta</th>
                        <th>Informasi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:24px">Belum ada data laporan.</td></tr>
                    <?php else: ?>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= (int)$r['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($r['nama_agen']) ?></td>
                        <td><?= htmlspecialchars($r['nama_konsumen']) ?></td>
                        <td style="text-align:center"><?= (int)$r['jumlah_peserta'] ?></td>
                        <td><?= htmlspecialchars($r['informasi'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['lokasi'] ?? '') ?></td>
                        <td><?= htmlspecialchars($labelStatus[$r['status_review']] ?? $r['status_review']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($rows)): ?>
    <script>
    window.addEventListener('load', function () {
        var imgs = Array.prototype.slice.call(document.images || []);
        var gambar = imgs.length ? Promise.all(imgs.map(function (i) {
            if (i.complete) return Promise.resolve();
            return new Promise(function (r) { i.addEventListener('load', r); i.addEventListener('error', r); });
        })) : Promise.resolve();
        var font = (document.fonts && document.fonts.ready) || Promise.resolve();
        Promise.race([Promise.all([font, gambar]), new Promise(function (r) { setTimeout(r, 2000); })])
            .then(function () { window.print(); });
    });
    window.addEventListener('afterprint', function () {
        if (history.length > 1) { history.back(); }
        else { location.href = '<?= htmlspecialchars($backHref) ?>'; }
    });
    </script>
    <?php endif; ?>
</body>
</html>
