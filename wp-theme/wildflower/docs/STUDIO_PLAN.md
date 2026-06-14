# Wildflower — Theming Engine & Site Rebuild — MASTER PLAN

> Living document. Updated as work proceeds so context survives compaction.
> Branch: `claude/website-redesign-bugs-doj090`. Work in `wp-theme/wildflower/`.

## North star
A professional, **data-driven theming engine** where a *theme* is a complete
"look" object (NOT just one colour), plus an installable owner remote ("pult")
to switch/publish looks, plus a **rebuilt, editorial homepage** (new rhythm) with
**video-capable hero**. Must scale to *many* themes with zero copy-paste.

## Hard requirements (from the user — do not drop any)
1. [DONE] **Two accents per theme**, both change. No hardcoded copper anywhere.
2. [DONE] **Headings/italics** must use a per-theme accent, not fixed copper.
3. [DONE] **Font pairs** are part of each theme (and swappable). Multiple pairs.
4. [DONE] **Single source of truth**: themes defined once (PHP), CSS generated
   from it; remote built from the same source. Adding a theme = 1 array entry.
5. [DONE] **Scale to many themes** — engine + remote handle N themes.
6. [DONE] **Button system**: configurable corner radius (sharp ↔ slightly soft,
   NEVER pill/very-round), plus animations (magnetic stick, sheen, pulse CTA).
7. [DONE] **Hero supports video** (mp4/webm) as well as image, with poster +
   graceful fallback.
8. [TODO] **Rebuild the actual site** — hero, sections, bento, products,
   storytelling, add-ons — per the editorial composition we discussed:
   - full-width lifestyle hero (img OR video)
   - asymmetric bento "shop by occasion"
   - 3 LARGE products (not 6 small)
   - dark storytelling block (local florists)
   - "complete the gift" add-ons
   - delivery zones + cutoff, subscriptions, reviews, gallery, CTA
9. Quality bar: professional, no crutches. Single-hue "expensive" gradients +
   grain on dark surfaces. Accents solid & crisp.

## Architecture
- `inc/theme-switcher.php` = **engine**:
  - `wildflower_themes()` — full theme objects (colors ×N, font pair id, radius, etc.)
  - `wildflower_font_pairs()` — registry of heading+body pairs + Google Fonts URL.
  - `wildflower_generate_theme_css()` — emits `[data-theme]` var blocks for ALL
    themes (via `wp_add_inline_style`). Single source of truth.
  - `wildflower_enqueue_theme_fonts()` — loads only the ACTIVE theme's font pair.
  - data-theme on <html> from saved option (server-side, no flash); admins
    preview via `?wf_preview=`.
  - REST `wildflower/v1/theme` (GET palettes+state / POST publish, admin only).
- `style.css` = semantic tokens only (`--primary`, `--accent`, `--accent-2`,
  `--radius-btn`, `--font-serif/-sans`, gradient, grain). No literal theme values.
- `template-studio.php` + `assets/js|css/studio` + `assets/studio/*` = the remote
  (PWA). Cards built from the engine; show both accent swatches + font name.
- Hero video: customizer settings + front-page markup.
- Button anim: `assets/js/main.js` (magnetic) + CSS (sheen/pulse/radius).

## Phases & status
- [DONE] P0 Foundation v1 (committed 1b70d00): swappable tokens, 4 themes,
  gradient+grain, server option, REST, studio PWA. (Superseded/expanded below.)
- [DONE] P1 Engine rewrite: data-driven themes w/ 2 accents + font pair + radius.
- [DONE] P2 De-hardcode copper across style.css → semantic accent tokens.
- [DONE] P3 Typography pairs + dynamic font loading.
- [DONE] P4 Button radius token + animations (magnetic/sheen/pulse).
- [DONE] P5 Expand theme set to 8 curated looks.
- [DONE] P6 Remote upgrade: dual-swatch cards, font label, scales to N.
- [DONE] P7 Hero video support (img/video/fallback) + customizer.
- [WIP] P8 Homepage composition rebuild. Locked section order:
  1 announce(accent) · 2 header · 3 hero(img/video) · 4 trust strip ·
  5 occasions bento(asymmetric) · 6 bestsellers(3 big) · 7 add-ons "complete
  the gift" NEW · 8 subscription · 9 how it works(dark) · 10 our story NEW ·
  11 delivery zones NEW · 12 reviews · 13 gallery · 14 final CTA · 15 footer.
  Screens: shop, product, cart/checkout, occasions, subscriptions, delivery,
  gallery, journal, about, contact, 404, search, studio remote.
- [TODO] P9 Per-axis remote controls (font pair / radius independent of palette) — stretch.
- [TODO] P10 Seasonal / time-of-day auto themes — stretch.

## Notes / decisions
- Cannot run live WordPress in sandbox (no MySQL/Woo, restricted net). Validate
  via `php -l` + static token renders; user verifies in real WP.
- Studio page must live at `/studio/` (manifest start_url). Configurable later.
