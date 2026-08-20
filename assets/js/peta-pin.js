/**
 * Peta Leaflet dengan 1 pin yang bisa digeser manual - dipakai di form
 * tambah/edit laporan (alamat konsumen/masyarakat) dan profil agen (alamat
 * agen). Posisi akhir pin adalah sumber utama titik yang disimpan ke
 * database, dan itu jadi titik yang muncul di peta sebaran.
 *
 * Alur:
 * 1. Peta dibuka di titik awal: koordinat tersimpan (kalau lagi edit),
 *    kalau belum ada dan alamat sudah terisi -> ditebak sekali lewat
 *    geocoding, kalau belum ada apa-apa -> pusat NTB.
 * 2. Begitu titik sudah ada (dari tersimpan / digeser manual / GPS), titik
 *    itu DIKUNCI - perubahan alamat berikutnya TIDAK lagi otomatis
 *    memindahkan pin, supaya titik yang sudah ditepatkan agen tidak
 *    ketiban-timpa tebakan baru tiap alamat diedit lagi.
 * 3. Kalau agen memang mau menebak ulang dari alamat terbaru, sediakan
 *    tombol/aksi eksplisit (cariUlangDariAlamat) yang bisa dipanggil kapan
 *    saja - ini satu-satunya cara titik berubah setelah terkunci, selain
 *    geser manual / klik peta / tombol GPS.
 *
 * Usage:
 *   const peta = initPetaPin({
 *     mapId: 'peta-lokasi', latId: 'latitude', lngId: 'longitude',
 *     statusId: 'gps-status', coordsId: 'gps-coords', gpsBtnId: 'btn-gps',
 *     geocodeUrl: 'ajax-geocode.php',
 *     getAlamat: function() { return ntbComposeAlamat(...); },
 *     initialLat: null, initialLng: null
 *   });
 *   // panggil tiap alamat berubah - otomatis diabaikan kalau sudah terkunci:
 *   peta.cariDariAlamat();
 *   // panggil dari tombol "cari ulang" - selalu jalan walau sudah terkunci:
 *   peta.cariUlangDariAlamat();
 */
function initPetaPin(cfg) {
  const mapEl = document.getElementById(cfg.mapId);
  const latEl = document.getElementById(cfg.latId);
  const lngEl = document.getElementById(cfg.lngId);
  const statusEl = cfg.statusId ? document.getElementById(cfg.statusId) : null;
  const coordsEl = cfg.coordsId ? document.getElementById(cfg.coordsId) : null;
  const gpsBtn = cfg.gpsBtnId ? document.getElementById(cfg.gpsBtnId) : null;
  if (!mapEl || !latEl || !lngEl || typeof L === 'undefined') return null;

  const NTB_CENTER = [-8.5833, 116.1167];
  const hasInitial = cfg.initialLat != null && cfg.initialLng != null
    && !isNaN(parseFloat(cfg.initialLat)) && !isNaN(parseFloat(cfg.initialLng));

  const startLatLng = hasInitial
    ? [parseFloat(cfg.initialLat), parseFloat(cfg.initialLng)]
    : NTB_CENTER;

  const map = L.map(mapEl, { scrollWheelZoom: false }).setView(startLatLng, hasInitial ? 16 : 9);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);

  const marker = L.marker(startLatLng, { draggable: true }).addTo(map);

  // Terkunci = titik sudah ditentukan (tersimpan/digeser manual/GPS) dan
  // TIDAK boleh lagi ditimpa otomatis oleh tebakan alamat berikutnya.
  let locked = hasInitial;

  function setStatus(msg, ok) {
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.classList.remove('text-red-600', 'text-green-700');
    statusEl.classList.add(ok ? 'text-green-700' : 'text-red-600');
  }

  function applyLatLng(lat, lng, msg, ok, lock) {
    lat = Number(lat); lng = Number(lng);
    latEl.value = lat;
    lngEl.value = lng;
    marker.setLatLng([lat, lng]);
    if (coordsEl) coordsEl.textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    if (msg) setStatus(msg, ok !== false);
    if (lock) locked = true;
  }

  if (hasInitial) {
    applyLatLng(startLatLng[0], startLatLng[1], 'Titik tersimpan - geser pin kalau kurang tepat', true, true);
  } else {
    // Isi default (pusat NTB) dulu biar field lat/lng gak pernah kosong;
    // ini BUKAN dianggap terkunci, jadi tebakan pertama dari alamat masih
    // boleh menggantikannya.
    applyLatLng(startLatLng[0], startLatLng[1], null, true, false);
  }

  // Geser manual / klik peta = sumber kebenaran, selalu mengunci titik
  marker.on('dragend', function () {
    const p = marker.getLatLng();
    applyLatLng(p.lat, p.lng, 'Titik dipilih manual di peta', true, true);
    map.panTo(p);
  });
  map.on('click', function (e) {
    applyLatLng(e.latlng.lat, e.latlng.lng, 'Titik dipilih manual di peta', true, true);
  });

  // Tebakan dari alamat yang diketik
  let geocodeTimer = null;
  let lastAlamat = '';
  function doGeocode(alamat, isManual) {
    setStatus('Mencari titik dari alamat...', true);
    fetch(cfg.geocodeUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'alamat=' + encodeURIComponent(alamat)
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        // Selagi request ini nunggu jawaban server, bisa aja agen udah
        // sempat geser pin manual (jadi locked=true). Kalau ini request
        // OTOMATIS (bukan klik "cari ulang"), jangan timpa - manual menang.
        if (!isManual && locked) return;
        if (data && data.ok) {
          // Tebakan (baik otomatis sebelum terkunci, maupun "cari ulang"
          // manual) mengisi pin tapi TIDAK mengunci - hasil tebakan tetap
          // perlu dicek/ditepatkan agen dengan menggeser pin.
          if (data.precise) {
            applyLatLng(data.lat, data.lng, 'Titik dari alamat (cocok sampai desa) - geser pin kalau masih kurang tepat', true, false);
            map.setView([data.lat, data.lng], 17);
          } else {
            // Data peta OSM di banyak desa NTB belum lengkap, jadi Nominatim
            // cuma nemu sampai level kecamatan/kabupaten - titik ini BUKAN
            // alamat persis, wajib digeser manual. Zoom dibikin lebih lebar
            // biar agen lihat konteks sekitarnya buat cari lokasi yang benar.
            applyLatLng(data.lat, data.lng, '\u26A0 Titik ini baru perkiraan wilayah kecamatan, BELUM tentu tepat - geser pin ke lokasi yang benar', false, false);
            map.setView([data.lat, data.lng], 13);
          }
        } else {
          setStatus((data && data.message) || 'Alamat belum ketemu, geser pin manual', false);
        }
      })
      .catch(function () {
        if (!isManual && locked) return;
        setStatus('Gagal mencari titik dari alamat, geser pin manual', false);
      });
  }
  function cariDariAlamat() {
    // Dipanggil otomatis tiap alamat berubah - diabaikan kalau titik sudah
    // terkunci (tersimpan/digeser manual/GPS), supaya gak ketiban timpa
    // titik yang sudah ditepatkan agen.
    if (locked) return;
    if (typeof cfg.getAlamat !== 'function') return;
    const alamat = (cfg.getAlamat() || '').trim();
    if (!alamat || alamat === lastAlamat) return;
    lastAlamat = alamat;
    if (geocodeTimer) clearTimeout(geocodeTimer);
    geocodeTimer = setTimeout(function () {
      // Dicek ULANG di sini (bukan cuma pas dijadwalkan) - soalnya bisa aja
      // agen sempat geser pin manual selama 700ms nunggu ini jalan.
      if (locked) return;
      doGeocode(alamat, false);
    }, 700);
  }
  function cariUlangDariAlamat() {
    // Dipanggil manual (tombol "cari ulang dari alamat") - selalu jalan
    // walau sudah terkunci, karena ini permintaan eksplisit dari agen.
    if (typeof cfg.getAlamat !== 'function') return;
    const alamat = (cfg.getAlamat() || '').trim();
    if (!alamat) { setStatus('Isi alamat dulu di atas', false); return; }
    lastAlamat = alamat;
    doGeocode(alamat, true);
  }

  // GPS HP - opsional, cuma mindahin pin ke lokasi HP saat ini & mengunci
  if (gpsBtn) {
    gpsBtn.addEventListener('click', function () {
      if (!navigator.geolocation) { setStatus('Browser tidak mendukung GPS', false); return; }
      setStatus('Mengambil lokasi HP...', true);
      navigator.geolocation.getCurrentPosition(
        function (pos) {
          applyLatLng(pos.coords.latitude, pos.coords.longitude,
            'Dari GPS HP (±' + Math.round(pos.coords.accuracy) + ' m) - geser pin kalau kurang tepat', true, true);
          map.setView([pos.coords.latitude, pos.coords.longitude], 16);
        },
        function (err) { setStatus('Gagal ambil GPS: ' + (err.message || 'izin ditolak'), false); },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
      );
    });
  }

  // PENTING: Leaflet ngitung posisi klik/drag berdasarkan UKURAN kontainer
  // peta SAAT peta dibuat. Kalau ukurannya berubah sesudahnya (mis. CSS
  // Tailwind yang dimuat dari CDN baru selesai belakangan, font baru
  // kebaca, sidebar collapse, dll), titik yang keliatan "pas" di layar bisa
  // tersimpan MELESET dari titik aslinya - ini penyebab paling umum "sudah
  // digeser manual tapi tetap meleset", BUKAN soal geocoding gratis/
  // berbayar (Nominatim gratis/berbayar sama sekali gak berpengaruh ke
  // pin yang digeser manual, itu murni interaksi peta, bukan geocoding).
  // Makanya di sini invalidateSize() dipanggil berulang & di-observe terus
  // biar peta selalu tahu ukuran kontainernya yang sebenarnya.
  setTimeout(function () { map.invalidateSize(); }, 200);
  setTimeout(function () { map.invalidateSize(); }, 800);
  window.addEventListener('load', function () { map.invalidateSize(); });
  if (typeof ResizeObserver !== 'undefined') {
    new ResizeObserver(function () { map.invalidateSize(); }).observe(mapEl);
  }

  return {
    map: map,
    marker: marker,
    cariDariAlamat: cariDariAlamat,
    cariUlangDariAlamat: cariUlangDariAlamat,
    applyLatLng: applyLatLng
  };
}