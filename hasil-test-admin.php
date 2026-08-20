<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();
if (!in_array($_SESSION['role'], ['admin', 'staff', 'kabalai'])) {
    header("Location: dashboard");
    exit;
}
$bolehEditNilai = in_array($_SESSION['role'], ['admin', 'staff']);
$bolehHapus = in_array($_SESSION['role'], ['admin', 'staff']); // sama seperti edit nilai

$filterJenis = $_GET['jenis'] ?? '';
$filterAgen  = $_GET['agen_id'] ?? '';

$flashMsgHapus = '';
$flashTypeHapus = '';

// === Hapus hasil test (satu / multi / semua sesuai filter) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_hapus_hasil']) && $bolehHapus) {
    try {
        $mode = $_POST['aksi_hapus_hasil'];

        $hapusByIds = function(array $ids) use ($pdo) {
            $ids = array_values(array_filter(array_map('intval', $ids), fn($x) => $x > 0));
            if (!$ids) return 0;
            $ph = implode(',', array_fill(0, count($ids), '?'));
            // Hapus detail jawaban & set soal dulu
            $pdo->prepare("DELETE FROM detail_jawaban WHERE hasil_test_id IN ($ph)")->execute($ids);
            if (is_file(__DIR__ . '/core/soal_parser.php')) {
                require_once __DIR__ . '/core/soal_parser.php';
                hasil_test_soal_ensure_table($pdo);
                $pdo->prepare("DELETE FROM hasil_test_soal WHERE hasil_test_id IN ($ph)")->execute($ids);
            }
            $st = $pdo->prepare("DELETE FROM hasil_test WHERE id IN ($ph)");
            $st->execute($ids);
            return $st->rowCount();
        };

        if ($mode === 'satu' && !empty($_POST['hasil_id'])) {
            $n = $hapusByIds([(int)$_POST['hasil_id']]);
            $flashMsgHapus = $n ? 'Hasil test berhasil dihapus.' : 'Data tidak ditemukan.';
            $flashTypeHapus = $n ? 'ok' : 'warn';
        } elseif ($mode === 'pilih' && !empty($_POST['hasil_ids']) && is_array($_POST['hasil_ids'])) {
            $n = $hapusByIds($_POST['hasil_ids']);
            $flashMsgHapus = $n ? ($n . ' hasil test berhasil dihapus.') : 'Tidak ada data yang dipilih.';
            $flashTypeHapus = $n ? 'ok' : 'warn';
        } elseif ($mode === 'semua') {
            // Hapus semua yang sesuai filter saat ini
            $fj = $_POST['filter_jenis'] ?? '';
            $fa = $_POST['filter_agen'] ?? '';
            $delWhere = ["bs.jenis IS NOT NULL"];
            $delParams = [];
            if (in_array($fj, ['pre_test', 'post_test'], true)) {
                $delWhere[] = "bs.jenis = ?";
                $delParams[] = $fj;
            }
            if ($fa !== '') {
                $delWhere[] = "ht.user_id = ?";
                $delParams[] = (int)$fa;
            }
            $delWhereStr = implode(' AND ', $delWhere);
            $stIds = $pdo->prepare("
                SELECT ht.id FROM hasil_test ht
                JOIN bank_soal bs ON bs.id = ht.bank_soal_id
                WHERE $delWhereStr
            ");
            $stIds->execute($delParams);
            $ids = $stIds->fetchAll(PDO::FETCH_COLUMN);
            $n = $hapusByIds($ids);
            $flashMsgHapus = $n . ' hasil test berhasil dihapus.';
            $flashTypeHapus = 'ok';
        }
    } catch (Throwable $e) {
        $flashMsgHapus = 'Gagal menghapus: ' . $e->getMessage();
        $flashTypeHapus = 'err';
    }
}

// === Input nilai Pre + Post sekaligus (agen yang tes di luar aplikasi) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_input_ganda']) && $bolehEditNilai) {
    try {
        $userId    = (int)($_POST['user_id'] ?? 0);
        $bankPre   = (int)($_POST['bank_pre_id'] ?? 0);
        $bankPost  = (int)($_POST['bank_post_id'] ?? 0);
        $nilaiPre  = filter_var($_POST['nilai_pre'] ?? null, FILTER_VALIDATE_FLOAT);
        $nilaiPost = filter_var($_POST['nilai_post'] ?? null, FILTER_VALIDATE_FLOAT);
        $benarPre  = max(0, (int)($_POST['benar_pre'] ?? 0));
        $benarPost = max(0, (int)($_POST['benar_post'] ?? 0));
        $total     = max(1, (int)($_POST['total_pertanyaan'] ?? 15));
        $tanggal   = $_POST['tanggal'] ?? date('Y-m-d');
        $tanggal   = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ? $tanggal : date('Y-m-d');
        $catatan   = htmlspecialchars(trim($_POST['catatan_manual'] ?? ''));

        if (!$userId || !$bankPre || !$bankPost) throw new Exception('Agen dan kedua paket soal wajib dipilih.');
        if ($nilaiPre === false || $nilaiPre < 0 || $nilaiPre > 100) throw new Exception('Nilai Pre-Test harus 0–100.');
        if ($nilaiPost === false || $nilaiPost < 0 || $nilaiPost > 100) throw new Exception('Nilai Post-Test harus 0–100.');

        // Pastikan jenis paket sesuai pre/post
        $stmtJenis = $pdo->prepare("SELECT id, jenis FROM bank_soal WHERE id IN (?, ?)");
        $stmtJenis->execute([$bankPre, $bankPost]);
        $jenisById = [];
        foreach ($stmtJenis->fetchAll() as $j) $jenisById[$j['id']] = $j['jenis'];
        if (($jenisById[$bankPre] ?? '') !== 'pre_test') throw new Exception('Paket Pre-Test tidak valid.');
        if (($jenisById[$bankPost] ?? '') !== 'post_test') throw new Exception('Paket Post-Test tidak valid.');

        $waktu = $tanggal . ' ' . date('H:i:s');

        $pdo->beginTransaction();
        foreach ([
            ['bank' => $bankPre, 'nilai' => $nilaiPre, 'benar' => $benarPre, 'jenis' => 'pre_test'],
            ['bank' => $bankPost, 'nilai' => $nilaiPost, 'benar' => $benarPost, 'jenis' => 'post_test'],
        ] as $t) {
            $stmtAda = $pdo->prepare("SELECT id FROM hasil_test WHERE user_id = ? AND bank_soal_id = ?");
            $stmtAda->execute([$userId, $t['bank']]);
            $ada = $stmtAda->fetch();

            if ($ada) {
                // Sudah ada: update (pertahankan status 'disetujui' bila sudah ditandatangani)
                $sql = "UPDATE hasil_test SET
                    nilai = ?, jawaban_benar = ?, total_pertanyaan = ?,
                    waktu_mulai = ?, waktu_selesai = ?, is_manual = 1, catatan_manual = ?,
                    status_sertifikat = CASE
                        WHEN ? = 'post_test' AND ? >= 70 AND status_sertifikat = 'disetujui' THEN status_sertifikat
                        WHEN ? = 'post_test' AND ? >= 70 THEN 'menunggu_ttd'
                        ELSE 'belum'
                    END
                    WHERE id = ?";
                $pdo->prepare($sql)->execute([
                    $t['nilai'], $t['benar'], $total,
                    $waktu, $waktu, $catatan,
                    $t['jenis'], $t['nilai'], $t['jenis'], $t['nilai'], $ada['id']
                ]);
            } else {
                $statusSert = ($t['jenis'] === 'post_test' && $t['nilai'] >= 70) ? 'menunggu_ttd' : 'belum';
                $pdo->prepare("INSERT INTO hasil_test
                    (user_id, bank_soal_id, nilai, jawaban_benar, total_pertanyaan, waktu_mulai, waktu_selesai, status_sertifikat, is_manual, catatan_manual)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)")
                    ->execute([$userId, $t['bank'], $t['nilai'], $t['benar'], $total, $waktu, $waktu, $statusSert, $catatan]);
            }
        }
        $pdo->commit();
        $_SESSION['flash_message'] = 'Nilai Pre-Test & Post-Test berhasil disimpan/diperbarui.';
        $_SESSION['flash_type'] = 'success';
        header("Location: hasil-test-admin");
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['flash_message'] = 'Gagal menyimpan nilai: ' . $e->getMessage();
        $_SESSION['flash_type'] = 'error';
        header("Location: hasil-test-admin");
        exit;
    }
}

$params = [];
$where  = ["bs.jenis IS NOT NULL"];

if (in_array($filterJenis, ['pre_test', 'post_test'])) {
    $where[] = "bs.jenis = ?";
    $params[] = $filterJenis;
}
if ($filterAgen) {
    $where[] = "ht.user_id = ?";
    $params[] = (int)$filterAgen;
}

$whereStr = implode(' AND ', $where);

$stmtHasil = $pdo->prepare("
    SELECT ht.*, bs.judul, bs.jenis, bs.tanggal as tanggal_test, u.nama as nama_agen, u.agen_id as kode_agen
    FROM hasil_test ht
    JOIN bank_soal bs ON bs.id = ht.bank_soal_id
    JOIN users u ON u.id = ht.user_id
    WHERE $whereStr
    ORDER BY bs.tanggal DESC, ht.created_at DESC
");
$stmtHasil->execute($params);
$hasilList = $stmtHasil->fetchAll();

$daftarAgen = $pdo->query("SELECT id, nama, agen_id FROM users WHERE role = 'agen' ORDER BY nama")->fetchAll();

// Data dropdown untuk input nilai pre + post (agen aktif & paket aktif)
$daftarAgenAktif = $pdo->query("SELECT id, nama, agen_id FROM users WHERE role = 'agen' AND status = 'aktif' ORDER BY nama")->fetchAll();
$daftarBankPre   = $pdo->query("SELECT id, judul, tanggal FROM bank_soal WHERE jenis = 'pre_test' AND status = 'aktif' ORDER BY tanggal DESC, id DESC")->fetchAll();
$daftarBankPost  = $pdo->query("SELECT id, judul, tanggal FROM bank_soal WHERE jenis = 'post_test' AND status = 'aktif' ORDER BY tanggal DESC, id DESC")->fetchAll();

$statPreTest  = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id WHERE bs.jenis = 'pre_test'")->fetchColumn() ?: 0;
$statPostTest = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id WHERE bs.jenis = 'post_test'")->fetchColumn() ?: 0;
$rataPreTest  = $pdo->query("SELECT AVG(ht.nilai) FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id WHERE bs.jenis = 'pre_test'")->fetchColumn();
$rataPostTest = $pdo->query("SELECT AVG(ht.nilai) FROM hasil_test ht JOIN bank_soal bs ON bs.id = ht.bank_soal_id WHERE bs.jenis = 'post_test'")->fetchColumn();

$flashMessage = $_SESSION['flash_message'] ?? '';
$flashType    = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);

function nilaiBadge($nilai) {
    if ($nilai >= 80) return ['bg-green-100 text-green-700', 'Sangat Baik'];
    if ($nilai >= 60) return ['bg-orange-100 text-orange-700', 'Cukup'];
    return ['bg-red-100 text-red-700', 'Perlu Belajar'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Test Agen | BBPOM GAS-PAMAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">

    <?php include 'views/includes/sidebar.php'; ?>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">
        <div class="max-w-5xl mx-auto">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10">
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Hasil Test Agen</h1>
                    <p class="text-sm text-gray-400 font-medium mt-1">Rekap nilai Pre-Test &amp; Post-Test seluruh agen</p>
                </div>
                <?php if ($bolehEditNilai): ?>
                <div class="flex flex-wrap gap-3">
                    <button type="button" id="btnToggleInputGanda" class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-black uppercase tracking-widest px-6 py-4 rounded-2xl transition-all shadow-lg">
                        <i class="fas fa-pen-nib"></i> Input Nilai Pre + Post
                    </button>
                    <a href="edit-nilai-test" class="inline-flex items-center gap-2 bg-red-800 hover:bg-black text-white text-xs font-black uppercase tracking-widest px-6 py-4 rounded-2xl transition-all shadow-lg">
                        <i class="fas fa-pen"></i> Input Nilai Manual
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <p class="text-[9px] font-black uppercase tracking-widest text-orange-600 mb-2">Sudah Pre-Test</p>
                    <p class="text-3xl font-black text-gray-900"><?= $statPreTest ?></p>
                    <p class="text-[10px] text-gray-400">agen</p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <p class="text-[9px] font-black uppercase tracking-widest text-red-700 mb-2">Sudah Post-Test</p>
                    <p class="text-3xl font-black text-gray-900"><?= $statPostTest ?></p>
                    <p class="text-[10px] text-gray-400">agen</p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Rata Pre-Test</p>
                    <p class="text-3xl font-black text-gray-900"><?= $rataPreTest ? number_format($rataPreTest, 1) : '—' ?></p>
                    <p class="text-[10px] text-gray-400">dari 100</p>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Rata Post-Test</p>
                    <p class="text-3xl font-black text-gray-900"><?= $rataPostTest ? number_format($rataPostTest, 1) : '—' ?></p>
                    <p class="text-[10px] text-gray-400">dari 100</p>
                </div>
            </div>

            <?php if ($bolehEditNilai): ?>
            <div id="panelInputGanda" class="hidden bg-white rounded-3xl border-2 border-dashed border-orange-200 shadow-sm p-6 md:p-8 mb-6">
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="aksi_input_ganda" value="1">

                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-red-800 text-white flex items-center justify-center shrink-0">
                            <i class="fas fa-pen-nib"></i>
                        </span>
                        <div>
                            <h3 class="font-black text-gray-900 text-sm uppercase tracking-widest">Input Nilai Pre + Post (Hard Copy)</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Untuk agen yang mengerjakan pre &amp; post test di luar aplikasi. Input ulang = perbarui (tidak duplikat).</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Agen</label>
                            <select name="user_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none focus:border-orange-500">
                                <option value="">— Pilih Agen —</option>
                                <?php foreach ($daftarAgenAktif as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['agen_id'] ?: '-') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Paket Pre-Test</label>
                            <select name="bank_pre_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none focus:border-orange-500">
                                <option value="">— Pilih Paket —</option>
                                <?php foreach ($daftarBankPre as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['judul']) ?><?= $s['tanggal'] ? ' · ' . $s['tanggal'] : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Paket Post-Test</label>
                            <select name="bank_post_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none focus:border-orange-500">
                                <option value="">— Pilih Paket —</option>
                                <?php foreach ($daftarBankPost as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['judul']) ?><?= $s['tanggal'] ? ' · ' . $s['tanggal'] : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Nilai Pre (0–100)</label>
                            <input type="number" name="nilai_pre" min="0" max="100" step="0.01" required placeholder="cth: 73.33"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 font-black text-lg outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Nilai Post (0–100)</label>
                            <input type="number" name="nilai_post" min="0" max="100" step="0.01" required placeholder="cth: 86.67"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 font-black text-lg outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Benar Pre</label>
                            <input type="number" name="benar_pre" min="0" value="0" class="w-full px-4 py-3 rounded-xl border border-gray-200 font-semibold text-sm outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Benar Post</label>
                            <input type="number" name="benar_post" min="0" value="0" class="w-full px-4 py-3 rounded-xl border border-gray-200 font-semibold text-sm outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Soal</label>
                            <input type="number" name="total_pertanyaan" min="1" value="15" class="w-full px-4 py-3 rounded-xl border border-gray-200 font-semibold text-sm outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Test</label>
                            <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" class="w-full px-4 py-3 rounded-xl border border-gray-200 font-semibold text-sm outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan (opsional)</label>
                            <input type="text" name="catatan_manual" placeholder="cth: Hard copy 12 Agustus 2026" class="w-full px-4 py-3 rounded-xl border border-gray-200 font-semibold text-sm outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-400 italic">
                        Jika post-test ≥ 70, status sertifikat otomatis menjadi <b>menunggu TTD</b> Kepala Balai (kecuali sudah disetujui).
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center gap-2 bg-red-800 hover:bg-black text-white font-black py-3.5 px-8 rounded-2xl uppercase tracking-widest text-xs transition-all">
                            <i class="fas fa-save mr-1"></i> Simpan Pre + Post
                        </button>
                        <button type="button" id="btnBatalInputGanda" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-3.5 px-8 rounded-2xl uppercase tracking-widest text-xs transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <form method="GET" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-6 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Test</label>
                    <select name="jenis" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none focus:border-orange-500">
                        <option value="">Semua</option>
                        <option value="pre_test" <?= $filterJenis === 'pre_test' ? 'selected' : '' ?>>Pre-Test</option>
                        <option value="post_test" <?= $filterJenis === 'post_test' ? 'selected' : '' ?>>Post-Test</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Agen</label>
                    <select name="agen_id" class="px-4 py-3 rounded-xl border border-gray-200 text-sm font-semibold outline-none focus:border-orange-500">
                        <option value="">Semua Agen</option>
                        <?php foreach ($daftarAgen as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $filterAgen == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nama']) ?> (<?= $a['agen_id'] ?: '-' ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="px-6 py-3 bg-red-800 hover:bg-black text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="hasil-test-admin" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-black uppercase tracking-widest rounded-xl transition-all">Reset</a>
            </form>

            <?php if (!empty($flashMsgHapus)): ?>
            <div class="mb-6 p-4 rounded-2xl font-bold text-center border <?= $flashTypeHapus === 'ok' ? 'bg-orange-100 text-orange-700 border-orange-200' : ($flashTypeHapus === 'warn' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-100 text-red-700 border-red-200') ?>">
                <?= htmlspecialchars($flashMsgHapus) ?>
            </div>
            <?php endif; ?>

            <form id="formHapusHasil" method="POST" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <input type="hidden" name="aksi_hapus_hasil" id="aksiHapusHasil" value="">
                <input type="hidden" name="filter_jenis" value="<?= htmlspecialchars($filterJenis) ?>">
                <input type="hidden" name="filter_agen" value="<?= htmlspecialchars($filterAgen) ?>">

                <?php if ($bolehHapus): ?>
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3 bg-gray-50/50">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="checkAllHasil" class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Pilih Semua</span>
                    </label>
                    <button type="button" onclick="hapusHasilTerpilih()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus Terpilih
                    </button>
                    <button type="button" onclick="hapusHasilSemua()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                        <i class="fas fa-trash mr-1"></i> Hapus Semua (Filter Saat Ini)
                    </button>
                    <span class="text-[10px] text-gray-400 font-semibold ml-auto" id="countSelectedHasil">0 dipilih</span>
                </div>
                <?php endif; ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <?php if ($bolehHapus): ?>
                                <th class="px-4 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-10"></th>
                                <?php endif; ?>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Agen</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jenis</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Paket Soal</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Nilai</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu</th>
                                <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (count($hasilList) > 0): foreach ($hasilList as $h):
                                [$cls, $ket] = nilaiBadge((float)$h['nilai']);
                            ?>
                            <tr class="hover:bg-orange-50/30 transition-all">
                                <?php if ($bolehHapus): ?>
                                <td class="px-4 py-5">
                                    <input type="checkbox" name="hasil_ids[]" value="<?= (int)$h['id'] ?>" class="hasil-check w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" onchange="updateCountSelectedHasil()">
                                </td>
                                <?php endif; ?>
                                <td class="px-6 py-5">
                                    <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($h['nama_agen']) ?></p>
                                    <p class="text-[10px] text-gray-400"><?= htmlspecialchars($h['kode_agen'] ?: '-') ?>
                                        <?php if (!empty($h['is_manual'])): ?>
                                        <span class="ml-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[8px] font-black uppercase rounded">Manual</span>
                                        <?php endif; ?>
                                    </p>
                                </td>
                                <td class="px-6 py-5">
                                    <?php if ($h['jenis'] === 'pre_test'): ?>
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-orange-100 text-orange-700">Pre-Test</span>
                                    <?php else: ?>
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-700">Post-Test</span>
                                    <?php endif; ?>
                                    <?php if (!empty($h['status_sertifikat']) && $h['status_sertifikat'] !== 'belum'): ?>
                                    <p class="text-[9px] text-gray-400 mt-1 font-semibold">
                                        <i class="fas fa-certificate mr-1"></i><?= htmlspecialchars($h['status_sertifikat']) ?>
                                    </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600 font-medium">
                                    <?= htmlspecialchars($h['judul']) ?>
                                    <?php if ($h['tanggal_test']): ?>
                                    <p class="text-[10px] text-orange-500 font-bold mt-0.5"><i class="fas fa-calendar-alt mr-1"></i><?= (new DateTime($h['tanggal_test']))->format('d M Y') ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <p class="text-xl font-black text-gray-900"><?= number_format($h['nilai'], 0) ?></p>
                                    <p class="text-[9px] text-gray-400"><?= $h['jawaban_benar'] ?>/<?= $h['total_pertanyaan'] ?></p>
                                    <span class="text-[8px] font-black px-2 py-0.5 rounded-lg <?= $cls ?>"><?= $ket ?></span>
                                </td>
                                <td class="px-6 py-5 text-xs text-gray-500"><?= date('d M Y, H:i', strtotime($h['waktu_selesai'])) ?></td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        <a href="detail-hasil-test?id=<?= $h['id'] ?>" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-orange-50 text-gray-400 hover:text-orange-600 flex items-center justify-center transition-all" title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <?php if ($bolehEditNilai): ?>
                                        <a href="edit-nilai-test?id=<?= $h['id'] ?>" class="w-9 h-9 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-600 flex items-center justify-center transition-all" title="Edit / isi manual">
                                            <i class="fas fa-pencil text-sm"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($bolehHapus): ?>
                                        <button type="button" onclick="hapusHasilSatu(<?= (int)$h['id'] ?>)" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-all" title="Hapus">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="<?= $bolehHapus ? 7 : 6 ?>" class="px-6 py-16 text-center italic text-gray-400">Belum ada data hasil test. Gunakan tombol <b>Input Nilai Manual</b> untuk mengisi.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            </div>

        </div>
    </main>
    <?php if ($flashMessage): ?>
    <script>
    Swal.fire({ icon: '<?= $flashType ?: 'success' ?>', title: 'Info', text: '<?= addslashes($flashMessage) ?>', confirmButtonColor: '#991b1b', customClass: { popup: 'rounded-[32px]' } });
    </script>
    <?php endif; ?>

<script>
function togglePanelInputGanda(show) {
    const p = document.getElementById('panelInputGanda');
    if (!p) return;
    p.classList.toggle('hidden', !show);
    if (show) p.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
document.getElementById('btnToggleInputGanda')?.addEventListener('click', function() {
    togglePanelInputGanda(document.getElementById('panelInputGanda')?.classList.contains('hidden') ?? true);
});
document.getElementById('btnBatalInputGanda')?.addEventListener('click', function() {
    togglePanelInputGanda(false);
});
document.getElementById('checkAllHasil')?.addEventListener('change', function() {
    document.querySelectorAll('.hasil-check').forEach(cb => { cb.checked = this.checked; });
    updateCountSelectedHasil();
});
function updateCountSelectedHasil() {
    const n = document.querySelectorAll('.hasil-check:checked').length;
    const el = document.getElementById('countSelectedHasil');
    if (el) el.textContent = n + ' dipilih';
}
function hapusHasilSatu(id) {
    Swal.fire({
        title: 'Hapus hasil test ini?',
        text: 'Data nilai dan detail jawaban akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.getElementById('formHapusHasil');
        document.getElementById('aksiHapusHasil').value = 'satu';
        let h = document.createElement('input');
        h.type = 'hidden'; h.name = 'hasil_id'; h.value = id;
        f.appendChild(h);
        f.submit();
    });
}
function hapusHasilTerpilih() {
    const n = document.querySelectorAll('.hasil-check:checked').length;
    if (n === 0) {
        Swal.fire({ icon: 'info', title: 'Belum ada yang dipilih', text: 'Centang hasil test yang ingin dihapus terlebih dahulu.', confirmButtonColor: '#ea580c' });
        return;
    }
    Swal.fire({
        title: 'Hapus ' + n + ' hasil test terpilih?',
        text: 'Data nilai dan detail jawaban akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        document.getElementById('aksiHapusHasil').value = 'pilih';
        document.getElementById('formHapusHasil').submit();
    });
}
function hapusHasilSemua() {
    Swal.fire({
        title: 'Hapus SEMUA hasil test sesuai filter saat ini?',
        text: 'Tindakan ini menghapus semua data yang tampil (atau seluruh hasil test jika tanpa filter). Tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        document.getElementById('aksiHapusHasil').value = 'semua';
        document.getElementById('formHapusHasil').submit();
    });
}
</script>
    <script src="assets/js/futuristik.js?v=2"></script>
</body>
</html>