/**
 * Combobox alamat NTB berjenjang: Kab/Kota -> Kecamatan -> Desa/Kelurahan
 * - Bisa pilih dari suggestion (datalist)
 * - Bisa ketik manual jika wilayah belum ada di data
 *
 * Usage: initNtbAlamat(elKab, elKec, elDesa, wilayahData, defaults?)
 * el* bisa berupa <input list="..."> atau <select> (legacy)
 */
function initNtbAlamat(elKab, elKec, elDesa, data, defaults) {
  defaults = defaults || {};
  if (!elKab || !elKec || !elDesa || !data) return;

  function isInput(el) {
    return el && el.tagName && el.tagName.toLowerCase() === 'input';
  }

  function getListEl(inputEl) {
    if (!inputEl || !inputEl.getAttribute) return null;
    var listId = inputEl.getAttribute('list');
    if (!listId) return null;
    return document.getElementById(listId);
  }

  function fillDatalist(listEl, items) {
    if (!listEl) return;
    listEl.innerHTML = '';
    (items || []).forEach(function (name) {
      var o = document.createElement('option');
      o.value = name;
      listEl.appendChild(o);
    });
  }

  function fillSelect(select, items, placeholder, selected) {
    select.innerHTML = '';
    var opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = placeholder;
    select.appendChild(opt0);
    (items || []).forEach(function (name) {
      var o = document.createElement('option');
      o.value = name;
      o.textContent = name;
      if (selected && selected === name) o.selected = true;
      select.appendChild(o);
    });
  }

  function fill(el, items, placeholder, selected) {
    if (isInput(el)) {
      fillDatalist(getListEl(el), items);
      if (selected) el.value = selected;
    } else {
      fillSelect(el, items, placeholder, selected);
    }
  }

  var kabs = Object.keys(data);
  fill(elKab, kabs, '-- Pilih Kabupaten/Kota --', defaults.kab || '');

  function resolveKabKey(raw) {
    if (!raw) return '';
    if (data[raw]) return raw;
    // case-insensitive match ke key data
    var lower = String(raw).toLowerCase().trim();
    for (var i = 0; i < kabs.length; i++) {
      if (kabs[i].toLowerCase() === lower) return kabs[i];
    }
    return raw; // nilai manual, cascade kosong
  }

  function onKab() {
    var kabRaw = elKab.value;
    var kab = resolveKabKey(kabRaw);
    var kecs = kab && data[kab] ? Object.keys(data[kab]) : [];
    fill(elKec, kecs, '-- Pilih Kecamatan --', defaults.kec || '');
    fill(elDesa, [], '-- Pilih Desa/Kelurahan --', '');
    // jangan paksa clear value jika user mengetik manual
    if (!defaults.kec && isInput(elKec) && !elKec.value) elKec.value = '';
    if (!defaults.desa && isInput(elDesa)) elDesa.value = '';
    defaults.kec = '';
    defaults.desa = '';
  }

  function onKec() {
    var kab = resolveKabKey(elKab.value);
    var kec = elKec.value;
    var desas = [];
    if (kab && data[kab]) {
      if (data[kab][kec]) {
        desas = data[kab][kec];
      } else {
        // case-insensitive match kecamatan
        var lower = String(kec).toLowerCase().trim();
        var kecKeys = Object.keys(data[kab]);
        for (var i = 0; i < kecKeys.length; i++) {
          if (kecKeys[i].toLowerCase() === lower) {
            desas = data[kab][kecKeys[i]];
            break;
          }
        }
      }
    }
    fill(elDesa, desas, '-- Pilih Desa/Kelurahan --', defaults.desa || '');
    defaults.desa = '';
  }

  // change + input: datalist fire change saat pilih; input saat ketik
  elKab.addEventListener('change', onKab);
  elKab.addEventListener('input', function () {
    // debounce ringan: isi suggestion kec saat user berhenti ketik sebentar
    clearTimeout(elKab._ntbT);
    elKab._ntbT = setTimeout(onKab, 180);
  });
  elKec.addEventListener('change', onKec);
  elKec.addEventListener('input', function () {
    clearTimeout(elKec._ntbT);
    elKec._ntbT = setTimeout(onKec, 180);
  });

  if (defaults.kab) {
    var initKec = defaults.kec || '';
    var initDesa = defaults.desa || '';
    onKab();
    if (initKec) {
      elKec.value = initKec;
      defaults.desa = initDesa;
      onKec();
      if (initDesa) elDesa.value = initDesa;
    }
  } else {
    fill(elKec, [], '-- Pilih Kecamatan --', '');
    fill(elDesa, [], '-- Pilih Desa/Kelurahan --', '');
  }
}

/** Bangun teks alamat dari dropdown (+ detail opsional) */
function ntbComposeAlamat(kab, kec, desa, detail) {
  var parts = [];
  if (detail && String(detail).trim()) parts.push(String(detail).trim());
  if (desa) parts.push('Desa/Kel. ' + desa);
  if (kec) parts.push('Kec. ' + kec);
  if (kab) parts.push(kab);
  parts.push('Nusa Tenggara Barat');
  return parts.join(', ');
}