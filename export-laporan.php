<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/excel_export.php';
cek_login();
cek_admin_atau_kabalai();

// Filter: analytics.php mengirim tgl_mulai/tgl_selesai,
// rangkuman-data.php mengirim mulai/selesai (+ agen). Terima keduanya.
$tglMulai  = $_GET['tgl_mulai'] ?? $_GET['mulai'] ?? '';
$tglSelesai = $_GET['tgl_selesai'] ?? $_GET['selesai'] ?? '';
$agenId    = isset($_GET['agen']) ? (int)$_GET['agen'] : 0;

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

$namaFilter = '';
if ($tglMulai && $tglSelesai) $namaFilter = "_{$tglMulai}_sampai_{$tglSelesai}";
elseif ($tglMulai) $namaFilter = "_dari_{$tglMulai}";
elseif ($tglSelesai) $namaFilter = "_sampai_{$tglSelesai}";

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

$labelStatus = [
    'approved' => 'Disetujui',
    'revisi'   => 'Revisi',
    'draft'    => 'Draft',
    'pending'  => 'Pending',
];

$headers = ['ID', 'Tanggal', 'Agen', 'Komunitas', 'Jumlah Peserta', 'Informasi', 'Lokasi', 'Status'];
$fmt = ['int', 'date', 'text', 'text', 'int', 'text', 'text', 'text'];

$subjudul = 'Periode: ' . ($tglMulai && $tglSelesai ? $tglMulai . ' s/d ' . $tglSelesai : ($tglMulai ? 'dari ' . $tglMulai : ($tglSelesai ? 'sampai ' . $tglSelesai : 'semua tanggal')))
    . ($namaAgen !== '' ? ' · Agen: ' . $namaAgen : '')
    . ' · Diunduh: ' . date('d/m/Y H:i');

$spreadsheet = excel_spreadsheet_baru();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Masyarakat');

$baris = 1;
$baris = excel_tulis_judul($sheet, $baris, 'LAPORAN MASYARAKAT BBPOM', count($headers), $subjudul);
$baris = excel_tulis_header($sheet, $baris, $headers);

foreach ($rows as $r) {
    $baris = excel_tulis_baris($sheet, $baris, [
        $r['id'],
        $r['tanggal'],
        $r['nama_agen'],
        $r['nama_konsumen'],
        $r['jumlah_peserta'],
        $r['informasi'],
        $r['lokasi'],
        $labelStatus[$r['status_review']] ?? $r['status_review'],
    ], $fmt);
}
if (empty($rows)) {
    $baris = excel_tulis_baris($sheet, $baris, array_fill(0, count($headers), ''), $fmt);
    $sheet->mergeCells('A' . ($baris - 1) . ':' . excel_huruf_kolom(count($headers)) . ($baris - 1));
    $sheet->setCellValue('A' . ($baris - 1), 'Belum ada data laporan.');
    $sheet->getStyle('A' . ($baris - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

excel_lebarkan($sheet, $headers, $baris - 1, 40);
excel_pengaturan_halaman($sheet, 'landscape');
excel_selesai_download($spreadsheet, "Laporan_BBPOM" . $namaFilter . "_" . date('Y-m-d') . ".xlsx");
