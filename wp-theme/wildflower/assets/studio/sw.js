/* Wildflower Studio service worker.
   Minimal: enables installability and an app-shell cache for the remote's
   static assets. The theme data itself is always fetched fresh (network-first)
   so the remote never shows a stale "live" state. */
var CACHE = 'wf-studio-v1';
var SHELL = [
  '../css/studio.css',
  '../js/studio.js',
  './manifest.webmanifest',
  './icon-192.png',
  './icon-512.png'
];

self.addEventListener('install', function (e) {
  self.skipWaiting();
  e.waitUntil(caches.open(CACHE).then(function (c) { return c.addAll(SHELL).catch(function () {}); }));
});

self.addEventListener('activate', function (e) {
  e.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys.map(function (k) { return k === CACHE ? null : caches.delete(k); }));
    }).then(function () { return self.clients.claim(); })
  );
});

self.addEventListener('fetch', function (e) {
  var req = e.request;
  if (req.method !== 'GET') return;
  var url = new URL(req.url);

  // Never cache REST (theme state) or the live preview document.
  if (url.pathname.indexOf('/wp-json/') !== -1 || url.searchParams.has('wf_preview')) {
    return; // default: go to network
  }

  // Cache-first for our shell assets, network fallback otherwise.
  e.respondWith(
    caches.match(req).then(function (hit) {
      return hit || fetch(req).then(function (res) {
        return res;
      }).catch(function () { return hit; });
    })
  );
});
