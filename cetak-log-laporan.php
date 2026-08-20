<?php
/**
 * Halaman cetak Log Laporan Edukasi Masyarakat (GAS-PAMAN).
 * - Agen: selalu mencetak data edukasinya sendiri (param agen diabaikan).
 * - Admin: bisa memfilter per agen / rentang tanggal / status.
 * Cetak via window.print() → browser "Simpan sebagai PDF" (A4 landscape).
 */
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['agen', 'admin'], true)) {
    header("Location: dashboard");
    exit;
}

require_once __DIR__ . '/views/includes/tabel-rekap-edukasi.php';

// Agen hanya boleh cetak datanya sendiri
$agenId = ($role === 'agen') ? (int)$_SESSION['user_id'] : (int)($_GET['agen'] ?? 0);
$mulai  = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';
$status = $_GET['status'] ?? '';
$mulai  = preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) ? $mulai : '';
$selesai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) ? $selesai : '';
if (!in_array($status, ['', 'draft', 'pending', 'approved', 'revisi'], true)) $status = '';

$rekap = rekap_edukasi_ambil($pdo, $agenId, $mulai, $selesai, $status);
$rows  = $rekap['rows'];
$files = $rekap['files'];

// Info agen untuk header cetak
$namaAgen = '';
$kodeAgen = '';
if ($agenId) {
    $stmtA = $pdo->prepare("SELECT nama, agen_id FROM users WHERE id = ?");
    $stmtA->execute([$agenId]);
    $a = $stmtA->fetch();
    if ($a) {
        $namaAgen = $a['nama'];
        $kodeAgen = $a['agen_id'] ?? '';
    }
}

$periode = 'Semua Periode';
if ($mulai && $selesai) {
    $periode = date('d/m/Y', strtotime($mulai)) . ' s/d ' . date('d/m/Y', strtotime($selesai));
} elseif ($mulai) {
    $periode = 'Dari ' . date('d/m/Y', strtotime($mulai));
} elseif ($selesai) {
    $periode = 'Sampai ' . date('d/m/Y', strtotime($selesai));
}

$labelStatus = [
    'approved' => 'Disetujui',
    'revisi'   => 'Revisi',
    'draft'    => 'Draft',
    'pending'  => 'Pending',
];

// Kembali ke halaman asal sesuai role
$backParams = http_build_query(array_filter([
    $role === 'admin' ? 're_agen' : 'agen' => $agenId ?: '',
    $role === 'admin' ? 're_mulai' : 'mulai' => $mulai,
    $role === 'admin' ? 're_selesai' : 'selesai' => $selesai,
    $role === 'admin' ? 're_status' : 'status' => $status,
], fn($v) => $v !== ''));
$backHref = ($role === 'admin' ? 'log-laporan' : 'log-laporan-agen') . ($backParams !== '' ? '?' . $backParams : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Log Laporan | BBPOM GAS-PAMAN</title>
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
            .rekap-table .rekap-foto img { width: 18mm; height: 13mm; }
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
                    <h1 style="margin-top:6px">LOG LAPORAN EDUKASI MASYARAKAT</h1>
                </div>
            </div>

            <div class="meta">
                <div><b>Periode</b><?= htmlspecialchars($periode) ?></div>
                <div><b>Status</b><?= htmlspecialchars($labelStatus[$status] ?? 'Semua (kecuali draft)') ?></div>
                <div><b>Jumlah Data</b><?= count($rows) ?> data</div>
                <div><b>Agen</b><?= $namaAgen !== '' ? htmlspecialchars($namaAgen) . ($kodeAgen ? ' (' . htmlspecialchars($kodeAgen) . ')' : '') : 'Semua Agen' ?></div>
                <div><b>Tanggal Cetak</b><?= date('d/m/Y H:i') ?></div>
            </div>

            <?php rekap_edukasi_tabel($rows, $files, ($role === 'admin'), 'print'); ?>
        </div>
    </div>

    <?php if (!empty($rows)): ?>
    <script>
    // Tunggu font & semua gambar siap (maks ±2 detik) supaya hasil cetak tidak terpotong
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
    window.addEventListener('afterprint', function() {
        if (history.length > 1) { history.back(); }
        else { location.href = '<?= htmlspecialchars($backHref) ?>'; }
    });
    </script>
    <?php endif; ?>
</body>
</html>
