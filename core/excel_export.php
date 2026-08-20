<?php
/**
 * core/excel_export.php
 * Helper bersama export Excel (.xlsx) GAS-PAMAN — semua endpoint export satu gaya.
 * Memakai PhpSpreadsheet (composer) supaya tabel rapi: judul merge merah, header
 * putih-bold merah, border tipis, zebra, auto-filter, freeze pane, landscape fit-to-width.
 */
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

const EXCEL_WARNA_MERAH = 'FF991B1B'; // red-800 aplikasi
const EXCEL_WARNA_ZEBRA = 'FFF1F5F9'; // slate-100

/** Konversi nomor kolom (1-based) jadi huruf (1=>A, 28=>AB). */
function excel_huruf_kolom(int $n): string
{
    $s = '';
    while ($n > 0) {
        $n--;
        $s = chr(65 + ($n % 26)) . $s;
        $n = intdiv($n, 26);
    }
    return $s;
}

/** Border tipis abu muda untuk semua sisi. */
function excel_border_thin(): array
{
    return [
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
        ],
    ];
}

/** Spreadsheet baru + properti dokumen. */
function excel_spreadsheet_baru(): Spreadsheet
{
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getProperties()
        ->setCreator('GAS-PAMAN BBPOM')
        ->setTitle('Export GAS-PAMAN');
    return $spreadsheet;
}

/**
 * Baris judul (merge penuh, putih-bold 14pt, fill merah) + subjudul abu italic.
 * Return nomor baris berikutnya.
 */
function excel_tulis_judul($sheet, int $baris, string $judul, int $jmlKolom, string $subjudul = ''): int
{
    $akhir = excel_huruf_kolom($jmlKolom);

    $sheet->mergeCells("A{$baris}:{$akhir}{$baris}");
    $sheet->setCellValue("A{$baris}", $judul);
    $sheet->getStyle("A{$baris}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => EXCEL_WARNA_MERAH]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);
    $sheet->getRowDimension($baris)->setRowHeight(30);
    $baris++;

    if ($subjudul !== '') {
        $sheet->mergeCells("A{$baris}:{$akhir}{$baris}");
        $sheet->setCellValue("A{$baris}", $subjudul);
        $sheet->getStyle("A{$baris}")->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($baris)->setRowHeight(18);
        $baris++;
    }
    return $baris;
}

/**
 * Baris header tabel: putih-bold merah, border tipis, tengah + wrap, tinggi 24.
 * Dipasang auto-filter + freeze pane (kolom terkunci di bawah header).
 * Return nomor baris berikutnya.
 */
function excel_tulis_header($sheet, int $baris, array $headers): int
{
    foreach (array_values($headers) as $i => $h) {
        $sheet->getCell([$i + 1, $baris])->setValue($h);
    }
    $akhir = excel_huruf_kolom(count($headers));
    $sheet->getStyle("A{$baris}:{$akhir}{$baris}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => EXCEL_WARNA_MERAH]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    ] + excel_border_thin());
    $sheet->getRowDimension($baris)->setRowHeight(24);

    $barisDataAwal = $baris + 1;
    $sheet->setAutoFilter("A{$baris}:{$akhir}{$baris}");
    $sheet->freezePane("A{$barisDataAwal}");
    return $barisDataAwal;
}

/**
 * Satu baris data. $fmt per kolom: 'text' | 'int' | 'num1' | 'date'.
 * 'num1' = angka 1 desimal; 'date' = tanggal yyyy-mm-dd (string 'Y-m-d' / DateTime).
 * Border tipis semua sel + zebra pada baris genap. Return nomor baris berikutnya.
 */
function excel_tulis_baris($sheet, int $baris, array $data, array $fmt): int
{
    $jmlKolom = count($data);
    foreach (array_values($data) as $i => $nilai) {
        $cell = $sheet->getCell([$i + 1, $baris]);
        $f = $fmt[$i] ?? 'text';
        $kosong = ($nilai === null || $nilai === '');
        switch ($f) {
            case 'int':
                $cell->setValue($kosong ? null : (int)$nilai);
                $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'num1':
                $cell->setValue($kosong ? null : (float)$nilai);
                if (!$kosong) $cell->getStyle()->getNumberFormat()->setFormatCode('0.0');
                $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            case 'date':
                if ($nilai instanceof DateTimeInterface) {
                    $cell->setValue(\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($nilai));
                } elseif (is_string($nilai) && preg_match('/^\d{4}-\d{2}-\d{2}/', $nilai)) {
                    $tgl = DateTimeImmutable::createFromFormat('Y-m-d', substr($nilai, 0, 10));
                    $cell->setValue($tgl !== false ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($tgl) : $nilai);
                } else {
                    $cell->setValue($nilai);
                }
                $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy-mm-dd');
                $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                break;
            default:
                $cell->setValueExplicit((string)$nilai, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                break;
        }
    }
    $akhir = excel_huruf_kolom($jmlKolom);
    $gaya = excel_border_thin() + [
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ];
    if ($baris % 2 === 0) {
        $gaya += ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => EXCEL_WARNA_ZEBRA]]];
    }
    $sheet->getStyle("A{$baris}:{$akhir}{$baris}")->applyFromArray($gaya);
    return $baris + 1;
}

/**
 * Lebarkan kolom menyesuaikan isi (perkiraan panjang teks) dengan cap.
 * Kolom yang isinya melebihi cap di-wrap text.
 */
function excel_lebarkan($sheet, array $headers, int $barisAkhir, int $cap = 40): void
{
    $jmlKolom = count($headers);
    for ($c = 1; $c <= $jmlKolom; $c++) {
        $max = mb_strlen((string)($headers[$c - 1] ?? ''));
        for ($r = 1; $r <= $barisAkhir; $r++) {
            $v = $sheet->getCell([$c, $r])->getValue();
            if ($v instanceof DateTimeInterface) {
                $max = max($max, 10); // yyyy-mm-dd
            } elseif ($v !== null && $v !== '') {
                $max = max($max, mb_strlen((string)$v));
            }
        }
        $lebar = max(10, min($cap, $max + 2));
        $sheet->getColumnDimensionByColumn($c)->setWidth($lebar);
        if ($max + 2 > $cap) {
            $sheet->getStyle([$c, 1, $c, $barisAkhir])->getAlignment()->setWrapText(true);
        }
    }
}

/** Orientasi + fit-to-width + margin kertas. */
function excel_pengaturan_halaman($sheet, string $orientasi = 'landscape'): void
{
    $sheet->getPageSetup()
        ->setOrientation($orientasi === 'portrait' ? PageSetup::ORIENTATION_PORTRAIT : PageSetup::ORIENTATION_LANDSCAPE)
        ->setFitToWidth(1)
        ->setFitToHeight(0);
    $sheet->getPageMargins()
        ->setTop(0.5)->setBottom(0.5)->setLeft(0.4)->setRight(0.4);
}

/** Bersihkan buffer, kirim header xlsx, download, selesai. */
function excel_selesai_download(Spreadsheet $spreadsheet, string $filename): void
{
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
