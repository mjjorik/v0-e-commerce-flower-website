/**
 * Wildflower theme interactions + motion.
 * Vanilla JS + GSAP (loaded from CDN). No build step.
 */
(function () {
  'use strict';

  /* ---- Announcement bar ---- */
  var announce = document.querySelector('[data-announce]');
  var announceClose = document.querySelector('[data-announce-close]');
  if (announce && announceClose) {
    announceClose.addEventListener('click', function () { announce.remove(); });
  }

  /* ---- Sticky header scrolled state ---- */
  var header = document.querySelector('[data-site-header]');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 24); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---- Mobile menu ---- */
  var mobileNav = document.querySelector('[data-mobile-nav]');
  var openBtn = document.querySelector('[data-menu-open]');
  var closeBtns = document.querySelectorAll('[data-menu-close]');
  function setMenu(open) {
    if (!mobileNav) return;
    mobileNav.classList.toggle('is-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }
  if (openBtn) openBtn.addEventListener('click', function () { setMenu(true); });
  closeBtns.forEach(function (b) { b.addEventListener('click', function () { setMenu(false); }); });
  if (mobileNav) {
    mobileNav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setMenu(false); });
    });
  }

  /* ---- Reveal on scroll (.reveal / .kinetic / .tile) ---- */
  var revealEls = document.querySelectorAll('.reveal, .kinetic, .tile');
  if (!('IntersectionObserver' in window)) {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var delay = parseFloat(el.getAttribute('data-delay') || '0');
          setTimeout(function () { el.classList.add('is-visible'); }, delay);
          io.unobserve(el);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -6% 0px' });

    revealEls.forEach(function (el) {
      if (el.classList.contains('kinetic')) {
        el.querySelectorAll('.word > span').forEach(function (span, i) {
          span.style.transitionDelay = (i * 0.05) + 's';
        });
      }
      io.observe(el);
    });
  }

  /* ---- Hero image GSAP reveal ---- */
  if (window.gsap) {
    var heroMedia = document.querySelector('[data-hero-media]');
    if (heroMedia) {
      window.gsap.fromTo(
        heroMedia,
        { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.08, y: 48 },
        { clipPath: 'inset(0% 0% 0% 0%)', scale: 1, y: 0, duration: 1.6, ease: 'expo.inOut', delay: 0.15 }
      );
    }
  }

  /* ---- Parallax on scroll (depth on media + drifting glows) ---- */
  if (window.gsap && window.ScrollTrigger && window.matchMedia('(min-width: 768px)').matches) {
    window.gsap.registerPlugin(window.ScrollTrigger);

    // Media layers drift at alternating speeds for depth (gallery + occasions).
    document.querySelectorAll('.gallery-grid .tile .media-fallback, .bento__tile .media-fallback').forEach(function (el, i) {
      var dir = (i % 2 === 0) ? 1 : -1;
      window.gsap.fromTo(el,
        { yPercent: -7 * dir },
        {
          yPercent: 7 * dir, ease: 'none',
          scrollTrigger: { trigger: el.closest('.tile, .bento__tile') || el, start: 'top bottom', end: 'bottom top', scrub: 0.5 },
        }
      );
    });

    // Decorative blocks (glows) drift on scroll.
    document.querySelectorAll('[data-parallax]').forEach(function (el) {
      var amt = parseFloat(el.getAttribute('data-parallax')) || 60;
      window.gsap.to(el, {
        y: amt, ease: 'none',
        scrollTrigger: { trigger: el.parentElement || el, start: 'top bottom', end: 'bottom top', scrub: 0.5 },
      });
    });
  }

  /* ---- Gallery lightbox (click to open, arrows / keys / swipe) ---- */
  var tiles = Array.prototype.slice.call(document.querySelectorAll('.gallery-grid .tile'));
  if (tiles.length) {
    var lb = document.createElement('div');
    lb.className = 'lightbox';
    lb.setAttribute('role', 'dialog');
    lb.setAttribute('aria-modal', 'true');
    lb.innerHTML =
      '<button class="lightbox__close" aria-label="Close">' +
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
      '</button>' +
      '<button class="lightbox__btn lightbox__btn--prev" aria-label="Previous">' +
        '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>' +
      '</button>' +
      '<div class="lightbox__stage"></div>' +
      '<button class="lightbox__btn lightbox__btn--next" aria-label="Next">' +
        '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>' +
      '</button>' +
      '<span class="lightbox__count"></span>';
    document.body.appendChild(lb);

    var stage = lb.querySelector('.lightbox__stage');
    var count = lb.querySelector('.lightbox__count');
    var idx = 0;

    function render() {
      stage.innerHTML = tiles[idx].innerHTML; // clones the gradient (or <img> once real photos exist)
      count.textContent = (idx + 1) + ' / ' + tiles.length;
    }
    function open(i) {
      idx = i; render();
      lb.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
    function close() {
      lb.classList.remove('is-open');
      document.body.style.overflow = '';
    }
    function go(d) { idx = (idx + d + tiles.length) % tiles.length; render(); }

    tiles.forEach(function (t, i) {
      t.addEventListener('click', function () { open(i); });
    });
    lb.querySelector('.lightbox__close').addEventListener('click', close);
    lb.querySelector('.lightbox__btn--prev').addEventListener('click', function (e) { e.stopPropagation(); go(-1); });
    lb.querySelector('.lightbox__btn--next').addEventListener('click', function (e) { e.stopPropagation(); go(1); });
    lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape') close();
      else if (e.key === 'ArrowLeft') go(-1);
      else if (e.key === 'ArrowRight') go(1);
    });

    // Touch swipe.
    var x0 = null;
    lb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1);
      x0 = null;
    });
  }
})();
