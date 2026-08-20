/* ============================================================
   GAS-PAMAN — FUTURISTIC UI INTERACTIONS
   - Scroll reveal untuk elemen [data-fx-reveal]
   - Animated counter untuk elemen [data-fx-count]
   ============================================================ */
(function () {
  'use strict';

  // Penanda bahwa JS aktif — reveal CSS hanya menyembunyikan elemen
  // saat class ini ada, jadi konten tidak pernah hilang jika JS gagal.
  document.documentElement.classList.add('fx-js');

  /* ---------- Scroll reveal ---------- */
  function initReveal() {
    var items = document.querySelectorAll('[data-fx-reveal]');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('fx-visible'); });
      return;
    }

    // threshold 0 (bukan 0.12): threshold berbasis RASIO tinggi elemen, jadi
    // elemen yang lebih tinggi dari ~8x viewport (mis. kartu preview import
    // berisi puluhan soal) tak akan pernah menyentuh 12% -> konten tertahan
    // opacity:0 selamanya. Dengan 0, callback menyala begitu 1px terlihat.
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('fx-visible');
          animateBars(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0, rootMargin: '0px 0px -30px 0px' });

    items.forEach(function (el, i) {
      el.style.transitionDelay = Math.min(i * 70, 420) + 'ms';
      observer.observe(el);
    });

    // Jaring pengaman: apa pun keputusan observer, elemen yang sudah berada
    // di viewport saat load WAJIB tampil — konten tidak boleh hilang diam-diam.
    window.addEventListener('load', function () {
      document.querySelectorAll('[data-fx-reveal]:not(.fx-visible)').forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0) {
          el.classList.add('fx-visible');
          animateBars(el);
        }
      });
    });
  }

  /* ---------- Progress bar statistik ---------- */
  function animateBars(root) {
    var bars = (root || document).querySelectorAll('.fx-bar');
    if (!bars.length) return;
    Array.prototype.forEach.call(bars, function (bar) {
      var fill = bar.querySelector('i');
      if (!fill || fill.dataset.done) return;
      fill.dataset.done = '1';
      var w = bar.getAttribute('data-fx-w');
      // Mulai dari 0 lalu ke target, supaya transisi CSS berjalan
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          fill.style.width = w || '60%';
        });
      });
    });
  }

  /* ---------- Animated counter ---------- */
  function animateCount(el) {
    var target = parseFloat(el.getAttribute('data-fx-count'));
    if (isNaN(target)) return;
    var suffix = el.getAttribute('data-fx-suffix') || '';
    var duration = 1100;
    var start = null;

    function easeOutCubic(t) {
      return 1 - Math.pow(1 - t, 3);
    }

    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var value = Math.round(target * easeOutCubic(progress));
      el.textContent = value.toLocaleString('id-ID') + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  function initCounters() {
    var counters = document.querySelectorAll('[data-fx-count]');
    if (!counters.length) return;

    if (!('IntersectionObserver' in window)) {
      counters.forEach(animateCount);
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    counters.forEach(function (el) { observer.observe(el); });
  }

  function init() {
    initReveal();
    initCounters();
    // Bar di luar elemen reveal langsung dianimasikan (bar terlihat di viewport)
    animateBars(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
