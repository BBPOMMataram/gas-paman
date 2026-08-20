<?php
require_once 'config/database.php';
require_once 'core/auth.php';
require_once 'core/soal_parser.php';
cek_login();
cek_staff_atau_admin();
set_time_limit(60); // unduh Google Docs/Drive bisa butuh waktu

// php.ini mematikan log_errors — fatal PHP tidak akan tercatat sama sekali.
// Catat manual + tampilkan fallback ramah bila halaman terhenti di tengah jalan.
register_shutdown_function(function () {
    $e = error_get_last();
    if (!$e || !in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) return;
    @error_log('[import-soal FATAL] ' . $e['message'] . ' @ ' . $e['file'] . ':' . $e['line']);
    if (!headers_sent()) {
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"></head><body style="font-family:sans-serif;background:#fef2f2;color:#7f1d1d;padding:48px"><h2>Terjadi kendala teknis saat memproses impor soal</h2><p>Proses terhenti sebelum halaman selesai. Belum ada data yang tersimpan. Muat ulang dan coba lagi.</p></body></html>';
    } else {
        echo '<div style="position:fixed;left:0;right:0;bottom:0;background:#7f1d1d;color:#fff;padding:12px 16px;font:13px sans-serif;z-index:9999">Kendala teknis: proses impor terhenti di tengah jalan. Belum ada data yang tersimpan — muat ulang dan coba lagi.</div>';
    }
});

/**
 * Validasi & normalisasi data sesi import (anti-crash bila sesi berisi data
 * versi lama: jawaban berupa huruf, opsi tak lengkap, dsb).
 * Return array ternormalisasi (jawaban selalu int|null) atau null bila rusak.
 */
function import_soal_normalisasi($data) {
    if (!is_array($data) || !isset($data['soal']) || !is_array($data['soal'])) return null;
    $ok = [];
    foreach ($data['soal'] as $s) {
        if (!is_array($s) || !isset($s['teks']) || !is_array($s['opsi'] ?? null)) continue;
        $opsi = [];
        foreach ($s['opsi'] as $o) {
            if (!is_array($o) || trim((string)($o['teks'] ?? '')) === '') continue;
            $opsi[] = ['teks' => (string)$o['teks'], 'benar' => !empty($o['benar'])];
        }
        if (count($opsi) < 2) continue;
        $s['opsi'] = $opsi;
        $s['teks'] = (string)$s['teks'];
        $s['kategori'] = in_array($s['kategori'] ?? '', SOAL_KATEGORI_LIST, true) ? $s['kategori'] : 'umum';
        $s['jenis'] = in_array($s['jenis'] ?? '', ['pre_test', 'post_test'], true) ? $s['jenis'] : null;
        $s['jawaban'] = null;
        if (isset($s['jawaban']) && is_numeric($s['jawaban'])) {
            $j = (int)$s['jawaban'];
            if ($j >= 0 && $j < count($opsi)) $s['jawaban'] = $j; // huruf (data legacy) -> null
        }
        $s['nomor'] = isset($s['nomor']) && is_numeric($s['nomor']) ? (int)$s['nomor'] : null;
        $ok[] = $s;
    }
    if (!$ok) return null;
    $data['soal'] = $ok;
    return $data;
}

$swal_type = '';
$swal_msg  = '';

$mode = $_POST['mode'] ?? '';

/* ============ MODE PARSE: ambil link / teks -> preview ============ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'parse') {
    $judul   = trim($_POST['judul'] ?? '');
    $jenis   = in_array($_POST['jenis'] ?? '', ['pre_test', 'post_test', 'keduanya']) ? $_POST['jenis'] : 'pre_test';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $tanggal = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ? $tanggal : date('Y-m-d');
    $url     = trim($_POST['url'] ?? '');
    $teks    = trim($_POST['teks'] ?? '');
    $refresh = isset($_POST['refresh']) || isset($_GET['refresh']);

    $mentah = null;
    $sumber = '';

    if ($url !== '') {
        $mentah = soal_ambil_dari_link($url, $refresh);
        $sumber = 'link';
        if ($mentah === null) {
            unset($_SESSION['import_soal']); // jangan tampilkan preview lama yang tidak relevan
            $swal_type = 'error';
            $swal_msg  = 'Gagal mengambil konten dari link. Pastikan link bisa diakses publik (Google Docs/Drive harus di-share "Siapa saja yang memiliki link"). Atau tempel teks soalnya langsung di kolom teks. Bila link sama pernah gagal, tandai "Muat ulang tanpa cache".';
        }
    } elseif ($teks !== '') {
        $mentah = $teks;
        $sumber = 'teks';
    } else {
        $swal_type = 'warning';
        $swal_msg  = 'Isi link soal ATAU tempel teks soalnya terlebih dahulu.';
    }

    if ($mentah !== null) {
        $soalList = soal_parse_teks($mentah);
        if (count($soalList) === 0) {
            error_log('[import-soal] 0 soal terdeteksi | sumber=' . $sumber . ' | url=' . $url);
            unset($_SESSION['import_soal']);
            $swal_type = 'error';
            $swal_msg  = 'Tidak ada soal yang berhasil dikenali. Pastikan formatnya: nomor soal (1. / 1)), opsi (a. b. c. d.), dan jawaban ("Jawaban: b" / bagian "Kunci Jawaban") — atau gunakan form tambah soal manual. Bila link sama pernah gagal, tandai "Muat ulang tanpa cache".';
        } else {
            $_SESSION['import_soal'] = [
                'judul'   => $judul,
                'jenis'   => $jenis,
                'tanggal' => $tanggal,
                'soal'    => $soalList,
                'sumber'  => $sumber,
            ];
            if ($jenis === 'keduanya') {
                $adaJenis = false;
                foreach ($soalList as $s) {
                    if (!empty($s['jenis'])) { $adaJenis = true; break; }
                }
                if (!$adaJenis) {
                    $swal_type = 'warning';
                    $swal_msg  = "Tidak ada penanda 'PRE TEST'/'POST TEST' terdeteksi di dokumen — semua soal akan masuk ke KEDUA paket (Pre-Test & Post-Test identik). Bila ingin memecah manual, hilangkan centang 'Masukkan semua soal ke kedua paket' di preview.";
                }
            }
        }
    }
}

/* ============ MODE SAVE: simpan hasil preview ke bank soal ============ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'save') {
    $data = import_soal_normalisasi($_SESSION['import_soal'] ?? null);
    if (!$data) {
        unset($_SESSION['import_soal']);
        $swal_type = 'error';
        $swal_msg  = 'Sesi import kedaluwarsa atau rusak. Ulangi proses import dari awal.';
    } else {
        $judul   = trim($_POST['judul'] ?? $data['judul']);
        $jenis   = in_array($_POST['jenis'] ?? '', ['pre_test', 'post_test', 'keduanya']) ? $_POST['jenis'] : 'pre_test';
        $tanggal = $_POST['tanggal'] ?? $data['tanggal'];
        $tanggal = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ? $tanggal : date('Y-m-d');

        // Mode keduanya: semua soal masuk ke KEDUA paket bila toggle dicentang.
        $semuaKedua = ($jenis === 'keduanya') && (($_POST['semua_kedua'] ?? '0') === '1');

        $kategoriPerSoal = $_POST['kategori'] ?? []; // idx => kategori
        $jenisPerSoal    = $_POST['jenis_soal'] ?? []; // idx => pre/post (mode keduanya, split manual)
        $benarPerSoal    = $_POST['benar'] ?? [];    // idx => indeks opsi benar (radio)
        $ikutkan         = $_POST['ikutkan'] ?? [];  // idx => 1

        // Susun ulang soal yang dicentang dengan kategori + jenis + kunci final
        $soalFinal   = [];
        $jenisFinal  = [];
        $benarFinal  = [];
        $kurangKunci = [];
        foreach ($data['soal'] as $i => $s) {
            if (!isset($ikutkan[$i])) continue;
            $kat = $kategoriPerSoal[$i] ?? $s['kategori'];
            if (!in_array($kat, SOAL_KATEGORI_LIST, true)) $kat = $s['kategori'];
            $j = ($jenis === 'keduanya') ? 'pre_test' : $jenis;
            if ($jenis === 'keduanya' && !$semuaKedua) {
                $j = $jenisPerSoal[$i] ?? $s['jenis'] ?? 'pre_test';
                if (!in_array($j, ['pre_test', 'post_test'], true)) $j = 'pre_test';
            }
            $s['kategori'] = $kat;
            $s['jenis']    = $j;

            $benarIdx = isset($benarPerSoal[$i]) ? (int)$benarPerSoal[$i] : -1;
            if ($benarIdx < 0 || $benarIdx >= count($s['opsi'])) {
                $kurangKunci[] = $i + 1;
                $benarIdx = -1;
            }
            $soalFinal[]  = $s;
            $jenisFinal[] = $j;
            $benarFinal[] = $benarIdx;
        }

        // Validasi komposisi: minimal 3 soal per kategori (total minimal 15).
        // Mode "keduanya": komposisi dihitung per paket (Pre & Post).
        $masalah = [];
        if ($jenis === 'keduanya' && $semuaKedua) {
            // Kedua paket identik — komposisi cukup divalidasi sekali.
            $m = soal_validasi_komposisi(soal_rekap_kategori($soalFinal));
            if (!empty($m)) $masalah[] = 'berlaku untuk kedua paket (identik): ' . implode(', ', $m);
        } elseif ($jenis === 'keduanya') {
            foreach (['pre_test' => 'Paket Pre-Test', 'post_test' => 'Paket Post-Test'] as $j => $label) {
                $sub = [];
                foreach ($soalFinal as $k => $s) {
                    if ($jenisFinal[$k] === $j) $sub[] = $s;
                }
                if (empty($sub)) {
                    $masalah[] = $label . ' kosong — pindahkan sebagian soal ke paket ini.';
                } else {
                    $m = soal_validasi_komposisi(soal_rekap_kategori($sub));
                    if (!empty($m)) $masalah[] = $label . ': ' . implode(', ', $m);
                }
            }
        } else {
            $masalah = soal_validasi_komposisi(soal_rekap_kategori($soalFinal));
        }

        if (empty($judul)) {
            $swal_type = 'error';
            $swal_msg  = 'Judul paket tidak boleh kosong.';
        } elseif (!empty($kurangKunci)) {
            $swal_type = 'error';
            $swal_msg  = 'Tandai jawaban benar untuk soal nomor: ' . implode(', ', $kurangKunci) . '. Setiap soal wajib punya satu jawaban benar agar paket bisa dinilai.';
        } elseif (!empty($masalah)) {
            $swal_type = 'error';
            $swal_msg  = 'Komposisi soal belum sesuai ketentuan (minimal 3 soal per kategori = total minimal 15): ' . implode(' ', $masalah) . ' Centang/hapus centang soal di preview lalu simpan ulang.';
        } else {
            try {
                $pdo->beginTransaction();

                $deskripsi = 'Diimport otomatis dari ' . ($data['sumber'] === 'link' ? 'link soal' : 'teks') . ' — ' . date('d/m/Y H:i');
                $ringkasan = [];

                // Mode keduanya -> dua paket; mode tunggal -> satu paket
                $paket = ($jenis === 'keduanya') ? ['pre_test', 'post_test'] : [$jenis];
                foreach ($paket as $j) {
                    if ($jenis === 'keduanya' && $semuaKedua) {
                        $sub = array_keys($soalFinal); // semua soal masuk ke KEDUA paket
                    } else {
                        $sub = [];
                        foreach ($soalFinal as $k => $s) {
                            if ($jenisFinal[$k] === $j) $sub[] = $k;
                        }
                    }
                    if (empty($sub)) continue;

                    $judulPaket = ($jenis === 'keduanya') ? $judul . ' — ' . ($j === 'pre_test' ? 'Pre-Test' : 'Post-Test') : $judul;
                    $pdo->prepare("INSERT INTO bank_soal (judul, deskripsi, jenis, tanggal, status, created_by) VALUES (?, ?, ?, ?, 'nonaktif', ?)")
                        ->execute([$judulPaket, $deskripsi, $j, $tanggal, $_SESSION['user_id']]);
                    $bankId = $pdo->lastInsertId();

                    $urutan = 0;
                    foreach ($sub as $k) {
                        $s = $soalFinal[$k];
                        $urutan++;
                        $pdo->prepare("INSERT INTO pertanyaan (bank_soal_id, teks_pertanyaan, kategori, urutan) VALUES (?, ?, ?, ?)")
                            ->execute([$bankId, htmlspecialchars($s['teks']), $s['kategori'], $urutan]);
                        $pId = $pdo->lastInsertId();

                        foreach ($s['opsi'] as $oi => $o) {
                            $pdo->prepare("INSERT INTO opsi_jawaban (pertanyaan_id, teks_opsi, adalah_benar) VALUES (?, ?, ?)")
                                ->execute([$pId, htmlspecialchars($o['teks']), ($oi === $benarFinal[$k]) ? 1 : 0]);
                        }
                    }
                    $ringkasan[] = '"' . $judulPaket . '" (' . count($sub) . ' soal)';
                }

                $pdo->commit();
                unset($_SESSION['import_soal']);
                if (count($ringkasan) === 2) {
                    $_SESSION['flash_message'] = 'Import berhasil! 2 paket dibuat: ' . $ringkasan[0] . ' dan ' . $ringkasan[1] . '. Jangan lupa aktifkan keduanya di daftar soal.';
                } else {
                    $_SESSION['flash_message'] = 'Import berhasil! Paket "' . $judul . '" dibuat dengan ' . count($soalFinal) . ' soal (minimal 3 per kategori). Jangan lupa aktifkan di daftar soal.';
                }
                $_SESSION['flash_type'] = 'success';
                header("Location: daftar-soal");
                exit;
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $swal_type = 'error';
                $swal_msg  = 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
    }
}

// Batalkan preview bila diminta
if (isset($_GET['batal'])) {
    unset($_SESSION['import_soal']);
    header("Location: import-soal");
    exit;
}

$rawImport = $_SESSION['import_soal'] ?? null;
$preview   = null;
if ($rawImport !== null) {
    $preview = import_soal_normalisasi($rawImport);
    if ($preview === null) {
        unset($_SESSION['import_soal']);
        if ($swal_type === '') {
            $swal_type = 'error';
            $swal_msg  = 'Sesi import kedaluwarsa atau rusak. Ulangi proses import dari awal.';
        }
    } else {
        $_SESSION['import_soal'] = $preview;
    }
}
$rekap   = $preview ? soal_rekap_kategori($preview['soal']) : null;
$modeKeduanya = ($preview['jenis'] ?? '') === 'keduanya';
// Bila dokumen tidak punya penanda PRE/POST, default = semua soal ke KEDUA paket.
$adaPenanda = false;
if ($preview) {
    foreach ($preview['soal'] as $s) {
        if (!empty($s['jenis'])) { $adaPenanda = true; break; }
    }
}
$modeSemuaKedua = $modeKeduanya && !$adaPenanda;
$rekapPre  = $preview ? array_fill_keys(SOAL_KATEGORI_LIST, 0) : null;
$rekapPost = $preview ? array_fill_keys(SOAL_KATEGORI_LIST, 0) : null;
if ($modeKeduanya && $preview) {
    if ($modeSemuaKedua) {
        $rekapPre  = $rekap;
        $rekapPost = $rekap;
    } else {
        foreach ($preview['soal'] as $s) {
            $jj = ($s['jenis'] ?? 'pre_test') === 'post_test' ? 'post_test' : 'pre_test';
            if ($jj === 'post_test') {
                if (isset($rekapPost[$s['kategori']])) $rekapPost[$s['kategori']]++;
            } else {
                if (isset($rekapPre[$s['kategori']])) $rekapPre[$s['kategori']]++;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Soal Otomatis | BBPOM GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">

    <?php include 'views/includes/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-4xl mx-auto">

            <header class="mb-8">
                <a href="daftar-soal" class="inline-flex items-center text-red-800 font-black text-xs uppercase tracking-widest hover:translate-x-1 transition-transform">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Soal
                </a>
            </header>

            <?php if ($swal_type): ?>
            <div class="rounded-2xl border px-5 py-4 font-bold text-sm mb-8 flex items-start gap-3 <?= $swal_type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-orange-50 border-orange-200 text-orange-700' ?>">
                <i class="fas fa-<?= $swal_type === 'error' ? 'exclamation-circle' : 'info-circle' ?> mt-0.5"></i>
                <span><?= htmlspecialchars($swal_msg) ?></span>
            </div>
            <?php endif; ?>

            <?php if (!$preview): ?>
            <!-- ============ LANGKAH 1: MASUKKAN LINK / TEKS ============ -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden" data-fx-reveal>
                <div class="bg-gradient-to-r from-red-800 to-red-700 p-8 text-white relative overflow-hidden">
                    <span class="fx-logo-halo absolute inset-0 bg-orange-400 rounded-full blur-2xl opacity-10"></span>
                    <h2 class="text-2xl font-black tracking-tight relative">Import Soal Otomatis</h2>
                    <p class="text-orange-200 text-xs font-medium mt-1 relative">Tempel link bank soal — sistem akan membuatkan paket 15 soal (3 per kategori). Pilih "Keduanya" bila dokumen memuat soal pre-test &amp; post-test sekaligus.</p>
                </div>

                <form action="" method="POST" class="p-8 space-y-6" id="formParse">
                    <input type="hidden" name="mode" value="parse">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Judul Paket Soal</label>
                            <input type="text" name="judul" placeholder="Contoh: Bank Soal GAS-PAMAN 2026"
                                   class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-semibold text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Jenis Test</label>
                            <select name="jenis" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-bold text-sm">
                                <option value="pre_test">Pre-Test</option>
                                <option value="post_test">Post-Test</option>
                                <option value="keduanya">Keduanya (Pre-Test &amp; Post-Test)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Link Bank Soal</label>
                        <div class="relative">
                            <i class="fas fa-link absolute left-5 top-1/2 -translate-y-1/2 text-gray-300"></i>
                            <input type="url" name="url" placeholder="https://... (Google Docs / Drive / halaman web publik)"
                                   class="w-full pl-12 pr-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-semibold text-sm">
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium mt-2 ml-1">
                            <i class="fas fa-info-circle mr-1 text-orange-500"></i>
                            Google Docs / Drive harus dibagikan sebagai <b>"Siapa saja yang memiliki link"</b> agar bisa dibaca otomatis.
                        </p>
                        <label class="inline-flex items-center gap-2 text-[10px] font-bold text-gray-500 mt-2 ml-1 cursor-pointer select-none">
                            <input type="checkbox" name="refresh" value="1" class="w-3.5 h-3.5 accent-[#991b1b]">
                            Muat ulang tanpa cache (abaikan cache 10 menit)
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <hr class="flex-1 border-gray-100">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">atau</span>
                        <hr class="flex-1 border-gray-100">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Tempel Teks Soal Langsung</label>
                        <textarea name="teks" rows="8" placeholder="BAGIAN 1: SOAL UMUM&#10;1. Pertanyaan...&#10;a. Opsi A&#10;b. Opsi B&#10;c. Opsi C&#10;d. Opsi D&#10;Jawaban: b"
                                  class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-semibold text-sm resize-y"></textarea>
                    </div>

                    <button type="submit" id="btnParse" class="w-full bg-red-800 hover:bg-black text-white font-black py-5 rounded-[28px] shadow-xl transition-all transform active:scale-[0.98] hover:-translate-y-1 text-sm uppercase tracking-widest">
                        <i class="fas fa-robot mr-2"></i> Baca &amp; Susun Otomatis
                    </button>
                </form>
            </div>

            <div class="bg-orange-50 border border-orange-100 rounded-[28px] p-6 mt-8" data-fx-reveal>
                <h3 class="fx-section-title text-xs font-black text-gray-800 uppercase tracking-[0.2em] mb-4">
                    <span class="fx-icon-chip text-xs"><i class="fas fa-lightbulb"></i></span>Ketentuan Komposisi Soal
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <?php
                    $warna = [
                        'umum'            => 'bg-red-50 border-red-100 text-red-800',
                        'komoditi_pangan' => 'bg-orange-50 border-orange-100 text-orange-700',
                        'kosmetik'        => 'bg-red-50 border-red-100 text-red-800',
                        'obat_bahan_alam' => 'bg-orange-50 border-orange-100 text-orange-700',
                        'obat'            => 'bg-red-50 border-red-100 text-red-800',
                    ];
                    foreach (SOAL_KATEGORI_LIST as $kat): ?>
                    <div class="rounded-2xl border <?= $warna[$kat] ?> p-3 text-center">
                        <p class="text-[9px] font-black uppercase tracking-widest opacity-70"><?= SOAL_KATEGORI_LABEL[$kat] ?></p>
                        <p class="text-xl font-black mt-1">Min 3</p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-[11px] font-bold text-orange-700 mt-4">Total <span class="font-black">minimal 15 soal</span> — pre-test &amp; post-test agen akan mengacak 3 soal dari tiap kategori.</p>
            </div>
            <?php else: ?>
            <!-- ============ LANGKAH 2: PREVIEW & SIMPAN ============ -->
            <form action="" method="POST" class="space-y-6" id="formSave">
                <input type="hidden" name="mode" value="save">

                <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-800 to-red-700 p-8 text-white">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-black tracking-tight">Preview Hasil Import</h2>
                                <p class="text-orange-200 text-xs font-medium mt-1"><?= count($preview['soal']) ?> soal terdeteksi — periksa &amp; sesuaikan sebelum disimpan</p>
                            </div>
                            <a href="import-soal?batal=1" class="text-[10px] font-black uppercase tracking-widest bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 rounded-xl transition-all">
                                <i class="fas fa-undo mr-1"></i> Batal / Mulai Ulang
                            </a>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Judul Paket Soal</label>
                                <input type="text" name="judul" required value="<?= htmlspecialchars($preview['judul']) ?>"
                                       class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-semibold text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Jenis Test</label>
                                <?php if ($modeKeduanya): ?>
                                <input type="hidden" name="jenis" value="keduanya">
                                <span class="inline-flex items-center gap-2 w-full px-5 py-4 rounded-2xl bg-orange-50 border border-orange-100 text-orange-700 font-black text-sm">
                                    <i class="fas fa-layer-group"></i> Keduanya (Pre-Test &amp; Post-Test)
                                </span>
                                <?php else: ?>
                                <select name="jenis" class="w-full px-5 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:border-orange-600 outline-none font-bold text-sm">
                                    <option value="pre_test" <?= $preview['jenis'] === 'pre_test' ? 'selected' : '' ?>>Pre-Test</option>
                                    <option value="post_test" <?= $preview['jenis'] === 'post_test' ? 'selected' : '' ?>>Post-Test</option>
                                </select>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                        $warna = [
                            'umum'            => ['ok' => 'bg-red-800', 'nok' => 'bg-red-100 text-red-800'],
                            'komoditi_pangan' => ['ok' => 'bg-orange-600', 'nok' => 'bg-orange-100 text-orange-700'],
                            'kosmetik'        => ['ok' => 'bg-red-800', 'nok' => 'bg-red-100 text-red-800'],
                            'obat_bahan_alam' => ['ok' => 'bg-orange-600', 'nok' => 'bg-orange-100 text-orange-700'],
                            'obat'            => ['ok' => 'bg-red-800', 'nok' => 'bg-red-100 text-red-800'],
                        ];
                        ?>
                        <!-- Rekap per kategori -->
                        <?php if ($modeKeduanya): ?>
                            <?php foreach ([['key' => 'pre_test', 'label' => 'Paket Pre-Test', 'rekap' => $rekapPre], ['key' => 'post_test', 'label' => 'Paket Post-Test', 'rekap' => $rekapPost]] as $rg): ?>
                            <div class="mb-5">
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2"><i class="fas fa-layer-group mr-1"></i><?= $rg['label'] ?></p>
                                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                    <?php foreach (SOAL_KATEGORI_LIST as $kat): ?>
                                    <div class="rounded-2xl p-3 text-center transition-colors <?= $rg['rekap'][$kat] >= 3 ? $warna[$kat]['ok'] . ' text-white' : $warna[$kat]['nok'] ?>">
                                        <p class="text-[9px] font-black uppercase tracking-widest opacity-80"><?= SOAL_KATEGORI_LABEL[$kat] ?></p>
                                        <p class="text-xl font-black mt-1"><span id="jml-<?= $rg['key'] ?>-<?= $kat ?>"><?= $rg['rekap'][$kat] ?></span>/min 3</p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3" id="rekapKategori">
                            <?php foreach (SOAL_KATEGORI_LIST as $kat): ?>
                            <div class="rounded-2xl p-3 text-center transition-colors <?= $rekap[$kat] >= 3 ? $warna[$kat]['ok'] . ' text-white' : $warna[$kat]['nok'] ?>">
                                <p class="text-[9px] font-black uppercase tracking-widest opacity-80"><?= SOAL_KATEGORI_LABEL[$kat] ?></p>
                                <p class="text-xl font-black mt-1"><span class="jml-<?= $kat ?>"><?= $rekap[$kat] ?></span>/min 3</p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($modeKeduanya): ?>
                        <label class="inline-flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-2xl px-5 py-4 cursor-pointer select-none">
                            <input type="hidden" name="semua_kedua" value="0">
                            <input type="checkbox" name="semua_kedua" value="1" id="semuaKedua" <?= $modeSemuaKedua ? 'checked' : '' ?> class="w-4 h-4 accent-[#991b1b]">
                            <span class="text-xs font-black text-gray-700">Masukkan semua soal ke kedua paket (Pre &amp; Post identik)</span>
                        </label>
                        <?php endif; ?>

                        <div class="text-[11px] font-bold text-orange-700 bg-orange-50 border border-orange-100 rounded-2xl px-4 py-3">
                            <i class="fas fa-info-circle mr-1"></i> <?= $modeKeduanya ? 'Simpan hanya bisa dilakukan bila komposisi <b>minimal 3 soal per kategori (total minimal 15)</b> terpenuhi di <b>KEDUA paket</b> (Pre-Test &amp; Post-Test). Bila opsi &quot;Masukkan semua soal ke kedua paket&quot; dicentang, kedua paket berisi soal yang sama.' : 'Simpan hanya bisa dilakukan bila komposisi <b>minimal 3 soal per kategori (total minimal 15)</b>. Tambah centang soal yang kurang, atau pindahkan kategori soal yang salah.' ?> Sistem menandai jawaban benar otomatis bila kunci terbaca — periksa &amp; centang bila belum.
                        </div>

                        <!-- Daftar soal -->
                        <div class="space-y-4">
                            <?php foreach ($preview['soal'] as $i => $s): ?>
                            <div class="soal-import bg-gray-50 rounded-3xl border border-gray-100 p-5 transition-colors" data-idx="<?= $i ?>" data-kat="<?= $s['kategori'] ?>">
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" name="ikutkan[<?= $i ?>]" value="1" checked class="chk-soal w-4 h-4 accent-[#991b1b]">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-red-800">Soal <?= $i + 1 ?></span>
                                    </label>
                                    <select name="kategori[<?= $i ?>]" class="sel-kat text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl border border-gray-200 bg-white focus:border-orange-500 outline-none">
                                        <?php foreach (SOAL_KATEGORI_LIST as $kat): ?>
                                        <option value="<?= $kat ?>" <?= $s['kategori'] === $kat ? 'selected' : '' ?>><?= SOAL_KATEGORI_LABEL[$kat] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($modeKeduanya): ?>
                                    <select name="jenis_soal[<?= $i ?>]" <?= $modeSemuaKedua ? 'disabled' : '' ?> class="sel-jenis text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl border border-orange-200 bg-orange-50 text-orange-700 focus:border-orange-500 outline-none">
                                        <option value="pre_test" <?= ($s['jenis'] ?? 'pre_test') === 'pre_test' ? 'selected' : '' ?>>Pre-Test</option>
                                        <option value="post_test" <?= ($s['jenis'] ?? '') === 'post_test' ? 'selected' : '' ?>>Post-Test</option>
                                    </select>
                                    <?php endif; ?>
                                    <?php if ($s['jawaban'] !== null): ?>
                                    <span id="kunci-badge-<?= $i ?>" class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                                        <i class="fas fa-check-circle mr-1"></i>Kunci: <?= strtoupper(chr(97 + $s['jawaban'])) ?>
                                    </span>
                                    <?php else: ?>
                                    <span id="kunci-badge-<?= $i ?>" class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-100">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Kunci tak terdeteksi
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <p class="font-bold text-gray-900 text-sm leading-relaxed mb-3"><?= htmlspecialchars($s['teks']) ?></p>
                                <div class="space-y-1.5">
                                    <?php foreach ($s['opsi'] as $oi => $o): ?>
                                    <label class="flex items-start gap-2 text-sm cursor-pointer <?= $s['jawaban'] === $oi ? 'text-green-700 font-bold' : 'text-gray-600 font-medium' ?>">
                                        <input type="radio" name="benar[<?= $i ?>]" value="<?= $oi ?>" <?= $s['jawaban'] === $oi ? 'checked' : '' ?> class="mt-1.5 w-4 h-4 accent-[#991b1b] shrink-0">
                                        <span class="w-5 h-5 shrink-0 rounded-md <?= $s['jawaban'] === $oi ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-400' ?> flex items-center justify-center text-[10px] font-black mt-0.5"><?= strtoupper(chr(97 + $oi)) ?></span>
                                        <span><?= htmlspecialchars($o['teks']) ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" id="btnSimpan" class="w-full bg-red-800 hover:bg-black text-white font-black py-5 rounded-[28px] shadow-xl transition-all transform active:scale-[0.98] hover:-translate-y-1 text-sm uppercase tracking-widest">
                            <i class="fas fa-save mr-2"></i> Simpan ke Bank Soal
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </main>

    <script>
    // Indikator loading saat mengambil soal dari link (unduhan Google bisa butuh beberapa detik)
    document.getElementById('formParse')?.addEventListener('submit', function () {
        var btn = document.getElementById('btnParse');
        if (!btn) return;
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-wait');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengambil soal dari Google Drive&hellip; (± beberapa detik)';
    });
    <?php if ($preview): ?>
    var MODE_KEDUANYA = <?= $modeKeduanya ? 'true' : 'false' ?>;
    var MODE_SEMUA = <?= $modeSemuaKedua ? 'true' : 'false' ?>;
    // Hitung ulang rekap kategori saat checkbox / select berubah
    function hitungRekap() {
        var rekap = { umum: 0, komoditi_pangan: 0, kosmetik: 0, obat_bahan_alam: 0, obat: 0 };
        var rekapGrup = {
            pre_test:  { umum: 0, komoditi_pangan: 0, kosmetik: 0, obat_bahan_alam: 0, obat: 0 },
            post_test: { umum: 0, komoditi_pangan: 0, kosmetik: 0, obat_bahan_alam: 0, obat: 0 }
        };
        document.querySelectorAll('.soal-import').forEach(function (el) {
            var chk = el.querySelector('.chk-soal');
            if (!chk.checked) return;
            var kat = el.querySelector('.sel-kat').value;
            if (MODE_KEDUANYA) {
                if (MODE_SEMUA) {
                    if (rekapGrup.pre_test[kat] !== undefined) rekapGrup.pre_test[kat]++;
                    if (rekapGrup.post_test[kat] !== undefined) rekapGrup.post_test[kat]++;
                } else {
                    var jns = el.querySelector('.sel-jenis') ? el.querySelector('.sel-jenis').value : 'pre_test';
                    if (rekapGrup[jns] && rekapGrup[jns][kat] !== undefined) rekapGrup[jns][kat]++;
                }
            } else if (rekap[kat] !== undefined) {
                rekap[kat]++;
            }
        });
        if (!MODE_KEDUANYA) {
            for (var k in rekap) {
                var span = document.querySelector('.jml-' + k);
                if (span) span.textContent = rekap[k];
            }
            return;
        }
        ['pre_test', 'post_test'].forEach(function (g) {
            for (var k2 in rekapGrup[g]) {
                var el2 = document.getElementById('jml-' + g + '-' + k2);
                if (el2) el2.textContent = rekapGrup[g][k2];
            }
        });
    }
    document.querySelectorAll('.chk-soal, .sel-kat, .sel-jenis').forEach(function (el) {
        el.addEventListener('change', hitungRekap);
    });
    // Toggle "semua soal ke kedua paket" (mode keduanya)
    var cbSemua = document.getElementById('semuaKedua');
    if (cbSemua) {
        cbSemua.addEventListener('change', function () {
            MODE_SEMUA = this.checked;
            document.querySelectorAll('.sel-jenis').forEach(function (el) { el.disabled = MODE_SEMUA; });
            hitungRekap();
        });
    }
    // Badge kunci mengikuti pilihan radio
    document.querySelectorAll('input[type="radio"][name^="benar"]').forEach(function (el) {
        el.addEventListener('change', function () {
            var idx = this.name.match(/\[(\d+)\]/)[1];
            var badge = document.getElementById('kunci-badge-' + idx);
            if (!badge) return;
            var huruf = String.fromCharCode(97 + parseInt(this.value, 10)).toUpperCase();
            badge.className = 'text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-100';
            badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Kunci: ' + huruf;
        });
    });
    // Validasi: setiap soal yang diikutkan wajib punya jawaban benar
    document.getElementById('formSave').addEventListener('submit', function (e) {
        var kosong = [];
        document.querySelectorAll('.soal-import').forEach(function (el) {
            var chk = el.querySelector('.chk-soal');
            if (!chk.checked) return;
            var picked = el.querySelector('input[name="benar[' + el.dataset.idx + ']"]:checked');
            if (!picked) { kosong.push(parseInt(el.dataset.idx, 10) + 1); el.style.outline = '2px solid #dc2626'; }
            else el.style.outline = '';
        });
        if (kosong.length) {
            e.preventDefault();
            alert('Tandai jawaban benar untuk soal nomor: ' + kosong.join(', '));
        }
    });
    <?php endif; ?>
    </script>
    <script src="assets/js/futuristik.js?v=2"></script>
</body>
</html>
