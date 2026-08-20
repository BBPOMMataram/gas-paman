<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/excel_export.php';
require_once __DIR__ . '/views/includes/tabel-rekap-edukasi.php';
cek_login();
cek_admin();

$agen    = (int)($_GET['agen'] ?? 0);
$status  = $_GET['status'] ?? '';
$mulai   = $_GET['mulai'] ?? '';
$selesai = $_GET['selesai'] ?? '';
if (!in_array($status, ['', 'draft', 'pending', 'approved', 'revisi'], true)) $status = '';
$mulai   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $mulai) ? $mulai : '';
$selesai = preg_match('/^\d{4}-\d{2}-\d{2}$/', $selesai) ? $selesai : '';

$rekap = rekap_edukasi_ambil($pdo, $agen, $mulai, $selesai, $status);
$rows  = $rekap['rows'];
$files = $rekap['files'];

$labelStatus = [
    'approved' => 'Disetujui',
    'revisi'   => 'Revisi',
    'draft'    => 'Draft',
    'pending'  => 'Pending',
];

$subjudul = 'Semua agen'
    . ($agen ? ' (filter: ' . ((string)$agen) . ')' : '')
    . ' · Periode: ' . ($mulai && $selesai ? $mulai . ' s/d ' . $selesai : ($mulai ? 'dari ' . $mulai : ($selesai ? 'sampai ' . $selesai : 'semua tanggal')))
    . ($status !== '' ? ' · Status: ' . $labelStatus[$status] : '')
    . ' · Diunduh: ' . date('d/m/Y H:i');

$headers = ['No', 'Tanggal', 'Agen', 'Nama Konsumen', 'JK', 'Usia', 'Pekerjaan', 'No. HP', 'Nilai Pre', 'Nilai Post', 'Lokasi', 'Status', 'Jumlah Foto'];
$fmt = ['int', 'date', 'text', 'text', 'text', 'int', 'text', 'text', 'num1', 'num1', 'text', 'text', 'int'];

$spreadsheet = excel_spreadsheet_baru();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Log Laporan Admin');

$baris = 1;
$baris = excel_tulis_judul($sheet, $baris, 'LOG LAPORAN EDUKASI — ADMIN', count($headers), $subjudul);
$baris = excel_tulis_header($sheet, $baris, $headers);

$no = 1;
foreach ($rows as $r) {
    $lokasi = trim($r['lokasi'] ?? '') !== '' ? $r['lokasi'] : ($r['alamat'] ?? '');
    $baris = excel_tulis_baris($sheet, $baris, [
        $no++,
        $r['tanggal'],
        trim(($r['nama_agen'] ?? '') . ($r['kode_agen'] ? ' (' . $r['kode_agen'] . ')' : '')),
        $r['nama_konsumen'],
        $r['jenis_kelamin'] ?? '',
        $r['usia'] ?? '',
        $r['pekerjaan'] ?? '',
        $r['no_hp'] ?? '',
        $r['nilai_pre_test'],
        $r['nilai_post_test'],
        $lokasi,
        $labelStatus[$r['status_review']] ?? $r['status_review'],
        count($files[$r['id']] ?? []),
    ], $fmt);
}
if ($no === 1) {
    $baris = excel_tulis_baris($sheet, $baris, array_fill(0, count($headers), ''), $fmt);
    $sheet->mergeCells('A' . ($baris - 1) . ':' . excel_huruf_kolom(count($headers)) . ($baris - 1));
    $sheet->setCellValue('A' . ($baris - 1), 'Belum ada data edukasi.');
    $sheet->getStyle('A' . ($baris - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

excel_lebarkan($sheet, $headers, $baris - 1, 40);
excel_pengaturan_halaman($sheet, 'landscape');
excel_selesai_download($spreadsheet, 'log-laporan-admin-' . date('Ymd-His') . '.xlsx');
