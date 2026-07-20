/**
 * Wildflower theme interactions + motion.
 * Vanilla JS + GSAP (loaded from CDN). No build step.
 */
(function () {
  'use strict';

  /* ---- Enable theme cross-fade after first paint (avoids flash on load) ---- */
  window.requestAnimationFrame(function () {
    document.documentElement.classList.add('theme-ready');
  });

  /* ---- Magnetic buttons (subtle pull toward the cursor) ---- */
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches &&
      !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.btn--magnetic').forEach(function (btn) {
      var strength = parseFloat(btn.getAttribute('data-magnetic')) || 0.3;
      btn.addEventListener('pointermove', function (e) {
        var r = btn.getBoundingClientRect();
        btn.style.setProperty('--mx', ((e.clientX - (r.left + r.width / 2)) * strength).toFixed(1) + 'px');
        btn.style.setProperty('--my', ((e.clientY - (r.top + r.height / 2)) * strength).toFixed(1) + 'px');
      });
      btn.addEventListener('pointerleave', function () {
        btn.style.setProperty('--mx', '0px');
        btn.style.setProperty('--my', '0px');
      });
    });
  }

  /* ---- Hero video: pause when off-screen to save battery/CPU ---- */
  var heroVideo = document.querySelector('[data-hero-video]');
  if (heroVideo && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { heroVideo.play().catch(function () {}); }
        else { heroVideo.pause(); }
      });
    }, { threshold: 0.1 }).observe(heroVideo);
  }

  /* ---- Count-up numbers when scrolled into view (stats) ---- */
  (function () {
    var nums = document.querySelectorAll('.story__stats strong, .page-hero__facts strong, .gift-terms strong, .deliv-rule__inner .gift-terms strong');
    if (!nums.length || !('IntersectionObserver' in window)) return;
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function run(el) {
      var t = el.textContent.trim();
      var m = t.match(/^(\$?)(\d+(?:\.\d+)?)(.*)$/);
      if (!m || reduce) return;
      var pre = m[1], target = parseFloat(m[2]), suf = m[3], dec = (m[2].split('.')[1] || '').length, dur = 1100, start = null;
      function step(ts) {
        if (!start) start = ts;
        var p = Math.min(1, (ts - start) / dur);
        var v = target * (1 - Math.pow(1 - p, 3));
        el.textContent = pre + v.toFixed(dec) + suf;
        if (p < 1) requestAnimationFrame(step); else el.textContent = pre + target.toFixed(dec) + suf;
      }
      requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (en) { if (en.isIntersecting) { run(en.target); io.unobserve(en.target); } });
    }, { threshold: 0.4 });
    nums.forEach(function (n) { io.observe(n); });
  })();

  /* ---- Hero headline: rotate the accent word ----
     Runs regardless of the OS "reduce motion" setting: this is a small,
     non-vestibular text swap the brand wants on every device. */
  {
    document.querySelectorAll('[data-rotate]').forEach(function (box) {
      var words = box.querySelectorAll('.hero__rotate-word');
      if (words.length < 2) return;
      var i = 0;
      setInterval(function () {
        words[i].classList.remove('is-active');
        words[i].classList.add('is-out');
        var prev = i;
        i = (i + 1) % words.length;
        words[i].classList.remove('is-out');
        words[i].classList.add('is-active');
        setTimeout(function () { words[prev].classList.remove('is-out'); }, 650);
      }, 2600);
    });
  }

  /* ---- Scroll-story: zoom the video from a card to full-bleed on scroll ---- */
  var vstory = document.querySelector('[data-vstory]');
  if (vstory) {
    var vmedia = vstory.querySelector('[data-vstory-media]');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (vmedia && window.gsap && window.ScrollTrigger && !reduce) {
      window.gsap.registerPlugin(window.ScrollTrigger);
      window.gsap.to(vmedia, {
        scale: 1, borderRadius: 0, ease: 'none',
        scrollTrigger: { trigger: vstory, start: 'top top', end: 'bottom bottom', scrub: 0.5 },
      });
      var vov = vstory.querySelector('[data-vstory-overlay]');
      if (vov) {
        window.gsap.from(vov, {
          autoAlpha: 0, y: 40, ease: 'none',
          scrollTrigger: { trigger: vstory, start: 'top top', end: '40% top', scrub: 0.5 },
        });
      }
    } else if (vmedia) {
      vmedia.style.transform = 'none';
      vmedia.style.borderRadius = '0';
    }
  }

  /* ---- Shop by occasion: a calm, visible-only rotating preview ---- */
  document.querySelectorAll('[data-occasions]').forEach(function (root) {
    var items = root.querySelectorAll('[data-occ]');
    var medias = root.querySelectorAll('[data-occ-media]');
    var list = root.querySelector('.occasions__list');
    var current = 0;
    var timer = null;
    var rootVisible = false;
    // Auto-advance on every device — the brand wants this picker moving on
    // mobile too, so we do not gate it behind the OS "reduce motion" flag.
    var reducedMotion = false;

    function activate(index) {
      if (index < 0 || index >= items.length) return;
      current = index;
      items.forEach(function (it, itemIndex) { it.classList.toggle('is-active', itemIndex === index); });
      medias.forEach(function (m, mediaIndex) { m.classList.toggle('is-active', mediaIndex === index); });
    }

    function stopAutoplay() {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    }

    function startAutoplay() {
      if (reducedMotion || !rootVisible || timer || items.length < 2) return;
      timer = window.setInterval(function () {
        activate((current + 1) % items.length);
      }, 3600);
    }

    items.forEach(function (it) {
      var index = Number(it.dataset.occ);
      it.addEventListener('mouseenter', function () { stopAutoplay(); activate(index); });
      it.addEventListener('focus', function () { stopAutoplay(); activate(index); });
    });

    root.addEventListener('mouseleave', startAutoplay);
    root.addEventListener('focusout', function (event) {
      if (!root.contains(event.relatedTarget)) startAutoplay();
    });

    if ('IntersectionObserver' in window) {
      var sectionObserver = new IntersectionObserver(function (entries) {
        rootVisible = entries[0].isIntersecting;
        if (rootVisible) startAutoplay();
        else stopAutoplay();
      }, { threshold: 0.2 });
      sectionObserver.observe(root);
    } else {
      rootVisible = true;
      startAutoplay();
    }

    // Desktop lists can be scrolled independently. Keep the visible row and
    // the large preview in lockstep, then continue the gentle rotation.
    if (list) {
      var scrollResumeTimer = null;
      list.addEventListener('scroll', function () {
        var listRect = list.getBoundingClientRect();
        var listCentre = listRect.top + (listRect.height / 2);
        var nearestIndex = current;
        var nearestDistance = Infinity;
        items.forEach(function (it, index) {
          var rect = it.getBoundingClientRect();
          var distance = Math.abs((rect.top + (rect.height / 2)) - listCentre);
          if (distance < nearestDistance) {
            nearestDistance = distance;
            nearestIndex = index;
          }
        });
        stopAutoplay();
        activate(nearestIndex);
        window.clearTimeout(scrollResumeTimer);
        scrollResumeTimer = window.setTimeout(startAutoplay, 900);
      }, { passive: true });
    }

    // On touch devices the section simply auto-advances on its own (like the
    // WeFixit Electronics picker) — the row highlights each occasion in turn and
    // the large preview swaps to match. We intentionally do NOT drive the active
    // item from finger swipes / page scroll, which felt unpredictable.
  });

  /* ---- Shop filters drawer ---- */
  var filters = document.querySelector('[data-filters]');
  if (filters) {
    var openF = document.querySelector('[data-filters-open]');
    var closers = filters.querySelectorAll('[data-filters-close]');
    function setFilters(open) {
      if (open) { filters.hidden = false; requestAnimationFrame(function () { filters.classList.add('is-open'); }); }
      else { filters.classList.remove('is-open'); setTimeout(function () { filters.hidden = true; }, 400); }
      document.body.style.overflow = open ? 'hidden' : '';
    }
    if (openF) openF.addEventListener('click', function () { setFilters(true); });
    closers.forEach(function (c) { c.addEventListener('click', function () { setFilters(false); }); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !filters.hidden) setFilters(false); });

    filters.querySelectorAll('[data-filter-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var group = btn.closest('[data-filter-group]');
        var open = group.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    });
  }

  /* ---- Site header (declared early; used by search toggle + scrolled state) ---- */
  var header = document.querySelector('[data-site-header]');

  /* ---- Header search toggle ---- */
  var searchToggle = document.querySelector('[data-search-toggle]');
  var searchInput = document.querySelector('[data-search-input]');
  if (searchToggle && header) {
    searchToggle.addEventListener('click', function () {
      var open = header.classList.toggle('search-open');
      searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && searchInput) { setTimeout(function () { searchInput.focus(); }, 120); }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && header.classList.contains('search-open')) {
        header.classList.remove('search-open');
        searchToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  /* ---- Mobile sticky action bar — reveal after the hero ---- */
  var mbar = document.querySelector('[data-mobile-bar]');
  if (mbar) {
    var heroEl = document.querySelector('.hero');
    var onBar = function () {
      var trigger = heroEl ? heroEl.offsetHeight * 0.8 : 480;
      mbar.classList.toggle('is-visible', window.scrollY > trigger);
    };
    onBar();
    window.addEventListener('scroll', onBar, { passive: true });
  }

  /* ---- Horizontal scrollers (bestsellers) ---- */
  document.querySelectorAll('[data-scroller]').forEach(function (sc) {
    var track = sc.querySelector('[data-scroller-track]');
    var prev = sc.querySelector('[data-scroll-prev]');
    var next = sc.querySelector('[data-scroll-next]');
    if (!track) return;

    function step() {
      var card = track.querySelector('.product, .addon, li');
      return card ? card.getBoundingClientRect().width + 24 : track.clientWidth * 0.8;
    }
    function update() {
      var maxScroll = track.scrollWidth - track.clientWidth - 2;
      var atStart = track.scrollLeft <= 2;
      var atEnd = track.scrollLeft >= maxScroll;
      sc.classList.toggle('is-end', atEnd);
      if (prev) prev.disabled = atStart;
      if (next) next.disabled = atEnd;
    }
    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); });
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    update();
  });

  /* ---- Per-card photo slider (each product card flips through its own photos) ---- */
  document.querySelectorAll('[data-card-slider]').forEach(function (root) {
    var track = root.querySelector('[data-card-slider-track]');
    if (!track) return;
    var prev = root.querySelector('[data-card-prev]');
    var next = root.querySelector('[data-card-next]');
    var dots = root.querySelectorAll('.card-slider__dot');

    function slideWidth() {
      var s = track.querySelector('.card-slider__slide');
      return s ? s.getBoundingClientRect().width : track.clientWidth;
    }
    function update() {
      var w = slideWidth() || 1;
      var idx = Math.round(track.scrollLeft / w);
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
      if (prev) prev.disabled = track.scrollLeft <= 2;
      if (next) next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
    }
    function go(dir, e) {
      // Don't let the arrow click bubble to the card's "View bouquet" link.
      if (e) { e.preventDefault(); e.stopPropagation(); }
      var w = slideWidth() || 1;
      var idx = Math.round(track.scrollLeft / w) + dir;
      idx = Math.max(0, Math.min(idx, track.querySelectorAll('.card-slider__slide').length - 1));
      track.scrollTo({ left: idx * w, behavior: 'smooth' });
    }
    if (prev) prev.addEventListener('click', function (e) { go(-1, e); });
    if (next) next.addEventListener('click', function (e) { go(1, e); });
    // Settle: after a swipe/scroll stops, snap to the exact nearest slide so a
    // photo never rests half-shown (no sliver of the next image peeking).
    var settleTimer = null;
    function settle() {
      var w = slideWidth() || 1;
      var idx = Math.round(track.scrollLeft / w);
      var target = idx * w;
      if (Math.abs(track.scrollLeft - target) > 1) {
        track.scrollTo({ left: target, behavior: 'smooth' });
      }
    }
    var ticking = false;
    track.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(function () { ticking = false; update(); }); }
      if (settleTimer) { window.clearTimeout(settleTimer); }
      settleTimer = window.setTimeout(settle, 140);
    }, { passive: true });
    window.addEventListener('resize', function () { update(); settle(); });
    update();
  });

  /* ---- Custom order request form → pre-filled email (no plugin needed) ---- */
  var corderForm = document.querySelector('[data-custom-order-form]');
  if (corderForm) {
    corderForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var to = corderForm.getAttribute('data-studio-email') || '';
      var get = function (n) { var el = corderForm.querySelector('[name="' + n + '"]'); return el ? String(el.value || '').trim() : ''; };
      var name = get('name');
      var rows = [
        ['Name', name], ['Email', get('email')], ['Phone', get('phone')],
        ['Occasion', get('occasion')], ['Date needed', get('date')], ['Budget', get('budget')],
        ['Colors / palette', get('palette')], ['Delivery city / ZIP', get('location')]
      ];
      var body = 'Hi Wildflower,\n\nI’d like to request a custom order.\n\n';
      rows.forEach(function (r) { if (r[1]) { body += r[0] + ': ' + r[1] + '\n'; } });
      var details = get('details');
      if (details) { body += '\nMy vision:\n' + details + '\n'; }
      body += '\nThank you!' + (name ? '\n' + name : '');
      var subject = 'Custom order request' + (name ? ' — ' + name : '');
      var href = 'mailto:' + encodeURIComponent(to) +
        '?subject=' + encodeURIComponent(subject) +
        '&body=' + encodeURIComponent(body);
      var ok = corderForm.querySelector('[data-custom-order-ok]');
      if (ok) { ok.hidden = false; }
      window.location.href = href;
    });
  }

  /* ---- Announcement bar ---- */
  var announce = document.querySelector('[data-announce]');
  var announceClose = document.querySelector('[data-announce-close]');
  if (announce && announceClose) {
    announceClose.addEventListener('click', function () { announce.remove(); });
  }

  /* ---- Sticky header scrolled state ---- */
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

  /* ---- Parallax on scroll (depth on media + drifting glows) ----
     Runs on all devices (incl. when the OS "reduce motion" flag is on) — the
     brand wants the gallery/media parallax visible on mobile too. */
  if (window.gsap && window.ScrollTrigger) {
    window.gsap.registerPlugin(window.ScrollTrigger);
    // iOS/Android: don't recalc (or freeze scrubbed tweens) when the browser
    // chrome shows/hides and resizes the viewport — keeps motion alive on mobile.
    window.ScrollTrigger.config({ ignoreMobileResize: true });

    // Lighter travel on small screens so it reads as depth, not jitter — but
    // still clearly visible (the media has 11% headroom to drift within).
    var pScale = window.matchMedia('(min-width: 768px)').matches ? 1 : 0.7;

    // Media layers drift at alternating speeds for depth (gallery, occasions,
    // and journal article-preview cards — same parallax as the gallery tiles).
    document.querySelectorAll('.gallery-grid .tile .media-fallback, .gallery-grid .tile img, .bento__tile .media-fallback, .bento__tile img, .post-card__media img, .post-card__media .media-fallback, .journal-feature__media img, .journal-feature__media .media-fallback').forEach(function (el, i) {
      var dir = (i % 2 === 0) ? 1 : -1;
      window.gsap.fromTo(el,
        { yPercent: -7 * dir * pScale },
        {
          yPercent: 7 * dir * pScale, ease: 'none',
          scrollTrigger: { trigger: el.closest('.tile, .bento__tile, .post-card__media, .journal-feature__media') || el, start: 'top bottom', end: 'bottom top', scrub: 0.5 },
        }
      );
    });

    // Decorative blocks (glows) drift on scroll.
    document.querySelectorAll('[data-parallax]').forEach(function (el) {
      var amt = (parseFloat(el.getAttribute('data-parallax')) || 60) * pScale;
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

  /* ---- Reading-progress bar (article pages) ---- */
  (function () {
    var bar = document.querySelector('[data-read-progress] span');
    var article = document.querySelector('.article');
    if (!bar || !article) return;
    var ticking = false;
    function update() {
      ticking = false;
      var rect = article.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      // Total scrollable distance through the article within the viewport.
      var total = rect.height - vh;
      var scrolled = total > 0 ? Math.min(1, Math.max(0, -rect.top / total)) : (rect.top <= 0 ? 1 : 0);
      bar.style.width = (scrolled * 100).toFixed(2) + '%';
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
    }, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();
  })();
})();
