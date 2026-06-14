/* ============================================================
   Wildflower Studio — the site remote
   Builds preset buttons from the curated palettes, previews a
   chosen theme live in the iframe, and publishes it to all
   visitors via the REST API.
   ============================================================ */
(function () {
  'use strict';

  var cfg = window.WF_STUDIO;
  if (!cfg) return;

  var root      = document.querySelector('[data-studio]');
  var presetsEl = root.querySelector('[data-presets]');
  var frame     = root.querySelector('[data-frame]');
  var frameUrl  = root.querySelector('[data-frame-url]');
  var frameWrap = root.querySelector('[data-frame-wrap]');
  var deviceBtn = root.querySelector('[data-device]');
  var publishBtn= root.querySelector('[data-publish]');
  var statusEl  = root.querySelector('[data-status]');
  var statusTxt = root.querySelector('[data-status-text]');
  var hintEl    = root.querySelector('[data-hint]');

  var published = cfg.published;   // what visitors currently get
  var selected  = cfg.published;   // what the remote is previewing

  // ── Build the preview URL for a theme (admin-only override param). ──
  function previewUrl(theme) {
    var sep = cfg.previewBase.indexOf('?') === -1 ? '?' : '&';
    return cfg.previewBase + sep + 'wf_preview=' + encodeURIComponent(theme);
  }

  // ── Render preset cards from the palettes map. ──
  function renderPresets() {
    presetsEl.innerHTML = '';
    Object.keys(cfg.palettes).forEach(function (id) {
      var p = cfg.palettes[id];
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'preset';
      btn.dataset.theme = id;
      btn.style.setProperty('--dot', p.accent);
      btn.innerHTML =
        '<span class="preset__swatch" style="background:' + p.primary + ';--dot:' + p.accent + '"></span>' +
        '<span class="preset__text"><span class="preset__name">' + p.label + '</span>' +
        '<span class="preset__desc">' + p.desc + '</span></span>' +
        '<span class="preset__live">Live</span>';
      btn.addEventListener('click', function () { select(id); });
      presetsEl.appendChild(btn);
    });
  }

  // ── Preview a theme (does not publish). ──
  function select(theme) {
    selected = theme;
    frame.src = previewUrl(theme);
    frameUrl.textContent = previewUrl(theme).replace(/^https?:\/\//, '');
    syncUI();
  }

  // ── Reflect state in the UI. ──
  function syncUI() {
    var cards = presetsEl.querySelectorAll('.preset');
    cards.forEach(function (c) {
      c.classList.toggle('is-selected', c.dataset.theme === selected);
      c.classList.toggle('is-live', c.dataset.theme === published);
    });

    var dirty = selected !== published;
    statusEl.classList.toggle('is-dirty', dirty);
    statusEl.classList.toggle('is-live', !dirty);
    var sel = cfg.palettes[selected] ? cfg.palettes[selected].label : selected;
    var pub = cfg.palettes[published] ? cfg.palettes[published].label : published;
    statusTxt.textContent = dirty
      ? ('Previewing ' + sel + ' · live is ' + pub)
      : (pub + ' is live');
    publishBtn.disabled = !dirty;
    publishBtn.textContent = dirty ? ('Publish ' + sel + ' to live') : 'Published';
    hintEl.textContent = '';
  }

  // ── Publish the selected theme to every visitor. ──
  function publish() {
    if (selected === published) return;
    publishBtn.disabled = true;
    hintEl.textContent = 'Publishing…';
    fetch(cfg.restUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
      body: JSON.stringify({ theme: selected })
    })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
      .then(function (res) {
        if (!res.ok || !res.d || !res.d.ok) throw new Error('Save failed');
        published = res.d.theme;
        selected = published;
        syncUI();
        hintEl.textContent = 'Live for everyone ✓';
        setTimeout(function () { if (hintEl.textContent === 'Live for everyone ✓') hintEl.textContent = ''; }, 2600);
      })
      .catch(function () {
        publishBtn.disabled = false;
        hintEl.textContent = 'Could not publish — try again.';
      });
  }

  // ── Device toggle (desktop / mobile preview). ──
  var mobile = false;
  deviceBtn.addEventListener('click', function () {
    mobile = !mobile;
    frameWrap.classList.toggle('is-mobile', mobile);
    deviceBtn.textContent = mobile ? 'Desktop' : 'Mobile';
  });

  publishBtn.addEventListener('click', publish);

  // ── Boot ──
  renderPresets();
  select(published);
})();
