<?php
require_once 'config/database.php';
require_once 'core/auth.php';
cek_login();

$role = $_SESSION['role'] ?? '';
$isKabalai = ($role === 'kabalai');
$isAgen = ($role === 'agen');

if (!$isAgen && !$isKabalai) {
    header('Location: admin-dashboard');
    exit;
}

$userId = $_SESSION['user_id'];
$hasilId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$hasilId) {
    header('Location: ' . ($isKabalai ? 'sertifikat-approval' : 'hasil-test'));
    exit;
}

// Agen: hanya sertifikat miliknya. Kabalai: boleh lihat semua yang sudah disetujui.
if ($isKabalai) {
    $stmt = $pdo->prepare("
        SELECT ht.*, bs.judul, u.nama,
        kb.nama as nama_kabalai, kb.tanda_tangan as ttd_kabalai
        FROM hasil_test ht
        JOIN bank_soal bs ON bs.id = ht.bank_soal_id
        JOIN users u ON u.id = ht.user_id
        LEFT JOIN users kb ON kb.id = ht.signed_by
        WHERE ht.id = ? AND bs.jenis = 'post_test'
    ");
    $stmt->execute([$hasilId]);
} else {
    $stmt = $pdo->prepare("
        SELECT ht.*, bs.judul, u.nama,
        kb.nama as nama_kabalai, kb.tanda_tangan as ttd_kabalai
        FROM hasil_test ht
        JOIN bank_soal bs ON bs.id = ht.bank_soal_id
        JOIN users u ON u.id = ht.user_id
        LEFT JOIN users kb ON kb.id = ht.signed_by
        WHERE ht.id = ? AND ht.user_id = ? AND bs.jenis = 'post_test'
    ");
    $stmt->execute([$hasilId, $userId]);
}
$hasil = $stmt->fetch();

$redirectBack = $isKabalai ? 'sertifikat-approval' : 'hasil-test';

if (!$hasil || (float)$hasil['nilai'] < 70) {
    $_SESSION['flash_type'] = 'error';
    $_SESSION['flash_message'] = 'Sertifikat tersedia setelah lulus Post-Test dengan nilai minimal 70.';
    header('Location: ' . $redirectBack);
    exit;
}

if ($hasil['status_sertifikat'] !== 'disetujui') {
    $_SESSION['flash_type'] = 'info';
    $_SESSION['flash_message'] = $isKabalai
        ? 'Sertifikat ini belum ditandatangani.'
        : 'Sertifikat kamu masih menunggu tanda tangan elektronik dari Kepala Balai.';
    header('Location: ' . $redirectBack);
    exit;
}

$bulanId = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$waktuSelesai = strtotime($hasil['waktu_selesai']);
$tanggalLengkap = date('d', $waktuSelesai) . ' ' . $bulanId[(int) date('n', $waktuSelesai)] . ' ' . date('Y', $waktuSelesai);

// Nomor unik mengikuti format template: 36B.03.25/GASPAMAN/01.
$nomorSertifikat = sprintf('36B.03.%s/GASPAMAN/%02d', date('y', $waktuSelesai), $hasil['id']);
$namaAgen = htmlspecialchars($hasil['nama'], ENT_QUOTES, 'UTF-8');
$fileNameSafe = preg_replace('/[^A-Za-z0-9_-]/', '-', $hasil['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat | <?= $namaAgen ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/futuristik.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f1f0f7; font-family: 'Libre Baskerville', serif; }
        .cert-page {
            width: 1200px; height: 848.4px; position: relative; overflow: hidden;
            background-position: center; background-size: cover; background-repeat: no-repeat;
            box-shadow: 0 24px 55px -24px rgba(67, 38, 14, .35);
        }
        .cert-page-utama { background-image: url('assets/sertifikat/template-sertifikat-utama.png'); }
        .cert-page-materi { background-image: url('assets/sertifikat/template-sertifikat-materi.png'); }
        .field-mask { position: absolute; background: rgba(255, 255, 255, .94); border-radius: 18px; }
        .cert-number { position: absolute; top: 29.3%; left: 31%; width: 38%; text-align: center; font-size: 21px; letter-spacing: .5px; }
        .mask-number { top: 28.7%; left: 30%; width: 40%; height: 5.8%; }
        .cert-name { position: absolute; top: 40.4%; left: 19%; width: 62%; text-align: center; color: #1e2a5e; font-family: 'Dancing Script', cursive; font-size: 58px; line-height: 1; }
        .mask-name { top: 39.2%; left: 18%; width: 64%; height: 10.5%; }
        .cert-date-badge { position: absolute; top: 52.5%; left: 28%; width: 44%; padding: 13px 20px; border-radius: 18px; background: linear-gradient(90deg, #ff2e32, #ff914d); color: #fff; text-align: center; font-weight: 700; font-size: 23px; letter-spacing: 1px; }
        .mask-badge { top: 51.8%; left: 27%; width: 46%; height: 8.5%; }
        /* Penutup teks bawaan template: putih solid tanpa sudut melengkung,
           persis selebar teksnya, sehingga tak terlihat (latar template putih). */
        .cover-band { position: absolute; background: #ffffff; }
        .cover-c { top: 70.6%; left: 39.2%; width: 23.5%; height: 2.4%; }
        .cover-d { top: 73.5%; left: 33.6%; width: 34.7%; height: 2.5%; }
        .cover-e { top: 86.9%; left: 35.4%; width: 29.9%; height: 2.7%; }
        /* Baris teks footer: selaras dengan ukuran & warna tinta template (hitam, serif) */
        .cert-footer-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 46%;
            text-align: center;
            color: #111111;
            white-space: nowrap;
            line-height: 1;
            z-index: 5;
        }
        /* 1. Tanggal */
        .cert-date-footer { top: 70.85%; font-size: 15px; font-weight: 400; }
        /* 2. Jabatan */
        .cert-jabatan { top: 73.83%; font-size: 15px; font-weight: 600; }
        /* 3. TTD — duduk rapi tepat di atas nama, menyatu tanpa kotak */
        .cert-signature {
            position: absolute;
            top: 76.3%;
            left: 50%;
            transform: translateX(-50%);
            width: 30%;
            height: 10.4%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            z-index: 5;
        }
        .cert-signature img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            mix-blend-mode: multiply;
        }
        /* 4. Nama Kepala Balai */
        .cert-kabalai-name {
            top: 87.27%;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        @media (max-width: 1240px) { .cert-page { transform: scale(calc((100vw - 32px) / 1200)); transform-origin: top left; margin-bottom: calc(848.4px * ((100vw - 32px) / 1200 - 1)); } }
    </style>
</head>
<body class="py-8 px-4">
    <div class="max-w-[1200px] mx-auto mb-6 flex items-center justify-between font-sans">
        <a href="<?= $isKabalai ? 'sertifikat-approval' : 'hasil-test' ?>" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-800 font-bold text-sm transition-colors"><i class="fas fa-arrow-left"></i> <?= $isKabalai ? 'Kembali ke Persetujuan' : 'Kembali ke Hasil Test' ?></a>
        <button type="button" onclick="downloadSertifikat()" class="inline-flex items-center gap-2 px-6 py-3 bg-red-800 hover:bg-black text-white font-black text-[11px] uppercase tracking-widest rounded-2xl shadow-lg transition-all active:scale-95"><i class="fas fa-download"></i> Download Sertifikat (PDF)</button>
    </div>

    <div id="certCapture" class="space-y-8 w-[1200px] mx-auto">
        <section class="cert-page cert-page-utama">
            <div class="field-mask mask-number"></div>
            <p class="cert-number">Nomor: <?= htmlspecialchars($nomorSertifikat, ENT_QUOTES, 'UTF-8') ?></p>
            <div class="field-mask mask-name"></div>
            <h1 class="cert-name"><?= $namaAgen ?></h1>
            <div class="field-mask mask-badge"></div>
            <p class="cert-date-badge">MATARAM, <?= strtoupper($tanggalLengkap) ?></p>
            <div class="cover-band cover-c"></div>
            <div class="cover-band cover-d"></div>
            <div class="cover-band cover-e"></div>
            <p class="cert-footer-line cert-date-footer">Mataram, <?= $tanggalLengkap ?></p>
            <p class="cert-footer-line cert-jabatan">Kepala Balai Besar POM di Mataram</p>
            <?php if (!empty($hasil['ttd_kabalai'])): ?>
            <div class="cert-signature">
                <img src="uploads/<?= htmlspecialchars($hasil['ttd_kabalai'], ENT_QUOTES, 'UTF-8') ?>" alt="Tanda Tangan">
            </div>
            <?php endif; ?>
            <?php if (!empty($hasil['nama_kabalai'])): ?>
            <?php $namaPanjang = (mb_strlen($hasil['nama_kabalai']) > 30); ?>
            <p class="cert-footer-line cert-kabalai-name" <?= $namaPanjang ? 'style="font-size:13px; letter-spacing:1px;"' : '' ?>><?= htmlspecialchars($hasil['nama_kabalai'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </section>
        <section class="cert-page cert-page-materi"></section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function downloadSertifikat() {
            Swal.fire({ title: 'Menyiapkan sertifikat...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const pages = document.querySelectorAll('.cert-page');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                for (let index = 0; index < pages.length; index++) {
                    const canvas = await html2canvas(pages[index], { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
                    if (index > 0) pdf.addPage('a4', 'landscape');
                    pdf.addImage(canvas.toDataURL('image/jpeg', 0.96), 'JPEG', 0, 0, 297, 210);
                }
                const pdfBase64 = pdf.output('datauristring');
                try {
                    const body = new FormData();
                    body.append('hasil_id', '<?= (int)$hasilId ?>');
                    body.append('pdf_data', pdfBase64);
                    await fetch('simpan-sertifikat', { method: 'POST', body });
                } catch (backupErr) {
                    console.warn('Backup sertifikat gagal:', backupErr);
                }
                pdf.save('Sertifikat-GAS-PAMAN-<?= $fileNameSafe ?>.pdf');
                Swal.close();
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Sertifikat gagal diunduh, coba lagi.' });
            }
        }
    </script>
    <script src="assets/js/futuristik.js?v=2"></script>
</body>
</html>