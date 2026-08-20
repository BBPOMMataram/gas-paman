<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/excel_export.php';
cek_login();
cek_admin_atau_kabalai();

$tgl_mulai   = $_GET['mulai'] ?? '';
$tgl_selesai = $_GET['selesai'] ?? '';
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

$headers = ['No', 'ID Agen', 'Nama', 'Email', 'Alamat', 'Jenis Kelamin', 'Usia', 'No. Telp', 'Pekerjaan', 'Kampus', 'Jurusan', 'Instansi', 'Pre-Test', 'Post-Test', 'Status', 'Terdaftar'];
$fmt = ['int', 'text', 'text', 'text', 'text', 'text', 'int', 'text', 'text', 'text', 'text', 'text', 'num1', 'num1', 'text', 'date'];

$subjudul = $filterPeriode
    ? 'Periode pendaftaran: ' . $tgl_mulai . ' s/d ' . $tgl_selesai . ' · Diunduh: ' . date('d/m/Y H:i')
    : 'Seluruh agen terdaftar · Diunduh: ' . date('d/m/Y H:i');

$spreadsheet = excel_spreadsheet_baru();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Daftar Agen');

$baris = 1;
$baris = excel_tulis_judul($sheet, $baris, 'DAFTAR AGEN GAS-PAMAN', count($headers), $subjudul);
$baris = excel_tulis_header($sheet, $baris, $headers);

$no = 1;
foreach ($rows as $r) {
    $baris = excel_tulis_baris($sheet, $baris, [
        $no++,
        $r['agen_id'] ?? '',
        $r['nama'] ?? '',
        $r['email'] ?? '',
        $r['alamat'] ?? '',
        $r['jenis_kelamin'] ?? '',
        $r['usia'] ?? '',
        $r['nomor_hp'] ?? '',
        $r['pekerjaan'] ?? '',
        $r['kampus'] ?? '',
        $r['jurusan'] ?? '',
        $r['nama_instansi'] ?? '',
        $r['nilai_pre'],
        $r['nilai_post'],
        $r['status'] ?? '',
        $r['created_at'] ?? '',
    ], $fmt);
}
if ($no === 1) {
    $baris = excel_tulis_baris($sheet, $baris, array_fill(0, count($headers), ''), $fmt);
    $sheet->mergeCells('A' . ($baris - 1) . ':' . excel_huruf_kolom(count($headers)) . ($baris - 1));
    $sheet->setCellValue('A' . ($baris - 1), 'Belum ada data agen.');
    $sheet->getStyle('A' . ($baris - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

excel_lebarkan($sheet, $headers, $baris - 1, 40);
excel_pengaturan_halaman($sheet, 'landscape');
excel_selesai_download($spreadsheet, 'daftar-agen-gaspaman-' . date('Ymd-His') . '.xlsx');
