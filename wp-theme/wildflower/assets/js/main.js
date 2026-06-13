/**
 * Wildflower theme interactions + motion.
 * Vanilla JS + GSAP (loaded from CDN). No build step.
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
  if (reduceMotion || !('IntersectionObserver' in window)) {
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
    }, { threshold: 0.2, rootMargin: '0px 0px -8% 0px' });
    revealEls.forEach(function (el) {
      // Stagger words inside kinetic headings.
      if (el.classList.contains('kinetic')) {
        el.querySelectorAll('.word > span').forEach(function (span, i) {
          span.style.transitionDelay = (i * 0.05) + 's';
        });
      }
      io.observe(el);
    });
  }

  /* ---- Hero image GSAP reveal ---- */
  if (!reduceMotion && window.gsap) {
    var heroMedia = document.querySelector('[data-hero-media]');
    if (heroMedia) {
      window.gsap.fromTo(
        heroMedia,
        { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.08, y: 48 },
        { clipPath: 'inset(0% 0% 0% 0%)', scale: 1, y: 0, duration: 1.6, ease: 'expo.inOut', delay: 0.15 }
      );
    }
  }
})();
