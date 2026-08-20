<?php
/**
 * Hierarki wilayah NTB untuk dropdown berjenjang.
 * Kabupaten/Kota -> Kecamatan -> Desa/Kelurahan
 */
function ntb_wilayah_hierarki() {
    static $data = null;
    if ($data !== null) return $data;
    $json = <<<'JSON'
{"Kota Mataram": {"Ampenan": ["Ampenan Selatan", "Ampenan Tengah", "Ampenan Utara", "Banjar", "Bintaro", "Dayan Peken", "Kebon Sari", "Pajarakan Karya", "Pejeruk", "Taman Sari"], "Sekarbela": ["Jempong Baru", "Karang Pule", "Kekalik Jaya", "Tanjung Karang", "Tanjung Karang Permai"], "Mataram": ["Mataram Timur", "Pagesangan", "Pagesangan Barat", "Pagesangan Timur", "Pagutan", "Pagutan Barat", "Pagutan Timur", "Pejanggik", "Punia"], "Selaparang": ["Dasan Agung", "Dasan Agung Baru", "Gomong", "Karang Baru", "Mataram Barat", "Monjok", "Monjok Barat", "Monjok Timur", "Rembiga"], "Cakranegara": ["Cakranegara Barat", "Cakranegara Selatan", "Cakranegara Selatan Baru", "Cakranegara Timur", "Cakranegara Utara", "Mayura", "Sapta Marga", "Sayang-Sayang"], "Sandubaya": ["Abian Tubuh Baru", "Babakan", "Bertais", "Dasan Cermen", "Mandalika", "Selagalas", "Turida"]}, "Lombok Barat": {"Gerung": ["Babussalam", "Banyu Urip", "Beleke", "Dasan Geres", "Dasan Tapen", "Gapuk", "Gerung Selatan", "Gerung Utara", "Kebonayu", "Mesanggok", "Sukamaju", "Taman Ayu", "Tempos"], "Kediri": ["Banyumulek", "Dasan Baru", "Gelogor", "Jagaraga Asri", "Kediri", "Kediri Selatan", "Lelede", "Montong Are", "Ombe Baru", "Rumak"], "Narmada": ["Badrain", "Batu Kumbung", "Batu Mekar", "Bengkel", "Duman", "Golong", "Keru", "Lebah Sempage", "Lembuak", "Mekarsari", "Narmada", "Nyur Lembang", "Pakuan", "Peresak", "Sedau", "Selat", "Sembung", "Suranadi", "Terong Tawah"], "Lingsar": ["Batu Kuta", "Bug-Bug", "Buwun Sejati", "Dasan Geria", "Gegelang", "Karang Bayan", "Langko", "Lingsar", "Peteluan Indah", "Saribaye", "Sigerongan"], "Gunungsari": ["Bukit Barejulat", "Dopang", "Gelangsar", "Guntur Macan", "Jatisela", "Jeringo", "Kekait", "Kekeri", "Mambalan", "Mekarsari", "Midang", "Penimbung", "Ranjok", "Sesela", "Taman Sari"], "Batu Layar": ["Batu Layar", "Batu Layar Barat", "Bengkaung", "Lembah Sari", "Meninting", "Sandik", "Senggigi", "Senteluk", "Sigerongan"], "Labuapi": ["Bagik Polak", "Bagik Polak Barat", "Bajur", "Bengkel", "Karang Bongkot", "Kuranji", "Kuranji Dalang", "Labuapi", "Merembu", "Perampuan", "Telagawar", "Terong Tawah"], "Kuripan": ["Jagaraga", "Kuripan", "Kuripan Selatan", "Kuripan Timur", "Kuripan Utara", "Pokoh"], "Lembar": ["Cemara", "Jembatan Kembar", "Jembatan Kembar Timur", "Labuan Tereng", "Lembar", "Lembar Selatan", "Mareje", "Mareje Timur", "Sekotong Tengah"], "Sekotong": ["Batu Putih", "Buwun Mas", "Cendi Manik", "Gili Gede Indah", "Kedaro", "Pelangan", "Sekotong Barat", "Sekotong Tengah", "Taman Baru"]}, "Lombok Tengah": {"Praya": ["Aik Mual", "Bunut Baok", "Gonjak", "Jago", "Leneng", "Mekar Sari", "Montong Terep", "Pandan Indah", "Permai", "Praya", "Semayan", "Tiwugalih"], "Pujut": ["Bangket Parak", "Batu Jangkih", "Kuta", "Mertak", "Pengembur", "Pengengat", "Rembitan", "Sengkol", "Sukadana", "Teruwai", "Tumpak"], "Jonggat": ["Barejulat", "Batu Tulis", "Bonjeruk", "Bunkate", "Gelangsar", "Jelantik", "Labulia", "Pengenjek", "Perina", "Puyung", "Sukamulia", "Ubung"], "Pringgarata": ["Bagu", "Bilebante", "Murbaya", "Pesuku", "Pringgarata", "Sepakek", "Sintung", "Taman Indah"], "Batukliang": ["Aik Darek", "Beber", "Bujak", "Karang Sidemen", "Mantang", "Mekar Bersatu", "Pagutan", "Selebung", "Teratak"], "Kopang": ["Bebuak", "Dasan Iyan", "Kopang Rembiga", "Lendang Aren", "Monggas", "Montong Gamang", "Mujur", "Semoyang", "Wajageseng"], "Janapria": ["Dumega", "Jango", "Janapria", "Kerembaik", "Langko", "Lekor", "Loang Maka", "Nyerot", "Pendem", "Saba", "Selebung Rembiga"]}, "Lombok Timur": {"Selong": ["Dane Rase", "Kelayu Jorong", "Kelayu Selatan", "Kelayu Utara", "Kembang Sari", "Majidi", "Rakam", "Sandubaya", "Selong", "Senyiur"], "Sikur": ["Gelangsar", "Kesik", "Kota Raja", "Montong Baan", "Montong Baan Selatan", "Rempung", "Semaya", "Sikur", "Silung", "Tetebatu", "Tetebatu Selatan"], "Masbagik": ["Danger", "Lendang Nangka", "Lendang Nangka Utara", "Masbagik Selatan", "Masbagik Timur", "Masbagik Utara", "Masbagik Utara Baru"], "Sukamulia": ["Dasan Lekong", "Padamara", "Setanggor", "Setanggor Selatan", "Sukamulia", "Sukamulia Timur", "Transa"], "Terara": ["Embung Kandong", "Embung Rungka", "Jantuk", "Ladon", "Rarang", "Rarang Selatan", "Rarang Tengah", "Santong", "Sukadana", "Suradadi", "Terara"], "Sambelia": ["Bagik", "Dara Ubi", "Obel Obel", "Padak Guar", "Sambelia", "Sugian", "Belanting"], "Sembalun": ["Bilok Petung", "Sajang", "Sembalun", "Sembalun Bumbung", "Sembalun Lawang", "Sembalun Timba Gading"], "Aikmel": ["Aikmel", "Aikmel Barat", "Aikmel Timur", "Aikmel Utara", "Kalijaga", "Kalijaga Baru", "Kalijaga Selatan", "Kalijaga Timur", "Kalijaga Tengah", "Lenek", "Lenek Baru", "Lenek Duren", "Lenek Kali Bambang", "Lenek Lauq", "Lenek Pesiraman", "Toya", "Toya Rinjani"], "Keruak": ["Batu Putik", "Dane Rase", "Keruak", "Ketapang Raya", "Pijot", "Pijot Utara", "Selebung Ketangga", "Senyiur", "Sepit", "Setungke", "Tanjung Luar"], "Jerowaru": ["Batunampar", "Batunampar Selatan", "Ekas Buana", "Jerowaru", "Pandan Wangi", "Pemongkong", "Sekaroh", "Seriwe", "Sukaraja", "Wakan"], "Sakra": ["Borok Toyang", "Gelangsar", "Kembang Kuning", "Kota Raja", "Lekong", "Rensing", "Rensing Bat", "Rensing Raya", "Sakra", "Sakra Selatan", "Suangi", "Suangi Timur"], "Pringgabaya": ["Labuhan Lombok", "Pohgading", "Pohgading Timur", "Pringgabaya", "Pringgabaya Utara", "Seruni Maja", "Telaga Waru"]}, "Lombok Utara": {"Bayan": ["Akar-Akar", "Anyar", "Bayan", "Karang Bajo", "Loloan", "Mumbul Sari", "Sambik Elen", "Senaru", "Sukadana"], "Kayangan": ["Gumantar", "Kayangan", "Pendua", "Salut", "Santong", "Selengan", "Sesait", "Wilantah"], "Gangga": ["Bentek", "Genggelang", "Gondang", "Rempek", "Rempek Datar", "Sambik Bangkol", "Tanjung"], "Tanjung": ["Jenggala", "Medana", "Sigar Penjalin", "Sokong", "Tanjung", "Tegal Maja"], "Pemenang": ["Gili Indah", "Malaka", "Pemenang Barat", "Pemenang Timur"]}, "Sumbawa Barat": {"Taliwang": ["Bugis", "Dalam", "Kuangsari", "Labuhan Lalar", "Menala", "Sampir", "Sarakral", "Taliwang", "Tamekan"], "Seteluk": ["Air Suning", "Kelanir", "Lamus", "Rempe", "Seran", "Seteluk Atas", "Seteluk Tengah"], "Brang Rea": ["Desar", "Lamuntet", "Rarak Ronges", "Sapugara", "Tepas", "Tepas Sepakat"], "Jereweh": ["Belo", "Beru", "Dasan Anyar", "Goa", "Jereweh", "Kertasari"], "Maluk": ["Benete", "Bukit Damai", "Maluk", "Mantun", "Pasir Putih"], "Sekongkang": ["Ai Kangkung", "Sekongkang Atas", "Sekongkang Bawah", "Tatar", "Tongo"]}, "Sumbawa": {"Sumbawa": ["Bugis", "Dalam", "Lempeh", "Pekat", "Samapuin", "Seketeng", "Semayan", "Uma Beringin"], "Alas": ["Alas", "Dalam", "Jarang", "Luar", "Marente", "Sabedo"], "Buer": ["Buer", "Kalabeso", "Labuhan Bajo", "Labuhan Jambu", "Labuhan Mapin", "Mapin Baru", "Mapin Kebak", "Mapin Rea"], "Utan": ["Genting", "Jorok", "Kokar", "Labuhan Bontong", "Labuhan Jambu", "Orong Bawa", "Pukat", "Stowe Brang", "Tengah"], "Moyo Hulu": ["Batubulan", "Berare", "Jorok", "Kukin", "Leseng", "Lito", "Moyo", "Pernek", "Pungka", "Semamung"], "Moyo Hilir": ["Batu Bangka", "Berare", "Kukin", "Labuhan Ijuk", "Moyo", "Ngeru", "Poto", "Serading"], "Plampang": ["Plampang", "Sepayung", "Sepayung Selatan", "Teluk Santong", "Usar"]}, "Dompu": {"Dompu": ["Bada", "Bali", "Dompu", "Kandai", "Karijawa", "Katua", "Mangge", "Mbawi", "Potu", "Saneo", "Syambu"], "Woja": ["Baka Jaya", "Mada Pangga", "Matua", "Nowa", "Rabajuka", "Sambori", "Simpasai", "Wawonduru", "Woja"], "Kempo": ["Dore", "Kempo", "Sori Wutok", "Sowa", "Ta'a", "Tolohe"], "Hu'u": ["Adu", "Cempi Jaya", "Daha", "Hu'u", "Jala", "Merada"], "Pekat": ["Calabai", "Kadindi", "Kadindi Barat", "Nangamiro", "Pekat", "Sori Nomo"]}, "Kota Bima": {"Rasanae Barat": ["Dara", "Pane", "Paruga", "Sarae", "Tanjung"], "Rasanae Timur": ["Kodo", "Kumbe", "Lelamase", "Nitu", "Rontu"], "Mpunda": ["Mangge", "Monggonao", "Penanae", "Penatoi", "Santi"], "Raba": ["Kendo", "Nungga", "Oimbo", "Rabadompu Barat", "Rabadompu Timur", "Rabangodu Selatan", "Rabangodu Utara"], "Asakota": ["Jatiwangi", "Kolo", "Melayu", "Sarae"]}, "Bima": {"Woha": ["Dadibou", "Keli", "Naru", "Pandai", "Rabakodu", "Rada", "Samili", "Tenga", "Woha"], "Belo": ["Belo", "Diha", "Ncandi", "Ntori", "Piong", "Soki"], "Sape": ["Boke", "Bugis", "Kowo", "Nae", "Parangina", "Sangeang", "Sape"], "Wawo": ["Kangga", "Maria", "Ntori", "Tamuwo", "Wawo"], "Lambitu": ["Kuta", "Mada", "Sambori", "Teta"], "Palibelo": ["Belanting", "Pandopa", "Rupe", "Sana", "Teke"], "Langgudu": ["Karumbu", "Kawuwu", "Kuta", "Laha", "Ntori"], "Lambu": ["Hidirasa", "Kalama", "Lambu", "Lanta", "Soro"], "Parado": ["Kuta", "Parado", "Parado Rato", "Tandaiga"], "Sanggar": ["Boro", "Kore", "Oi Kasipute", "Taloko"], "Tambora": ["Hidirasa", "Kawinda Toi", "Labuhan Kenanga", "Oi Bura", "Tambora"]}}
JSON;
    $data = json_decode($json, true) ?: [];
    return $data;
}

/** Gabung jadi teks alamat standar */
function ntb_format_alamat($kab, $kec, $desa, $detail = '') {
    $parts = [];
    if ($detail) $parts[] = trim($detail);
    if ($desa) $parts[] = 'Desa/Kel. ' . trim($desa);
    if ($kec) $parts[] = 'Kec. ' . trim($kec);
    if ($kab) $parts[] = trim($kab);
    $parts[] = 'Nusa Tenggara Barat';
    return implode(', ', $parts);
}

/** Ambil lat/lng dari kecamatan (atau kab) */

/** Cek apakah koordinat masih di wilayah NTB (approx bounding box) */
function ntb_is_inside_ntb($lat, $lng): bool {
    $lat = (float)$lat;
    $lng = (float)$lng;
    return $lat >= -9.25 && $lat <= -8.05 && $lng >= 115.40 && $lng <= 119.45;
}

/**
 * Ambil lat/lng terbaik dari kab + kec (dengan alias nama).
 */
function ntb_coords_from_wilayah($kab, $kec) {
    if (!function_exists('ntb_wilayah_list')) {
        $helper = __DIR__ . '/ntb_helper.php';
        if (is_file($helper)) require_once $helper;
    }
    $list = function_exists('ntb_wilayah_list') ? ntb_wilayah_list() : [];

    $alias = [
        'Batu Layar' => 'Batulayar',
        'Batulayar' => 'Batulayar',
    ];

    $candidates = [];
    $kec = trim((string)$kec);
    $kab = trim((string)$kab);
    if ($kec !== '') {
        $candidates[] = $kec;
        if (isset($alias[$kec])) $candidates[] = $alias[$kec];
        foreach ($list as $name => $ll) {
            if (strcasecmp($name, $kec) === 0) $candidates[] = $name;
        }
    }
    if ($kab !== '') {
        $candidates[] = $kab;
        foreach ($list as $name => $ll) {
            if (strcasecmp($name, $kab) === 0) $candidates[] = $name;
        }
    }

    foreach ($candidates as $name) {
        if (isset($list[$name])) {
            return ['lat' => (float)$list[$name][0], 'lng' => (float)$list[$name][1], 'sumber' => $name];
        }
    }
    return ['lat' => -8.5833, 'lng' => 116.1167, 'sumber' => 'Kota Mataram'];
}

/**
 * Koordinat final untuk disimpan & ditampilkan di peta.
 * GPS hanya dipakai jika berada di dalam NTB; selain itu pakai titik kab/kec.
 */
function ntb_resolve_coords($lat, $lng, $kab, $kec) {
    $kab = trim((string)$kab);
    $kec = trim((string)$kec);
    // PRIORITAS: alamat (kab/kec) — ini yang ditampilkan di peta sebaran
    if ($kab !== '' || $kec !== '') {
        $wil = ntb_coords_from_wilayah($kab, $kec);
        return [
            'lat' => (float)$wil['lat'],
            'lng' => (float)$wil['lng'],
            'gps' => false,
            'sumber' => $wil['sumber'] ?? 'wilayah',
        ];
    }
    // Tanpa alamat: GPS hanya jika di NTB
    if ($lat !== null && $lng !== null && is_finite((float)$lat) && is_finite((float)$lng)
        && ntb_is_inside_ntb($lat, $lng)) {
        return ['lat' => (float)$lat, 'lng' => (float)$lng, 'gps' => true, 'sumber' => 'GPS'];
    }
    $wil = ntb_coords_from_wilayah('', '');
    return ['lat' => (float)$wil['lat'], 'lng' => (float)$wil['lng'], 'gps' => false, 'sumber' => 'default'];
}


/** Coba ekstrak kab/kec/desa dari teks alamat lama */
function ntb_parse_alamat_parts($alamat) {
    $alamat = trim((string)$alamat);
    $out = ['kab' => '', 'kec' => '', 'desa' => '', 'detail' => ''];
    if ($alamat === '') return $out;

    $data = ntb_wilayah_hierarki();
    $a = strtolower($alamat);

    foreach ($data as $kab => $kecs) {
        if (stripos($alamat, $kab) !== false) {
            $out['kab'] = $kab;
            break;
        }
        $alias = str_replace(['Kota ', 'Kabupaten '], '', $kab);
        if ($alias !== $kab && stripos($alamat, $alias) !== false) {
            $out['kab'] = $kab;
            break;
        }
    }

    if ($out['kab'] && isset($data[$out['kab']])) {
        foreach ($data[$out['kab']] as $kec => $desas) {
            if (stripos($alamat, $kec) !== false) {
                $out['kec'] = $kec;
                foreach ($desas as $desa) {
                    if (stripos($alamat, $desa) !== false) {
                        $out['desa'] = $desa;
                        break;
                    }
                }
                break;
            }
        }
    }

    // Detail: bagian sebelum "Desa/Kel"
    if (preg_match('/^(.*?)(?:,\s*)?(?:Desa\/Kel\.|Desa|Kelurahan)/iu', $alamat, $m)) {
        $det = trim($m[1], " \t,");
        if ($det && strlen($det) > 2) $out['detail'] = $det;
    }

    // Fallback regex eksplisit
    if ($out['desa'] === '' && preg_match('/\b(?:desa|ds\.?|kelurahan|kel\.?)\s+([^,]+)/iu', $alamat, $m)) {
        $out['desa'] = trim($m[1]);
    }
    if ($out['kec'] === '' && preg_match('/\b(?:kecamatan|kec\.?)\s+([^,]+)/iu', $alamat, $m)) {
        $out['kec'] = trim($m[1]);
    }

    return $out;
}
