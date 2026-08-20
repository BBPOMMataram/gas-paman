<?php
/**
 * Halaman cetak Daftar Agen GAS-PAMAN — 16 kolom (A4 landscape).
 * Admin & kabalai. Param mulai/selesai (periode pendaftaran) sama dengan export-agen.php.
 * Cetak via window.print() → browser "Simpan sebagai PDF".
 */
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();
cek_admin_atau_kabalai();

$tgl_mulai   = $_GET['mulai'] ?? '';
$tgl_selesai = $_GET['selesai'] ?? '';
$tgl_mulai   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_mulai) ? $tgl_mulai : '';
$tgl_selesai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_selesai) ? $tgl_selesai : '';
$filterPeriode = ($tgl_mulai && $tgl_selesai);

$where = ["u.role = 'agen'"];
$params = [];
if ($filterPeriode) {
    $where[] = "DATE(u.created_at) BETWEEN ? AND ?";
    $params[] = $tgl_mulai;
    $params[] = $tgl_selesai;
}
$whereStr = implode(' AND ', $where);

$sql = "
    SELECT u.*,
        (SELECT ht.nilai FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id
         WHERE ht.user_id = u.id AND bs.jenis = 'pre_test'
         " . ($filterPeriode ? " AND DATE(ht.waktu_selesai) BETWEEN " . $pdo->quote($tgl_mulai) . " AND " . $pdo->quote($tgl_selesai) : "") . "
         ORDER BY ht.waktu_selesai DESC LIMIT 1) as nilai_pre,
        (SELECT ht.nilai FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id
         WHERE ht.user_id = u.id AND bs.jenis = 'post_test'
         " . ($filterPeriode ? " AND DATE(ht.waktu_selesai) BETWEEN " . $pdo->quote($tgl_mulai) . " AND " . $pdo->quote($tgl_selesai) : "") . "
         ORDER BY ht.waktu_selesai DESC LIMIT 1) as nilai_post
    FROM users u WHERE $whereStr ORDER BY u.nama ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$periode = 'Semua Periode';
if ($filterPeriode) {
    $periode = date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai));
}

$backParams = http_build_query(array_filter(['mulai' => $tgl_mulai, 'selesai' => $tgl_selesai], fn($v) => $v !== ''));
$backHref = 'daftar-agen' . ($backParams !== '' ? '?' . $backParams : '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Daftar Agen | BBPOM GAS-PAMAN</title>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #eef0f3; color: #111; margin: 0; }
        .cetak-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 16px 60px; }
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
        .rekap-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .rekap-table th, .rekap-table td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        .rekap-table th { background: #f8fafc; font-size: 8px; text-transform: uppercase; letter-spacing: 0.08em; color: #6b7280; }
        @page { size: A4 landscape; margin: 10mm 10mm; }
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
            .rekap-table th, .rekap-table td { border: 0.5pt solid #444; font-size: 8.5pt; padding: 2mm 1.5mm; }
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
                    <h1 style="margin-top:6px">DAFTAR AGEN GAS-PAMAN</h1>
                </div>
            </div>

            <div class="meta">
                <div><b>Periode</b><?= htmlspecialchars($periode) ?></div>
                <div><b>Jumlah Data</b><?= count($rows) ?> agen</div>
                <div><b>Tanggal Cetak</b><?= date('d/m/Y H:i') ?></div>
            </div>

            <table class="rekap-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Agen</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Alamat</th>
                        <th>JK</th>
                        <th>Usia</th>
                        <th>No. Telp</th>
                        <th>Pekerjaan</th>
                        <th>Kampus</th>
                        <th>Jurusan</th>
                        <th>Instansi</th>
                        <th>Pre-Test</th>
                        <th>Post-Test</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="16" style="text-align:center;padding:24px">Belum ada data agen.</td></tr>
                    <?php else: $no = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($r['agen_id'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['nama'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['alamat'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['jenis_kelamin'] ?? '') ?></td>
                        <td style="text-align:center"><?= (int)($r['usia'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($r['nomor_hp'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['pekerjaan'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['kampus'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['jurusan'] ?? '') ?></td>
                        <td><?= htmlspecialchars($r['nama_instansi'] ?? '') ?></td>
                        <td style="text-align:center"><?= $r['nilai_pre'] !== null ? number_format((float)$r['nilai_pre'], 1) : '-' ?></td>
                        <td style="text-align:center"><?= $r['nilai_post'] !== null ? number_format((float)$r['nilai_post'], 1) : '-' ?></td>
                        <td><?= htmlspecialchars($r['status'] ?? '') ?></td>
                        <td><?= $r['created_at'] ? date('d/m/Y', strtotime($r['created_at'])) : '' ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
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
