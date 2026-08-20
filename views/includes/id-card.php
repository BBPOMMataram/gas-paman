<?php
/**
 * Partial: ID Card Agen GAS-PAMAN
 *
 * Variabel yang HARUS di-set sebelum include file ini:
 *   $card_data     = array ['nama' => ..., 'agen_id' => ..., 'foto_profil' => ...]
 *
 * Variabel opsional:
 *   $card_size     = 'lg' (default), 'sm', atau 'xs'
 *                     lg -> halaman profil (preview besar + download)
 *                     sm -> preview medium (mis. modal / kartu detail)
 *                     xs -> badge mini (mis. di banner dashboard)
 *   $card_download = true/false -> tampilkan tombol download PNG (default false)
 *   $card_uid      = string unik, dipakai sebagai suffix ID elemen jika partial
 *                    ini di-include lebih dari sekali di halaman yang sama (default 'card')
 */

$card_size     = in_array($card_size ?? 'lg', ['lg', 'sm', 'xs']) ? $card_size : 'lg';
$card_download = $card_download ?? false;
$card_uid      = $card_uid ?? 'card';

// Preset ukuran per varian: [lg, sm, xs] -- urutan HARUS selalu sama
$P = [
    'card_w'          => ['300px', '190px', '130px'],
    'card_h'          => ['534px', '338px', '231px'],
    'card_radius'     => ['32px', '22px', '14px'],
    'greenbar_w'      => ['32px', '20px', '14px'],
    'stripe_tl_top'   => ['92px', '58px', '40px'],
    'stripe_tl_left'  => ['16px', '10px', '7px'],
    'stripe_w'        => ['34px', '22px', '15px'],
    'stripe_h'        => ['150px', '90px', '62px'],
    'stripe_br_bottom'=> ['150px', '92px', '64px'],
    'stripe_br_right' => ['46px', '28px', '20px'],
    'badge_top'       => ['16px', '10px', '7px'],
    'badge_left'      => ['16px', '10px', '7px'],
    'badge_gap'       => ['6px', '4px', '3px'],
    'badge_padding'   => ['5px 14px 5px 5px', '3px 8px 3px 3px', '2px 6px 2px 2px'],
    'bpom_h'          => ['26px', '16px', '11px'],
    'gas_size'        => ['36px', '22px', '16px'],
    'title_top'       => ['14px', '8px', '5px'],
    'title_right'     => ['34px', '20px', '15px'],
    'title_size'      => ['22px', '13px', '9px'],
    // Cutout dibuat tinggi agar menyatu dengan komposisi kartu, bukan seperti foto kotak.
    'photo_top'       => ['94px', '58px', '40px'],
    'photo_w'         => ['282px', '178px', '122px'],
    'photo_h'         => ['410px', '260px', '178px'],
    'photo_radius'    => ['28px', '18px', '12px'],
    'photo_main_radius' => ['24px', '16px', '10px'],
    'photo_border'    => ['7px', '4px', '3px'],
    'name_left'       => ['18px', '10px', '7px'],
    'name_bottom'     => ['80px', '48px', '35px'],
    'name_right'      => ['42px', '26px', '18px'],
    'name_radius'     => ['14px', '8px', '6px'],
    'name_padding'    => ['10px 18px', '5px 10px', '3px 6px'],
    'name_size'       => ['16px', '10px', '7px'],
    'id_left'         => ['18px', '10px', '7px'],
    'id_bottom'       => ['30px', '18px', '13px'],
    'id_right'        => ['58px', '34px', '24px'],
    'id_radius'       => ['14px', '8px', '6px'],
    'id_padding'      => ['8px 18px', '4px 10px', '2px 6px'],
    'id_size'         => ['14px', '9px', '6px'],
];
$idx = ['lg' => 0, 'sm' => 1, 'xs' => 2][$card_size];
$v = fn($key) => $P[$key][$idx];

$foto_file = $card_data['foto_profil'] ?: 'default.png';
$foto_src = 'uploads/' . rawurlencode($foto_file) . '?v=' . time();
$nama_agen = htmlspecialchars($card_data['nama'] ?? '-');
$id_agen   = htmlspecialchars($card_data['agen_id'] ?? '-');
$tahun_card = date('Y');
?>
<style>
    .idcard-<?= $card_uid ?> {
        width: <?= $v('card_w') ?>;
        height: <?= $v('card_h') ?>;
        border-radius: <?= $v('card_radius') ?>;
        position: relative;
        overflow: hidden;
        background: linear-gradient(155deg, #fb923c 0%, #f97316 30%, #f59e0b 60%, #fbbf24 100%);
        box-shadow: 0 25px 50px -15px rgba(154, 52, 18, 0.45);
        font-family: 'Plus Jakarta Sans', sans-serif;
        flex-shrink: 0;
    }

    .idcard-<?= $card_uid ?> .ic-greenbar {
        position: absolute; top: 0; right: 0; bottom: 0;
        width: <?= $v('greenbar_w') ?>;
        background: linear-gradient(180deg, #14532d 0%, #16a34a 55%, #4ade80 100%);
    }

    .idcard-<?= $card_uid ?> .ic-stripes {
        position: absolute;
        opacity: 0.5;
        background: repeating-linear-gradient(-55deg, rgba(255,255,255,0.9) 0 4px, transparent 4px 11px);
    }
    .idcard-<?= $card_uid ?> .ic-stripes-tl {
        top: <?= $v('stripe_tl_top') ?>; left: <?= $v('stripe_tl_left') ?>;
        width: <?= $v('stripe_w') ?>; height: <?= $v('stripe_h') ?>;
    }
    .idcard-<?= $card_uid ?> .ic-stripes-br {
        bottom: <?= $v('stripe_br_bottom') ?>; right: <?= $v('stripe_br_right') ?>;
        width: <?= $v('stripe_w') ?>; height: <?= $v('stripe_h') ?>;
    }

    .idcard-<?= $card_uid ?> .ic-badge {
        position: absolute; top: <?= $v('badge_top') ?>; left: <?= $v('badge_left') ?>;
        display: flex; align-items: center; gap: <?= $v('badge_gap') ?>;
        background: #fff; border-radius: 999px;
        padding: <?= $v('badge_padding') ?>;
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
        z-index: 5;
    }
    .idcard-<?= $card_uid ?> .ic-badge img.ic-bpom { height: <?= $v('bpom_h') ?>; object-fit: contain; }
    .idcard-<?= $card_uid ?> .ic-badge img.ic-gas {
        height: <?= $v('gas_size') ?>; width: <?= $v('gas_size') ?>;
        border-radius: 50%; object-fit: cover; border: 1.5px solid #fff;
    }

    .idcard-<?= $card_uid ?> .ic-title {
        position: absolute; top: <?= $v('title_top') ?>; right: <?= $v('title_right') ?>;
        text-align: right; color: #fff; font-weight: 800; text-transform: uppercase;
        line-height: 1.02; letter-spacing: -0.5px;
        font-size: <?= $v('title_size') ?>;
        text-shadow: 0 0 16px rgba(255,255,255,0.4), 0 2px 6px rgba(0,0,0,0.15);
    }

    .idcard-<?= $card_uid ?> .ic-photo-wrap {
        position: absolute; left: 50%; top: <?= $v('photo_top') ?>;
        transform: translateX(-50%);
        width: <?= $v('photo_w') ?>; height: <?= $v('photo_h') ?>;
        z-index: 2;
    }
    .idcard-<?= $card_uid ?> .ic-photo-ghost {
        display: block;
        position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain;
        object-position: center top;
        opacity: 0.16;
        filter: blur(1px) saturate(0.8);
        transform: scale(1.18) translateY(-12%);
        transform-origin: center top;
    }
    .idcard-<?= $card_uid ?> .ic-photo-main {
        position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain;
        object-position: center top;
        border-radius: <?= $v('photo_main_radius') ?>;
        border: 0;
        /* Mengikuti bentuk subjek PNG transparan, bukan kotak foto. */
        filter: drop-shadow(0 14px 14px rgba(63, 24, 8, 0.28))
                drop-shadow(0 4px 5px rgba(255, 255, 255, 0.14));
        z-index: 2;
    }

    .idcard-<?= $card_uid ?> .ic-name-banner {
        position: absolute; left: <?= $v('name_left') ?>; bottom: <?= $v('name_bottom') ?>;
        right: <?= $v('name_right') ?>;
        background: #1e2a5e; border-radius: <?= $v('name_radius') ?>;
        padding: <?= $v('name_padding') ?>;
        color: #fff; font-weight: 800; text-transform: uppercase;
        font-size: <?= $v('name_size') ?>;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        z-index: 5;
    }
    .idcard-<?= $card_uid ?> .ic-id-banner {
        position: absolute; left: <?= $v('id_left') ?>; bottom: <?= $v('id_bottom') ?>;
        right: <?= $v('id_right') ?>;
        background: rgba(35, 35, 35, 0.72); border-radius: <?= $v('id_radius') ?>;
        padding: <?= $v('id_padding') ?>;
        color: #f3f4f6; font-weight: 700; letter-spacing: 0.5px;
        font-size: <?= $v('id_size') ?>;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        z-index: 5;
    }
</style>

<div class="flex flex-col items-center gap-4">
    <div class="idcard-<?= $card_uid ?>" id="idcard-el-<?= $card_uid ?>">
        <div class="ic-greenbar"></div>
        <div class="ic-stripes ic-stripes-tl"></div>
        <div class="ic-stripes ic-stripes-br"></div>

        <div class="ic-badge">
            <img src="views/bpom.webp" class="ic-bpom" alt="BPOM">
            <img src="views/gas-paman-round.png" class="ic-gas" alt="GAS-PAMAN">
        </div>

        <div class="ic-title">AGEN<br>GAS-PAMAN<br><?= $tahun_card ?></div>

        <div class="ic-photo-wrap">
            <img src="<?= htmlspecialchars($foto_src, ENT_QUOTES, 'UTF-8') ?>" class="ic-photo-ghost" data-idcard-photo crossorigin="anonymous">
            <img src="<?= htmlspecialchars($foto_src, ENT_QUOTES, 'UTF-8') ?>" class="ic-photo-main" data-idcard-photo crossorigin="anonymous">
        </div>

        <div class="ic-name-banner"><?= $nama_agen ?></div>
        <div class="ic-id-banner"><?= $id_agen ?></div>
    </div>

    <?php if ($card_download): ?>
    <button type="button" onclick="downloadIdCard_<?= $card_uid ?>()"
        class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 hover:bg-black text-white font-black text-[11px] uppercase tracking-widest rounded-2xl shadow-lg transition-all active:scale-95">
        <i class="fas fa-download"></i> Download ID Card (PNG)
    </button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadIdCard_<?= $card_uid ?>() {
            const el = document.getElementById('idcard-el-<?= $card_uid ?>');
            html2canvas(el, { scale: 3, useCORS: true, backgroundColor: null }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'ID-Card-<?= preg_replace('/[^A-Za-z0-9_-]/', '', $id_agen) ?: 'agen' ?>.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(() => {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'ID Card gagal diunduh, coba lagi.' });
                } else {
                    alert('ID Card gagal diunduh, coba lagi.');
                }
            });
        }
    </script>
    <?php endif; ?>
</div>
