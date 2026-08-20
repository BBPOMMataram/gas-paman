<?php
/**
 * views/includes/tabel-rekap-edukasi.php
 * Partial bersama: rekap masyarakat yang diedukasi (1 baris = 1 konsumen),
 * dipakai oleh log-laporan-agen.php, log-laporan.php (admin), dan cetak-log-laporan.php.
 *
 * Berisi helper:
 *   - rekap_edukasi_ambil(PDO $pdo, $agenId, $mulai, $selesai, $status) : array
 *     => ['rows' => catatan_harian + nama_agen/kode_agen, 'files' => catatan_id => [file_path]]
 *   - rekap_edukasi_tabel(array $rows, array $filesByCatatan, bool $showAgenKolom, string $mode)
 *     => echo tabel (mode 'screen' | 'print'; CSS ada di assets/css/futuristik.css)
 */

if (!function_exists('rekap_edukasi_ambil')) {
    /**
     * Ambil daftar catatan edukasi (masyarakat) + foto bukti dalam satu kali query.
     * $status kosong => semua kecuali draft (sama seperti riwayat.php).
     */
    function rekap_edukasi_ambil(PDO $pdo, $agenId = 0, $mulai = '', $selesai = '', $status = '') {
        $query = "SELECT c.*, u.nama AS nama_agen, u.agen_id AS kode_agen
                  FROM catatan_harian c
                  JOIN users u ON u.id = c.user_id
                  WHERE 1=1";
        $params = [];

        if ($agenId) {
            $query .= " AND c.user_id = ?";
            $params[] = (int)$agenId;
        }
        if ($mulai && $selesai) {
            $query .= " AND c.tanggal BETWEEN ? AND ?";
            $params[] = $mulai;
            $params[] = $selesai;
        }
        if (in_array($status, ['draft', 'pending', 'approved', 'revisi'], true)) {
            $query .= " AND c.status_review = ?";
            $params[] = $status;
        } else {
            $query .= " AND c.status_review != 'draft'";
        }

        $query .= " ORDER BY c.tanggal DESC, c.id DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $filesByCatatan = [];
        if ($rows) {
            $cids = array_column($rows, 'id');
            $ph = implode(',', array_fill(0, count($cids), '?'));
            $stmtF = $pdo->prepare("SELECT catatan_id, file_path FROM catatan_files WHERE catatan_id IN ($ph) ORDER BY id");
            $stmtF->execute($cids);
            foreach ($stmtF->fetchAll() as $f) {
                $filesByCatatan[$f['catatan_id']][] = $f['file_path'];
            }
        }

        return ['rows' => $rows, 'files' => $filesByCatatan];
    }
}

if (!function_exists('rekap_edukasi_tabel')) {
    function rekap_edukasi_tabel(array $rows, array $filesByCatatan, bool $showAgenKolom = false, string $mode = 'screen') {
        $isPrint = ($mode === 'print');
        $role = $_SESSION['role'] ?? '';
        // Tombol hapus hanya di layar (bukan cetak), untuk agen & admin.
        $tampilHapus = !$isPrint && in_array($role, ['agen', 'admin'], true);

        // URL kembali setelah hapus = halaman saat ini + filter saat ini (di-whitelist di hapus-catatan.php)
        $kembali = '';
        if ($tampilHapus) {
            $pageNow = basename($_SERVER['PHP_SELF'] ?? '', '.php');
            $anchor  = ($pageNow === 'log-laporan') ? '#rekap-edukasi' : '';
            $query   = $_SERVER['QUERY_STRING'] ?? '';
            $kembali = $pageNow . ($query !== '' ? '?' . $query : '') . $anchor;
        }
        $badgeCls = [
            'approved' => 'rekap-pill-approved',
            'revisi'   => 'rekap-pill-revisi',
            'draft'    => 'rekap-pill-draft',
            'pending'  => 'rekap-pill-pending',
        ];
        $labelStatus = [
            'approved' => 'Disetujui',
            'revisi'   => 'Revisi',
            'draft'    => 'Draft',
            'pending'  => 'Pending',
        ];
        ?>
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width:32px">No</th>
                    <th>Tanggal</th>
                    <?php if ($showAgenKolom): ?><th>Agen</th><?php endif; ?>
                    <th>Nama Konsumen</th>
                    <th>JK</th>
                    <th>Usia</th>
                    <th>Pekerjaan</th>
                    <th>No. HP</th>
                    <th>Nilai Pre</th>
                    <th>Nilai Post</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Foto Bukti</th>
                    <?php if ($tampilHapus): ?><th style="width:44px">Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= ($showAgenKolom ? 13 : 12) + ($tampilHapus ? 1 : 0) ?>" style="text-align:center; padding:32px 10px; color:#9ca3af; font-style:italic">
                        Belum ada data edukasi.
                    </td>
                </tr>
                <?php else: foreach ($rows as $no => $r):
                    $lokasi = trim($r['lokasi'] ?? '') !== '' ? $r['lokasi'] : ($r['alamat'] ?? '');
                    $files  = $filesByCatatan[$r['id']] ?? [];
                    $files  = array_slice($files, 0, 3);
                ?>
                <tr>
                    <td style="text-align:center"><?= $no + 1 ?></td>
                    <td style="white-space:nowrap"><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                    <?php if ($showAgenKolom): ?>
                    <td>
                        <?= htmlspecialchars($r['nama_agen'] ?? '') ?>
                        <?php if (!empty($r['kode_agen'])): ?><br><small style="color:#9ca3af"><?= htmlspecialchars($r['kode_agen']) ?></small><?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td style="font-weight:700"><?= htmlspecialchars($r['nama_konsumen']) ?></td>
                    <td><?= htmlspecialchars($r['jenis_kelamin'] ?? '—') ?></td>
                    <td style="text-align:center"><?= htmlspecialchars((string)($r['usia'] ?? '—')) ?></td>
                    <td><?= htmlspecialchars($r['pekerjaan'] ?? '—') ?></td>
                    <td style="white-space:nowrap"><?= htmlspecialchars($r['no_hp'] ?? '—') ?></td>
                    <td style="text-align:center"><?= $r['nilai_pre_test'] !== null && $r['nilai_pre_test'] !== '' ? number_format((float)$r['nilai_pre_test'], 0) : '—' ?></td>
                    <td style="text-align:center"><?= $r['nilai_post_test'] !== null && $r['nilai_post_test'] !== '' ? number_format((float)$r['nilai_post_test'], 0) : '—' ?></td>
                    <td><?= htmlspecialchars($lokasi ?: '—') ?></td>
                    <td style="text-align:center">
                        <?php if ($isPrint): ?>
                        <?= htmlspecialchars($labelStatus[$r['status_review']] ?? $r['status_review']) ?>
                        <?php else: ?>
                        <span class="rekap-pill <?= $badgeCls[$r['status_review']] ?? 'rekap-pill-pending' ?>">
                            <?= htmlspecialchars($labelStatus[$r['status_review']] ?? $r['status_review']) ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($files): ?>
                        <div class="rekap-foto">
                            <?php foreach ($files as $f):
                                if (preg_match('/\.mp4$/i', $f)): ?>
                                <span class="rekap-video">Video</span>
                                <?php else: ?>
                                <img src="uploads/<?= htmlspecialchars($f) ?>" alt="Bukti kegiatan" loading="lazy">
                                <?php endif;
                            endforeach; ?>
                        </div>
                        <?php else: ?>
                        <span style="color:#9ca3af">—</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($tampilHapus): ?>
                    <td style="text-align:center">
                        <button type="button" class="rekap-btn-hapus" title="Hapus data ini"
                                onclick="konfirmasiHapusRekap(<?= (int)$r['id'] ?>, '<?= addslashes('hapus-catatan?id=' . (int)$r['id'] . '&kembali=' . rawurlencode($kembali)) ?>')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <?php
        if ($tampilHapus && empty($GLOBALS['__rekapHapusScript'])) {
            $GLOBALS['__rekapHapusScript'] = true;
        ?>
        <script>
        function konfirmasiHapusRekap(id, url) {
            if (typeof Swal === 'undefined') {
                if (confirm('Hapus data edukasi ini? Data akan terhapus permanen.')) window.location.href = url;
                return;
            }
            Swal.fire({
                title: 'Hapus data edukasi?',
                text: 'Data masyarakat ini akan dihapus permanen dari sistem BBPOM!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#991b1b',
                cancelButtonColor: '#cbd5e1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[40px]' }
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }
        </script>
        <?php
        }
    }
}
