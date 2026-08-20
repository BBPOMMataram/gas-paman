
<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();

if ($_SESSION['role'] !== 'agen') {
    header("Location: dashboard");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard");
    exit;
}

$userId      = $_SESSION['user_id'];
$bankSoalId  = (int)($_POST['bank_soal_id'] ?? 0);
$waktuMulai  = $_POST['waktu_mulai'] ?? date('Y-m-d H:i:s');
$jawabanPost = $_POST['jawaban'] ?? [];

if (!$bankSoalId) { header("Location: dashboard"); exit; }

// Ambil data bank soal
$stmtBank = $pdo->prepare("SELECT * FROM bank_soal WHERE id = ? AND status = 'aktif'");
$stmtBank->execute([$bankSoalId]);
$bankSoal = $stmtBank->fetch();
if (!$bankSoal) {
    $_SESSION['flash_message'] = 'Paket soal tidak ditemukan atau sudah tidak aktif.';
    $_SESSION['flash_type'] = 'error';
    header("Location: dashboard");
    exit;
}

// Cek apakah sudah pernah mengerjakan
$stmtCek = $pdo->prepare("SELECT id FROM hasil_test WHERE user_id = ? AND bank_soal_id = ?");
$stmtCek->execute([$userId, $bankSoalId]);
if ($stmtCek->fetch()) {
    $_SESSION['flash_message'] = 'Kamu sudah pernah mengerjakan test ini.';
    $_SESSION['flash_type'] = 'info';
    header("Location: hasil-test");
    exit;
}

require_once 'core/soal_parser.php';

// Set 15 soal yang dikerjakan agen disimpan di sesi (dibuat saat halaman test dibuka).
// Tanpa set ini POST tidak bisa dinilai — cegah submit langsung tanpa membuka halaman test.
$kunciSesi = 'set_test_' . $userId . '_' . $bankSoalId;
$setIds = $_SESSION[$kunciSesi]['ids'] ?? null;
if (
    !is_array($setIds)
    || count($setIds) < 15
    || ($_SESSION[$kunciSesi]['bank_id'] ?? 0) !== $bankSoalId
) {
    $_SESSION['flash_message'] = 'Sesi pengerjaan test berakhir. Silakan mulai ulang.';
    $_SESSION['flash_type'] = 'warning';
    header('Location: ' . ($bankSoal['jenis'] === 'post_test' ? 'post-test' : 'pre-test'));
    exit;
}
$setIds = array_map('intval', $setIds);

// Ambil pertanyaan SET saja (bukan semua soal bank) beserta opsi yang benar
$phSet = implode(',', array_fill(0, count($setIds), '?'));
$stmtP = $pdo->prepare("SELECT p.id as pid, o.id as oid, o.adalah_benar
    FROM pertanyaan p
    JOIN opsi_jawaban o ON o.pertanyaan_id = p.id
    WHERE p.bank_soal_id = ? AND p.id IN ($phSet)");
$stmtP->execute(array_merge([$bankSoalId], $setIds));
$rows = $stmtP->fetchAll();

// Buat map: pertanyaan_id → [opsi_id => adalah_benar]
$soalMap = [];
foreach ($rows as $row) {
    $soalMap[$row['pid']][$row['oid']] = (bool)$row['adalah_benar'];
}

$totalPertanyaan = count($soalMap);
$jawabanBenar    = 0;
$detailJawaban   = []; // [pertanyaan_id => opsi_id yang dipilih]

foreach ($soalMap as $pid => $opsiBenarMap) {
    $opsiDipilih = isset($jawabanPost[$pid]) ? (int)$jawabanPost[$pid] : null;
    $detailJawaban[$pid] = $opsiDipilih;

    if ($opsiDipilih && isset($opsiBenarMap[$opsiDipilih]) && $opsiBenarMap[$opsiDipilih]) {
        $jawabanBenar++;
    }
}

$nilai       = $totalPertanyaan > 0 ? round(($jawabanBenar / $totalPertanyaan) * 100, 2) : 0;
$waktuSelesai = date('Y-m-d H:i:s');

// Validasi waktu mulai
if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $waktuMulai)) {
    $waktuMulai = $waktuSelesai;
}

try {
    $pdo->beginTransaction();

    // Kalau ini post-test dan nilainya lulus (>= 70), langsung tandai sertifikatnya
    // "menunggu tanda tangan" biar muncul di halaman Persetujuan Sertifikat kabalai.
    // Sebelumnya kolom ini cuma keisi kalau nilai diinput manual sama admin,
    // jadi hasil test normal agen gak pernah nongol di sana.
    $statusSertifikat = ($bankSoal['jenis'] === 'post_test' && $nilai >= 70) ? 'menunggu_ttd' : 'belum';

    $pdo->prepare("INSERT INTO hasil_test (user_id, bank_soal_id, nilai, jawaban_benar, total_pertanyaan, waktu_mulai, waktu_selesai, status_sertifikat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute([$userId, $bankSoalId, $nilai, $jawabanBenar, $totalPertanyaan, $waktuMulai, $waktuSelesai, $statusSertifikat]);
    $hasilId = $pdo->lastInsertId();

    foreach ($detailJawaban as $pid => $opsiId) {
        $pdo->prepare("INSERT INTO detail_jawaban (hasil_test_id, pertanyaan_id, opsi_id) VALUES (?, ?, ?)")
            ->execute([$hasilId, $pid, $opsiId ?: null]);
    }

    // Simpan set soal yang dikerjakan (urutan tampil) untuk pembahasan
    hasil_test_soal_ensure_table($pdo);
    $stmtSet = $pdo->prepare("INSERT INTO hasil_test_soal (hasil_test_id, pertanyaan_id, urutan) VALUES (?, ?, ?)");
    foreach ($setIds as $urutan => $pid) {
        $stmtSet->execute([$hasilId, $pid, $urutan + 1]);
    }

    $pdo->commit();
    unset($_SESSION[$kunciSesi]);

    try {
        require_once __DIR__ . '/core/notifikasi.php';
        $namaAgen = $_SESSION['nama'] ?? 'Agen';
        $jenisLabel = ($bankSoal['jenis'] ?? '') === 'post_test' ? 'Post-Test' : 'Pre-Test';
        notifikasi_kirim_role($pdo, 'admin', 'test_selesai',
            $namaAgen . ' menyelesaikan ' . $jenisLabel,
            'Nilai: ' . $nilai . ' (' . $jawabanBenar . '/' . $totalPertanyaan . ' benar).',
            'detail-hasil-test?id=' . (int)$hasilId,
            (int)$hasilId
        );
        if (($bankSoal['jenis'] ?? '') === 'post_test' && $nilai >= 70) {
            notifikasi_kirim_role($pdo, 'kabalai', 'sertifikat',
                'Sertifikat menunggu TTD: ' . $namaAgen,
                'Post-Test lulus (nilai ' . $nilai . '). Silakan review & tanda tangan.',
                'sertifikat-approval',
                (int)$hasilId
            );
        }
    } catch (Throwable $e) {}

    $_SESSION['flash_nilai']  = $nilai;
    $_SESSION['flash_benar']  = $jawabanBenar;
    $_SESSION['flash_total']  = $totalPertanyaan;
    $_SESSION['flash_jenis']  = $bankSoal['jenis'];
    $_SESSION['flash_type']   = 'success';
    $_SESSION['flash_message'] = 'Test selesai! Nilai kamu: ' . $nilai;
    header("Location: hasil-test");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_message'] = 'Gagal menyimpan jawaban: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    header("Location: dashboard");
    exit;
}