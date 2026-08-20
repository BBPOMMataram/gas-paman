<?php
/**
 * Partial dropdown alamat NTB berjenjang + input manual (combobox).
 * Variabel opsional sebelum include:
 *   $alamatPrefix, $alamatRequired, $alamatKab, $alamatKec, $alamatDesa, $alamatDetail, $alamatShowDetail
 *
 * Setiap field (kab/kec/desa) memakai <input list="datalist"> supaya:
 * - bisa dipilih dari daftar suggestion
 * - bisa diketik manual jika wilayah belum ada di data
 */
if (!function_exists('ntb_wilayah_hierarki')) {
    require_once __DIR__ . '/../../core/ntb_wilayah_data.php';
}
$alamatPrefix = $alamatPrefix ?? 'alamat';
$alamatRequired = $alamatRequired ?? true;
$alamatKab = $alamatKab ?? '';
$alamatKec = $alamatKec ?? '';
$alamatDesa = $alamatDesa ?? '';
$alamatDetail = $alamatDetail ?? '';
$alamatShowDetail = $alamatShowDetail ?? true;
$req = $alamatRequired ? 'required' : '';
$inpClass = 'w-full px-6 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:ring-4 focus:ring-orange-600/10 focus:border-orange-600 outline-none transition-all font-semibold text-gray-800 text-sm';
$pfx = htmlspecialchars($alamatPrefix);
?>
<div class="space-y-4" data-ntb-alamat="<?= $pfx ?>"
     data-default-kab="<?= htmlspecialchars($alamatKab) ?>"
     data-default-kec="<?= htmlspecialchars($alamatKec) ?>"
     data-default-desa="<?= htmlspecialchars($alamatDesa) ?>">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="min-w-0">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kabupaten/Kota <?= $alamatRequired ? '<span class="text-red-800">*</span>' : '' ?></label>
            <input type="text" name="kab_kota" id="<?= $pfx ?>_kab" list="<?= $pfx ?>_kab_list"
                   value="<?= htmlspecialchars($alamatKab) ?>"
                   placeholder="Pilih atau ketik Kabupaten/Kota"
                   autocomplete="off" <?= $req ?> class="<?= $inpClass ?>">
            <datalist id="<?= $pfx ?>_kab_list"></datalist>
            <p class="mt-1 ml-1 text-[10px] text-gray-400 font-medium">Bisa dipilih dari daftar atau diketik manual</p>
        </div>
        <div class="min-w-0">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kecamatan <?= $alamatRequired ? '<span class="text-red-800">*</span>' : '' ?></label>
            <input type="text" name="kecamatan" id="<?= $pfx ?>_kec" list="<?= $pfx ?>_kec_list"
                   value="<?= htmlspecialchars($alamatKec) ?>"
                   placeholder="Pilih atau ketik Kecamatan"
                   autocomplete="off" <?= $req ?> class="<?= $inpClass ?>">
            <datalist id="<?= $pfx ?>_kec_list"></datalist>
            <p class="mt-1 ml-1 text-[10px] text-gray-400 font-medium">Bisa dipilih dari daftar atau diketik manual</p>
        </div>
        <div class="min-w-0">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Desa/Kelurahan <?= $alamatRequired ? '<span class="text-red-800">*</span>' : '' ?></label>
            <input type="text" name="desa" id="<?= $pfx ?>_desa" list="<?= $pfx ?>_desa_list"
                   value="<?= htmlspecialchars($alamatDesa) ?>"
                   placeholder="Pilih atau ketik Desa/Kelurahan"
                   autocomplete="off" <?= $req ?> class="<?= $inpClass ?>">
            <datalist id="<?= $pfx ?>_desa_list"></datalist>
            <p class="mt-1 ml-1 text-[10px] text-gray-400 font-medium">Bisa dipilih dari daftar atau diketik manual</p>
        </div>
    </div>
    <?php if ($alamatShowDetail): ?>
    <div>
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Detail Alamat (opsional)</label>
        <input type="text" name="alamat_detail" id="<?= $pfx ?>_detail"
               value="<?= htmlspecialchars($alamatDetail) ?>"
               placeholder="Contoh: Jl. Catur Warga No. 12, dekat pasar"
               class="<?= $inpClass ?>">
    </div>
    <?php endif; ?>
    <input type="hidden" name="alamat" id="<?= $pfx ?>_full" value="">
</div>