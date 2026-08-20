<?php
/**
 * Helper sebaran wilayah NTB (untuk peta Leaflet & statistik).
 * Deteksi KECAMATAN dari teks alamat (keyword matching) - lebih presisi
 * daripada level kabupaten/kota, tapi tetap ringan (murni cocok kata kunci,
 * gak manggil API luar / geocoding, jadi cepet & gak gantung koneksi internet).
 *
 * Kalau alamat cuma nyebut nama kabupaten/kota tanpa kecamatan spesifik,
 * fallback ke titik pusat kabupaten/kota itu.
 */

/**
 * Titik kecamatan spesifik (koordinat perkiraan pusat kecamatan).
 */
function ntb_kecamatan_list() {
    return [
        // Kota Mataram
        'Ampenan'          => [-8.5830, 116.0830],
        'Sekarbela'        => [-8.6000, 116.0870],
        'Mataram'          => [-8.5880, 116.1080],
        'Selaparang'       => [-8.5700, 116.1170],
        'Cakranegara'      => [-8.5930, 116.1330],
        'Sandubaya'        => [-8.6100, 116.1330],

        // Lombok Barat
        'Sekotong'         => [-8.7500, 116.0500],
        'Lembar'           => [-8.7000, 116.0670],
        'Gerung'           => [-8.6330, 116.1000],
        'Labuapi'          => [-8.6330, 116.1170],
        'Kediri'           => [-8.6170, 116.1170],
        'Kuripan'          => [-8.6330, 116.1330],
        'Narmada'          => [-8.5830, 116.1830],
        'Lingsar'          => [-8.5670, 116.1670],
        'Gunungsari'       => [-8.5330, 116.1500],
        'Batulayar'        => [-8.4830, 116.0830],

        // Lombok Tengah
        'Praya'            => [-8.7000, 116.2670],
        'Pujut'            => [-8.8330, 116.2830],
        'Jonggat'          => [-8.6670, 116.2330],
        'Pringgarata'      => [-8.6330, 116.2670],
        'Batukliang'       => [-8.6330, 116.3170],
        'Kopang'           => [-8.6670, 116.3170],
        'Janapria'         => [-8.7170, 116.3170],

        // Lombok Timur
        'Selong'           => [-8.6670, 116.5330],
        'Sikur'            => [-8.6500, 116.4830],
        'Masbagik'         => [-8.6330, 116.5000],
        'Sukamulia'        => [-8.6330, 116.5330],
        'Terara'           => [-8.7000, 116.5000],
        'Sambelia'         => [-8.3830, 116.6170],
        'Sembalun'         => [-8.3830, 116.5500],
        'Aikmel'           => [-8.5670, 116.5500],
        'Keruak'           => [-8.8000, 116.5330],
        'Jerowaru'         => [-8.8330, 116.5670],
        'Sakra'            => [-8.7330, 116.5170],
        'Pringgabaya'      => [-8.5170, 116.5830],

        // Lombok Utara
        'Bayan'            => [-8.2830, 116.4000],
        'Kayangan'         => [-8.3170, 116.3670],
        'Gangga'           => [-8.3500, 116.3170],
        'Tanjung'          => [-8.3670, 116.2670],
        'Pemenang'         => [-8.4000, 116.1170],

        // Sumbawa Barat
        'Taliwang'         => [-8.7500, 116.8670],
        'Seteluk'          => [-8.7000, 116.9000],
        'Brang Rea'        => [-8.7670, 116.9170],
        'Brang Ene'        => [-8.8000, 116.9330],
        'Jereweh'          => [-8.8500, 116.6670],
        'Maluk'            => [-8.9330, 116.6170],
        'Sekongkang'       => [-8.9330, 116.5500],

        // Sumbawa
        'Sumbawa Besar'    => [-8.5000, 117.4170],
        'Alas'             => [-8.6330, 117.1500],
        'Buer'             => [-8.5830, 117.1170],
        'Utan'             => [-8.5500, 117.1500],
        'Moyo Hulu'        => [-8.6170, 117.4830],
        'Moyo Hilir'       => [-8.5500, 117.4670],
        'Lape'             => [-8.6330, 117.6670],
        'Lopok'            => [-8.6330, 117.7000],
        'Plampang'         => [-8.7000, 117.7500],
        'Labangka'         => [-8.7830, 117.7670],
        'Labuhan Badas'    => [-8.4500, 117.4170],
        'Unter Iwes'       => [-8.5170, 117.4000],
        'Rhee'             => [-8.5500, 117.2670],

        // Dompu
        'Dompu'            => [-8.5330, 118.4670],
        'Woja'             => [-8.5000, 118.4830],
        'Kempo'            => [-8.5170, 118.2670],
        'Hu\'u'            => [-8.7830, 118.4670],
        'Kilo'             => [-8.4170, 118.2670],
        'Pekat'            => [-8.2830, 118.1670],
        'Manggelewa'       => [-8.4670, 118.4000],
        'Pajo'             => [-8.5500, 118.4000],

        // Kota Bima
        'Rasanae Barat'    => [-8.4670, 118.7170],
        'Rasanae Timur'    => [-8.4500, 118.7500],
        'Mpunda'           => [-8.4500, 118.7170],
        'Raba'             => [-8.4670, 118.7500],
        'Asakota'          => [-8.4170, 118.7000],

        // Bima
        'Woha'             => [-8.5330, 118.6330],
        'Belo'             => [-8.5830, 118.6500],
        'Wawo'             => [-8.5000, 118.7830],
        'Sape'             => [-8.5330, 119.0170],
        'Lambitu'          => [-8.5000, 118.7830],
        'Palibelo'         => [-8.5170, 118.7000],
        'Langgudu'         => [-8.6670, 118.8330],
        'Lambu'            => [-8.6170, 118.9830],
        'Parado'           => [-8.6330, 118.8500],
        'Sanggar'          => [-8.1500, 118.1500],
        'Tambora'          => [-8.2500, 117.9830],
        'Bolo'             => [-8.3830, 118.5170],
        'Wera'             => [-8.2670, 118.7500],
    ];
}

/**
 * Titik fallback pusat kabupaten/kota (dipakai kalau alamat cuma nyebut
 * nama kabupaten/kota tanpa kecamatan spesifik).
 */
function ntb_kabupaten_list() {
    return [
        'Kota Mataram'      => [-8.5833, 116.1167],
        'Lombok Barat'      => [-8.6500, 116.1167],
        'Lombok Tengah'     => [-8.7000, 116.2700],
        'Lombok Timur'      => [-8.6500, 116.5300],
        'Lombok Utara'      => [-8.3500, 116.4000],
        'Sumbawa'           => [-8.5000, 117.4200],
        'Sumbawa Barat'     => [-8.7500, 116.8500],
        'Dompu'             => [-8.5367, 118.4633],
        'Bima'              => [-8.4600, 118.7200],
        'Kota Bima'         => [-8.4600, 118.7267],
    ];
}

/**
 * Keyword spesifik per kecamatan. Dicek DULUAN sebelum keyword kabupaten,
 * biar alamat yang nyebut nama kecamatan kepetakan lebih presisi.
 */
function ntb_keyword_kecamatan() {
    return [
        'Ampenan'       => ['ampenan'],
        'Sekarbela'     => ['sekarbela'],
        'Selaparang'    => ['selaparang'],
        'Cakranegara'   => ['cakranegara'],
        'Sandubaya'     => ['sandubaya'],

        'Sekotong'      => ['sekotong'],
        'Lembar'        => ['lembar'],
        'Gerung'        => ['gerung'],
        'Labuapi'       => ['labuapi'],
        'Kediri'        => ['kediri'],
        'Kuripan'       => ['kuripan'],
        'Narmada'       => ['narmada'],
        'Lingsar'       => ['lingsar'],
        'Gunungsari'    => ['gunungsari'],
        'Batulayar'     => ['batulayar', 'senggigi'],

        'Praya'         => ['praya'],
        'Pujut'         => ['pujut', 'kuta lombok', 'kuta, lombok'],
        'Jonggat'       => ['jonggat'],
        'Pringgarata'   => ['pringgarata'],
        'Batukliang'    => ['batukliang'],
        'Kopang'        => ['kopang'],
        'Janapria'      => ['janapria'],

        'Selong'        => ['selong'],
        'Sikur'         => ['sikur'],
        'Masbagik'      => ['masbagik'],
        'Sukamulia'     => ['sukamulia'],
        'Terara'        => ['terara'],
        'Sambelia'      => ['sambelia'],
        'Sembalun'      => ['sembalun'],
        'Aikmel'        => ['aikmel'],
        'Keruak'        => ['keruak'],
        'Jerowaru'      => ['jerowaru'],
        'Sakra'         => ['sakra'],
        'Pringgabaya'   => ['pringgabaya'],

        'Bayan'         => ['bayan'],
        'Kayangan'      => ['kayangan'],
        'Gangga'        => ['gangga'],
        'Tanjung'       => ['tanjung'],
        'Pemenang'      => ['pemenang', 'gili trawangan', 'gili air', 'gili meno'],

        'Taliwang'      => ['taliwang'],
        'Seteluk'       => ['seteluk'],
        'Brang Rea'     => ['brang rea'],
        'Brang Ene'     => ['brang ene'],
        'Jereweh'       => ['jereweh'],
        'Maluk'         => ['maluk'],
        'Sekongkang'    => ['sekongkang'],

        'Sumbawa Besar' => ['sumbawa besar'],
        'Alas'          => ['alas'],
        'Buer'          => ['buer'],
        'Utan'          => ['utan'],
        'Moyo Hulu'     => ['moyo hulu'],
        'Moyo Hilir'    => ['moyo hilir'],
        'Lape'          => ['lape'],
        'Lopok'         => ['lopok'],
        'Plampang'      => ['plampang'],
        'Labangka'      => ['labangka'],
        'Labuhan Badas' => ['labuhan badas'],
        'Unter Iwes'    => ['unter iwes'],
        'Rhee'          => ['rhee'],

        'Woja'          => ['woja'],
        'Kempo'         => ['kempo'],
        'Hu\'u'         => ['hu\'u', 'huu'],
        'Kilo'          => ['kilo'],
        'Pekat'         => ['pekat'],
        'Manggelewa'    => ['manggelewa'],
        'Pajo'          => ['pajo'],

        'Rasanae Barat' => ['rasanae barat'],
        'Rasanae Timur' => ['rasanae timur', 'rasanaye'],
        'Mpunda'        => ['mpunda'],
        'Raba'          => ['raba'],
        'Asakota'       => ['asakota'],

        'Woha'          => ['woha'],
        'Belo'          => ['belo'],
        'Wawo'          => ['wawo'],
        'Sape'          => ['sape'],
        'Lambitu'       => ['lambitu'],
        'Palibelo'      => ['palibelo'],
        'Langgudu'      => ['langgudu'],
        'Lambu'         => ['lambu'],
        'Parado'        => ['parado'],
        'Sanggar'       => ['sanggar'],
        'Tambora'       => ['tambora'],
        'Bolo'          => ['bolo'],
        'Wera'          => ['wera'],
    ];
}

/**
 * Keyword umum per kabupaten/kota - dicek belakangan, cuma dipakai kalau
 * gak ada keyword kecamatan spesifik yang cocok.
 */
function ntb_keyword_kabupaten() {
    return [
        'Kota Mataram'  => ['kota mataram', 'mataram'],
        'Lombok Barat'  => ['lombok barat', 'lobar'],
        'Lombok Tengah' => ['lombok tengah', 'loteng'],
        'Lombok Timur'  => ['lombok timur', 'lotim'],
        'Lombok Utara'  => ['lombok utara', 'klu'],
        'Sumbawa Barat' => ['sumbawa barat', 'ksb'],
        'Sumbawa'       => ['sumbawa'],
        'Dompu'         => ['dompu'],
        'Kota Bima'     => ['kota bima'],
        'Bima'          => ['bima'],
    ];
}

/**
 * Ekstrak nama wilayah (kecamatan, atau fallback kabupaten/kota) dari alamat.
 * Return nama wilayah atau 'Lainnya / Tidak Diketahui'.
 */
function ntb_detect_wilayah($alamat) {
    if (!$alamat) return 'Lainnya / Tidak Diketahui';
    $a = strtolower($alamat);

    // 1. Coba cocokkan ke kecamatan spesifik dulu (lebih presisi)
    foreach (ntb_keyword_kecamatan() as $kecamatan => $keywords) {
        foreach ($keywords as $kw) {
            if (str_contains($a, $kw)) {
                return $kecamatan;
            }
        }
    }

    // 2. Fallback ke nama kabupaten/kota kalau kecamatannya gak kesebut
    foreach (ntb_keyword_kabupaten() as $kabupaten => $keywords) {
        foreach ($keywords as $kw) {
            if (str_contains($a, $kw)) {
                return $kabupaten;
            }
        }
    }

    return 'Lainnya / Tidak Diketahui';
}

/**
 * Gabungan semua titik yang bisa dikembalikan (kecamatan + fallback kabupaten),
 * dipakai buat lookup koordinat & nge-render legenda.
 */
function ntb_wilayah_list() {
    return array_merge(ntb_kecamatan_list(), ntb_kabupaten_list());
}

/**
 * Mapping tiap kecamatan ke kabupaten/kota induknya - dipakai buat bikin
 * rekap tabel per kabupaten (lebih ringkas daripada per kecamatan).
 */
function ntb_kecamatan_ke_kabupaten() {
    return [
        'Ampenan' => 'Kota Mataram', 'Sekarbela' => 'Kota Mataram', 'Mataram' => 'Kota Mataram',
        'Selaparang' => 'Kota Mataram', 'Cakranegara' => 'Kota Mataram', 'Sandubaya' => 'Kota Mataram',

        'Sekotong' => 'Lombok Barat', 'Lembar' => 'Lombok Barat', 'Gerung' => 'Lombok Barat',
        'Labuapi' => 'Lombok Barat', 'Kediri' => 'Lombok Barat', 'Kuripan' => 'Lombok Barat',
        'Narmada' => 'Lombok Barat', 'Lingsar' => 'Lombok Barat', 'Gunungsari' => 'Lombok Barat',
        'Batulayar' => 'Lombok Barat',

        'Praya' => 'Lombok Tengah', 'Pujut' => 'Lombok Tengah', 'Jonggat' => 'Lombok Tengah',
        'Pringgarata' => 'Lombok Tengah', 'Batukliang' => 'Lombok Tengah', 'Kopang' => 'Lombok Tengah',
        'Janapria' => 'Lombok Tengah',

        'Selong' => 'Lombok Timur', 'Sikur' => 'Lombok Timur', 'Masbagik' => 'Lombok Timur',
        'Sukamulia' => 'Lombok Timur', 'Terara' => 'Lombok Timur', 'Sambelia' => 'Lombok Timur',
        'Sembalun' => 'Lombok Timur', 'Aikmel' => 'Lombok Timur', 'Keruak' => 'Lombok Timur',
        'Jerowaru' => 'Lombok Timur', 'Sakra' => 'Lombok Timur', 'Pringgabaya' => 'Lombok Timur',

        'Bayan' => 'Lombok Utara', 'Kayangan' => 'Lombok Utara', 'Gangga' => 'Lombok Utara',
        'Tanjung' => 'Lombok Utara', 'Pemenang' => 'Lombok Utara',

        'Taliwang' => 'Sumbawa Barat', 'Seteluk' => 'Sumbawa Barat', 'Brang Rea' => 'Sumbawa Barat',
        'Brang Ene' => 'Sumbawa Barat', 'Jereweh' => 'Sumbawa Barat', 'Maluk' => 'Sumbawa Barat',
        'Sekongkang' => 'Sumbawa Barat',

        'Sumbawa Besar' => 'Sumbawa', 'Alas' => 'Sumbawa', 'Buer' => 'Sumbawa', 'Utan' => 'Sumbawa',
        'Moyo Hulu' => 'Sumbawa', 'Moyo Hilir' => 'Sumbawa', 'Lape' => 'Sumbawa', 'Lopok' => 'Sumbawa',
        'Plampang' => 'Sumbawa', 'Labangka' => 'Sumbawa', 'Labuhan Badas' => 'Sumbawa',
        'Unter Iwes' => 'Sumbawa', 'Rhee' => 'Sumbawa',

        'Dompu' => 'Dompu', 'Woja' => 'Dompu', 'Kempo' => 'Dompu', 'Hu\'u' => 'Dompu',
        'Kilo' => 'Dompu', 'Pekat' => 'Dompu', 'Manggelewa' => 'Dompu', 'Pajo' => 'Dompu',

        'Rasanae Barat' => 'Kota Bima', 'Rasanae Timur' => 'Kota Bima', 'Mpunda' => 'Kota Bima',
        'Raba' => 'Kota Bima', 'Asakota' => 'Kota Bima',

        'Woha' => 'Bima', 'Belo' => 'Bima', 'Wawo' => 'Bima', 'Sape' => 'Bima', 'Lambitu' => 'Bima',
        'Palibelo' => 'Bima', 'Langgudu' => 'Bima', 'Lambu' => 'Bima', 'Parado' => 'Bima',
        'Sanggar' => 'Bima', 'Tambora' => 'Bima', 'Bolo' => 'Bima', 'Wera' => 'Bima',
    ];
}

/**
 * Agregasi array baris jadi hitungan per KABUPATEN/KOTA (bukan per kecamatan)
 * - dipakai buat tabel rekap ringkas di dashboard. Urutannya tetap 10
 * kabupaten/kota NTB walau hitungannya 0, biar tabelnya konsisten.
 */
function ntb_aggregate_kabupaten($rows, $alamatKey = 'alamat', $bobotKey = null) {
    $petaKabupaten = ntb_kecamatan_ke_kabupaten();
    $namaKabupaten = array_keys(ntb_kabupaten_list());

    $counts = array_fill_keys($namaKabupaten, 0);
    $counts['Lainnya / Tidak Diketahui'] = 0;

    foreach ($rows as $row) {
        $wil = ntb_detect_wilayah($row[$alamatKey] ?? '');
        $kabupaten = $petaKabupaten[$wil] ?? (in_array($wil, $namaKabupaten, true) ? $wil : 'Lainnya / Tidak Diketahui');
        $bobot = $bobotKey ? (int)($row[$bobotKey] ?? 1) : 1;
        $counts[$kabupaten] = ($counts[$kabupaten] ?? 0) + max(1, $bobot);
    }

    return $counts;
}

/**
 * Agregasi array baris (dengan kolom alamat) menjadi hitungan per wilayah + koordinat.
 * $rows: array of ['alamat' => ..., 'bobot' => int optional]
 */
function ntb_aggregate($rows, $alamatKey = 'alamat', $bobotKey = null) {
    $wilayahList = ntb_wilayah_list();
    $counts = [];
    $counts['Lainnya / Tidak Diketahui'] = 0;

    foreach ($rows as $row) {
        $wil = ntb_detect_wilayah($row[$alamatKey] ?? '');
        $bobot = $bobotKey ? (int)($row[$bobotKey] ?? 1) : 1;
        if (!isset($counts[$wil])) $counts[$wil] = 0;
        $counts[$wil] += max(1, $bobot);
    }

    $markers = [];
    foreach ($wilayahList as $nama => $latlng) {
        if (($counts[$nama] ?? 0) <= 0) continue;
        $markers[] = [
            'nama'  => $nama,
            'lat'   => $latlng[0],
            'lng'   => $latlng[1],
            'total' => $counts[$nama],
        ];
    }
    return ['counts' => $counts, 'markers' => $markers];
}


/**
 * Parse alamat teks jadi struktur: desa, kecamatan, kabupaten, detail, koordinat.
 */
function ntb_parse_alamat($alamat) {
    $alamat = trim((string)$alamat);
    $desa = null;
    $kecamatan = null;
    $kabupaten = null;

    if ($alamat !== '') {
        // 1) Kab/kota resmi (nama panjang dulu)
        $kabs = array_keys(ntb_kabupaten_list());
        usort($kabs, function ($a, $b) { return strlen($b) - strlen($a); });
        foreach ($kabs as $namaKab) {
            if (stripos($alamat, $namaKab) !== false) {
                $kabupaten = $namaKab;
                break;
            }
        }

        // 2) Kecamatan: utamakan pola "Kec. XXX" / "Kecamatan XXX"
        if (preg_match('/\b(?:kecamatan|kec\.?)\s+([A-Za-z0-9\'\.\-\s]{2,40})/iu', $alamat, $m)) {
            $cand = trim($m[1], " \t\n\r\0\x0B,.");
            if (strpos($cand, ',') !== false) $cand = trim(explode(',', $cand)[0]);
            // cocokkan ke nama resmi (case-insensitive)
            foreach (array_keys(ntb_kecamatan_list()) as $namaKec) {
                if (strcasecmp($namaKec, $cand) === 0 || stripos($cand, $namaKec) === 0) {
                    $kecamatan = $namaKec;
                    break;
                }
            }
            if (!$kecamatan) $kecamatan = $cand;
        }
        // 3) Fallback: cari nama kecamatan di teks (panjang dulu, hindari "Mataram" dari "Kota Mataram")
        if (!$kecamatan) {
            $kecs = array_keys(ntb_kecamatan_list());
            usort($kecs, function ($a, $b) { return strlen($b) - strlen($a); });
            foreach ($kecs as $namaKec) {
                // jangan match kecamatan yang sama dengan nama kab (mis. Dompu) tanpa konteks kec
                if (stripos($alamat, $namaKec) !== false) {
                    // Skip jika ini hanya bagian dari "Kota XXX" dan nama kec = potongan kota
                    if ($kabupaten && stripos($kabupaten, $namaKec) !== false && stripos($alamat, 'Kec') === false) {
                        continue;
                    }
                    $kecamatan = $namaKec;
                    break;
                }
            }
        }

        if (preg_match('/\b(?:desa|ds\.?|kelurahan|kel\.?|desa\/kel\.?)\s+([A-Za-z0-9\'\.\-\s]{2,40})/iu', $alamat, $m)) {
            $desa = trim($m[1], " \t\n\r\0\x0B,.");
            if (strpos($desa, ',') !== false) $desa = trim(explode(',', $desa)[0]);
        }
        if (!$kabupaten && preg_match('/\b(?:kabupaten|kab\.?|kota)\s+([A-Za-z0-9\'\.\-\s]{2,40})/iu', $alamat, $m)) {
            $kabupaten = trim($m[1], " \t\n\r\0\x0B,.");
            if (strpos($kabupaten, ',') !== false) $kabupaten = trim(explode(',', $kabupaten)[0]);
            if (strcasecmp($kabupaten, 'Bima') === 0 && stripos($alamat, 'Kota Bima') !== false) {
                $kabupaten = 'Kota Bima';
            }
        }
    }

    $wil = ntb_detect_wilayah($alamat);
    $petaKec = ntb_kecamatan_ke_kabupaten();
    $namaKabList = array_keys(ntb_kabupaten_list());

    if (!$kecamatan && isset($petaKec[$wil])) {
        $kecamatan = $wil;
    }
    if (!$kabupaten) {
        if (isset($petaKec[$wil])) {
            $kabupaten = $petaKec[$wil];
        } elseif (in_array($wil, $namaKabList, true)) {
            $kabupaten = $wil;
        }
    }

    $coords = ntb_wilayah_list();
    $lat = null;
    $lng = null;
    if (isset($coords[$wil])) {
        $lat = $coords[$wil][0];
        $lng = $coords[$wil][1];
    } elseif ($kecamatan && isset($coords[$kecamatan])) {
        $lat = $coords[$kecamatan][0];
        $lng = $coords[$kecamatan][1];
    } elseif ($kabupaten && isset($coords[$kabupaten])) {
        $lat = $coords[$kabupaten][0];
        $lng = $coords[$kabupaten][1];
    }

    return [
        'alamat' => $alamat,
        'desa' => $desa,
        'kecamatan' => $kecamatan,
        'kabupaten' => $kabupaten,
        'wilayah' => $wil,
        'lat' => $lat,
        'lng' => $lng,
    ];
}

/**
 * Agregasi + daftar lokasi detail (untuk panel di luar peta).
 * Return: counts, markers (dengan detail alamat), locations (list per baris).
 */
function ntb_aggregate_detail($rows, $alamatKey = 'alamat', $bobotKey = null, $labelKey = null) {
    $base = ntb_aggregate($rows, $alamatKey, $bobotKey);
    $locations = [];
    $byWilayahSamples = [];

    foreach ($rows as $row) {
        $alamat = $row[$alamatKey] ?? '';
        $parsed = ntb_parse_alamat($alamat);
        $bobot = $bobotKey ? (int)($row[$bobotKey] ?? 1) : 1;
        $label = $labelKey ? ($row[$labelKey] ?? '') : '';
        $item = [
            'label' => $label,
            'alamat' => $alamat,
            'desa' => $parsed['desa'],
            'kecamatan' => $parsed['kecamatan'],
            'kabupaten' => $parsed['kabupaten'],
            'wilayah' => $parsed['wilayah'],
            'lat' => $parsed['lat'],
            'lng' => $parsed['lng'],
            'bobot' => max(1, $bobot),
        ];
        $locations[] = $item;
        $w = $parsed['wilayah'];
        if (!isset($byWilayahSamples[$w])) $byWilayahSamples[$w] = [];
        if (count($byWilayahSamples[$w]) < 3) {
            $byWilayahSamples[$w][] = $alamat;
        }
    }

    // Perkaya popup marker dengan contoh alamat + desa
    foreach ($base['markers'] as &$m) {
        $samples = $byWilayahSamples[$m['nama']] ?? [];
        $m['samples'] = $samples;
        $m['detail'] = implode(' | ', array_filter(array_map(function ($s) {
            $p = ntb_parse_alamat($s);
            $parts = array_filter([$p['desa'] ? 'Desa/Kel. ' . $p['desa'] : null, $p['kecamatan'] ? 'Kec. ' . $p['kecamatan'] : null]);
            return $parts ? implode(', ', $parts) : $s;
        }, $samples)));
    }
    unset($m);

    $base['locations'] = $locations;
    return $base;
}


/**
 * Agregasi marker berbasis GPS (lat/lng per baris).
 * Jika lat/lng ada → pin tepat di koordinat itu.
 * Jika tidak → fallback ke pusat wilayah dari teks alamat.
 */
function ntb_aggregate_gps($rows, $alamatKey = 'alamat', $latKey = 'latitude', $lngKey = 'longitude', $bobotKey = null, $labelKey = null) {
    if (!function_exists('ntb_is_inside_ntb')) {
        $w = __DIR__ . '/ntb_wilayah_data.php';
        if (is_file($w)) require_once $w;
    }
    $markers = [];
    $locations = [];
    $counts = [];
    // Pusat default NTB (Mataram) — dipakai kalau GPS & deteksi wilayah gagal, biar pin tetap muncul
    $defaultLat = -8.5833;
    $defaultLng = 116.1167;
    $i = 0;

    foreach ($rows as $row) {
        $alamat = trim((string)($row[$alamatKey] ?? ''));
        $parsed = ntb_parse_alamat($alamat);

        // Kolom terstruktur (paling akurat) > hasil parse teks alamat
        $kab = trim((string)($row['kab_kota'] ?? $row['kabupaten'] ?? $parsed['kabupaten'] ?? ''));
        $kec = trim((string)($row['kecamatan'] ?? $parsed['kecamatan'] ?? ''));
        $desa = trim((string)($row['desa'] ?? $parsed['desa'] ?? ''));
        if ($desa !== '') $parsed['desa'] = $desa;
        if ($kec !== '') $parsed['kecamatan'] = $kec;
        if ($kab !== '') $parsed['kabupaten'] = $kab;

        // PRIORITAS:
        // 1) Koordinat tersimpan (pin manual / GPS yang dikonfirmasi agen) — sumber utama peta
        // 2) Titik pusat kab/kec dari kolom terstruktur atau parse alamat — cadangan
        // 3) Default NTB + offset — biar pin tetap muncul
        $lat = null;
        $lng = null;
        $hasGps = false;
        $sumber = 'Alamat';

        $rawLat = $row[$latKey] ?? null;
        $rawLng = $row[$lngKey] ?? null;
        $tmpLat = ($rawLat !== null && $rawLat !== '') ? (float)$rawLat : null;
        $tmpLng = ($rawLng !== null && $rawLng !== '') ? (float)$rawLng : null;
        if ($tmpLat !== null && $tmpLng !== null && is_finite($tmpLat) && is_finite($tmpLng)
            && function_exists('ntb_is_inside_ntb') && ntb_is_inside_ntb($tmpLat, $tmpLng)) {
            $lat = $tmpLat;
            $lng = $tmpLng;
            $hasGps = true;
            $sumber = 'Titik tersimpan';
        } elseif (($kab !== '' || $kec !== '') && function_exists('ntb_coords_from_wilayah')) {
            $c = ntb_coords_from_wilayah($kab, $kec);
            $lat = (float)$c['lat'];
            $lng = (float)$c['lng'];
            $sumber = 'Alamat: ' . ($c['sumber'] ?? ($kec ?: $kab));
        } elseif ($parsed['lat'] !== null && $parsed['lng'] !== null) {
            $lat = (float)$parsed['lat'];
            $lng = (float)$parsed['lng'];
            $sumber = 'Wilayah terdeteksi';
        }

        if ($lat === null || $lng === null || !is_finite($lat) || !is_finite($lng)) {
            $offset = ($i % 10) * 0.004;
            $lat = $defaultLat + $offset;
            $lng = $defaultLng + (($i % 5) * 0.004);
            $sumber = 'Perkiraan (alamat belum terdeteksi)';
            $hasGps = false;
        }

        // Offset stabil per entitas agar pin di kecamatan sama tidak numpuk
        if (!$hasGps) {
            $seed = crc32(strtolower(($labelKey ? (string)($row[$labelKey] ?? '') : '') . '|' . $alamat . '|' . $i));
            $lat += (($seed % 11) - 5) * 0.0035;
            $lng += ((($seed >> 8) % 11) - 5) * 0.0035;
        }

        $bobot = $bobotKey ? (int)($row[$bobotKey] ?? 1) : 1;
        $bobot = max(1, $bobot);
        $label = $labelKey ? (string)($row[$labelKey] ?? '') : '';
        $wil = $parsed['wilayah'] ?: 'Lainnya / Tidak Diketahui';
        $counts[$wil] = ($counts[$wil] ?? 0) + $bobot;

        $locations[] = [
            'label' => $label,
            'alamat' => $alamat,
            'desa' => $parsed['desa'],
            'kecamatan' => $parsed['kecamatan'],
            'kabupaten' => $parsed['kabupaten'],
            'wilayah' => $wil,
            'lat' => $lat,
            'lng' => $lng,
            'gps' => $hasGps,
            'bobot' => $bobot,
        ];

        $markers[] = [
            'nama' => $label !== '' ? $label : ($parsed['desa'] ? 'Desa/Kel. ' . $parsed['desa'] : ($wil !== 'Lainnya / Tidak Diketahui' ? $wil : ($alamat ?: 'Lokasi'))),
            'lat' => (float)$lat,
            'lng' => (float)$lng,
            'total' => $bobot,
            'detail' => implode(' · ', array_filter([
                $alamat ?: null,
                $parsed['desa'] ? 'Desa/Kel. ' . $parsed['desa'] : null,
                $parsed['kecamatan'] ? 'Kec. ' . $parsed['kecamatan'] : null,
                $parsed['kabupaten'] ?: null,
                $sumber,
            ])),
            'gps' => $hasGps,
        ];
        $i++;
    }

    return ['counts' => $counts, 'markers' => $markers, 'locations' => $locations];
}
