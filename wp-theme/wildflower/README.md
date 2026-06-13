# Wildflower — WordPress theme (WooCommerce)

A classic PHP theme (no build step) that ports the Wildflower design system —
warm light palette, Fraunces + Inter, editorial animated buttons, GSAP motion,
and elegant botanical image fallbacks — to WordPress + WooCommerce.

## Quick preview (Docker, one command)

From the `wp-theme/` folder (the one with `docker-compose.yml`):

```bash
cd wp-theme
docker compose up -d     # WordPress + MariaDB
./setup.sh               # installs WP, activates theme, creates pages + menu
```

Then open **http://localhost:8080** (admin: `http://localhost:8080/wp-admin`, `admin` / `admin`).

The homepage renders without WooCommerce — shop/category/featured sections simply
stay hidden until you add the plugin (see below). Stop with `docker compose down`
(add `-v` to wipe the database).

## Manual install

1. Copy the `wildflower/` folder into `wp-content/themes/`.
2. **Appearance → Themes → Activate** “Wildflower”.
3. Install & activate **WooCommerce** (Plugins → Add New).

## Required WordPress setup

These live in the WP admin, not in code:

- **WooCommerce setup wizard** — creates Shop, Cart, Checkout and My Account
  pages, currency, payment (Stripe/PayPal), shipping zones and tax.
- **Products** — add bouquets, set featured ones (star on the product list) so
  they appear in the homepage “line-up”. Assign product categories and upload a
  category image (used by the homepage category row).
- **Menus** (Appearance → Menus) — create a menu and assign it to the
  **Primary** location (e.g. Shop, Subscriptions, Occasions, Delivery, About),
  and optionally a **Footer** menu.
- **Homepage** — Settings → Reading → “A static page” → set Home to a blank page
  (the theme renders `front-page.php` automatically).
- **Pages** — create `Subscriptions`, `About`, `Contact`, `Delivery` pages
  (slugs used by links: `/subscriptions/`, `/contact/`).
- **Hero image** — Appearance → Customize → *Wildflower — Home* → Hero image.
- **Logo** — Customize → Site Identity (optional; falls back to the text name).

> Until you upload photos, every image slot shows a branded gradient fallback —
> the site looks intentional, not broken.

## SEO / GEO / E-E-A-T

The theme outputs JSON-LD automatically:

- `Florist` (LocalBusiness) + `WebSite` in the `<head>` on every page.
- `Product` + `Offer` on single product pages.

If you install **Rank Math** or **Yoast**, they handle meta tags, sitemaps,
breadcrumbs and richer product schema. To avoid duplicate Product schema, add to
a small custom plugin or `functions.php`:

```php
add_filter( 'wildflower_output_product_schema', '__return_false' );
```

Recommended: Rank Math (free) for titles, OG/Twitter, sitemap and breadcrumbs.

## Animations

Same motion as the original design, via GSAP (loaded from CDN in
`functions.php`) plus CSS:

- Reveal-on-scroll (`.reveal`) and kinetic headings (`.kinetic`) — IntersectionObserver in `assets/js/main.js`.
- Hero image clip-path reveal — GSAP.
- Button sheen + arrow nudge, marquee testimonials — pure CSS.
- All respect `prefers-reduced-motion`.

## Editing

- Colors, type and spacing: CSS variables at the top of `style.css`.
- Buttons: `.btn--primary / --accent / --outline / --ghost`.
- WooCommerce styling: `assets/css/woocommerce.css`.
- Homepage sections: `front-page.php`.
- Brand details (email, phone, Instagram) used in templates & schema:
  `wildflower_brand()` in `functions.php`.

## Notes

- Add `screenshot.png` (1200×900) for a theme thumbnail in the admin.
- Advanced shop filters (price/colour/occasion facets like the prototype) are
  best added with a plugin such as “WooCommerce Product Filters” or FacetWP,
  then styled to match.
