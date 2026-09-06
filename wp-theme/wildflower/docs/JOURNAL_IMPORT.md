# Journal — 8 SEO articles (import)

The theme's Journal template (`page-journal.php`) renders real WordPress **posts**.
Article text and the "Hello World" default post live in the WordPress **database**,
not in theme files — so they can't be created or deleted by an FTP file deploy.
Everything below is a 2-minute job in `wp-admin`.

## 1. Delete the default "Hello world!" post
`wp-admin → Posts → All Posts →` hover **Hello world!** → **Trash** → then **Trash → Empty Trash**.
(Also fine to trash the sample "Sample Page" and the default comment.)

## 2. Import the 8 articles
1. `wp-admin → Tools → Import → WordPress` (install the "WordPress Importer" if prompted).
2. Upload **`journal-8-articles.wordpress.xml`** (in this `docs/` folder).
3. On the assign-authors step, map the author to your account (or the `wildflower` user).
   Leave **"Download and import file attachments"** unchecked — the posts use the
   theme's placeholder art until you add real photos.
4. Import. You'll get 8 published posts with categories, tags, excerpts and meta.

## 3. Point the Journal at the posts
- The `Journal` page already lists posts automatically. If posts don't show,
  set `Settings → Reading → Posts page` to your Journal page, or make sure the
  page uses the **Journal** template.

## 4. Recommended follow-ups (for E-E-A-T + GEO, per the 2026 playbook)
- Replace the placeholder cover on each post with a **real, original studio photo**
  (Featured image). The grid + article previews now have the same parallax as the
  gallery, so real photos will look best.
- Add a real **author** with a headshot and one-line credential (E-E-A-T weights
  first-hand experience heavily in 2026). The articles are written in the studio's
  first-person voice to support this.
- Keep a visible **"Last updated"** date and refresh seasonal posts before each peak.

## What's in each article
Answer-first intro, question-based H2s, a **Key takeaways** list, first-hand Boston
studio detail, and a **FAQ** block (AEO). Meta title/description are included as
`_yoast_wpseo_*` post meta (used automatically if Yoast is active; harmless if not).

Topics: cut-flower care · what's blooming in Boston · same-day delivery · flowers by
occasion · choosing a subscription · sympathy & funeral flowers · Boston wedding/event
flowers · why local & seasonal.
