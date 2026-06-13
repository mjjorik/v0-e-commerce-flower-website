#!/usr/bin/env bash
#
# One-shot WordPress install + Wildflower theme activation, via wp-cli in Docker.
# Run AFTER `docker compose up -d`.
#
#   cd wp-theme && docker compose up -d && ./setup.sh
#
# Re-running is safe (idempotent). Override the URL with SITE_URL=... ./setup.sh
set -euo pipefail

URL="${SITE_URL:-http://localhost:8080}"
TITLE="Wildflower"
ADMIN_USER="admin"
ADMIN_PASS="admin"
ADMIN_EMAIL="admin@example.com"

# Helper: run a wp-cli command inside the wpcli service.
WP() { docker compose run --rm -T wpcli "$@"; }

echo "⏳ Waiting for WordPress core files & database..."
for i in $(seq 1 40); do
  if WP core version >/dev/null 2>&1; then
    echo "   core files ready."
    break
  fi
  sleep 3
  if [ "$i" -eq 40 ]; then
    echo "❌ WordPress did not become ready. Is 'docker compose up -d' running?" >&2
    exit 1
  fi
done

# Install WordPress if needed.
if WP core is-installed >/dev/null 2>&1; then
  echo "ℹ️  WordPress already installed."
else
  echo "📦 Installing WordPress..."
  WP core install \
    --url="$URL" \
    --title="$TITLE" \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email
fi

echo "🎨 Activating the Wildflower theme..."
WP theme activate wildflower

echo "📄 Creating pages..."
create_page() {
  local title="$1" slug="$2" template="${3:-}"
  local id
  id="$(WP post list --post_type=page --name="$slug" --field=ID 2>/dev/null | tr -d '\r' | head -n1 || true)"
  if [ -z "$id" ]; then
    id="$(WP post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" --porcelain | tr -d '\r')"
    echo "   + $title (/$slug/)"
  fi
  if [ -n "$template" ] && [ -n "$id" ]; then
    WP post meta update "$id" _wp_page_template "$template" >/dev/null 2>&1 || true
  fi
  echo "$id"
}

HOME_ID="$(create_page "Home" "home")"
create_page "Subscriptions" "subscriptions" >/dev/null
create_page "Occasions"     "occasions"     >/dev/null
create_page "Gallery"       "gallery"       "page-gallery.php" >/dev/null
create_page "Journal"       "journal"       "page-journal.php" >/dev/null
create_page "Delivery"      "delivery"      >/dev/null
create_page "About"         "about"         >/dev/null
create_page "Contact"       "contact"       >/dev/null

echo "🏠 Setting the static homepage..."
WP option update show_on_front page
WP option update page_on_front "$HOME_ID"
WP rewrite structure '/%postname%/' --hard >/dev/null 2>&1 || true

echo "🧭 Building the primary menu..."
if ! WP menu list --fields=name 2>/dev/null | grep -q "Primary"; then
  WP menu create "Primary" >/dev/null
fi
# Add page links (ignore duplicates on re-run). "Shop" is injected automatically
# by the theme when WooCommerce is active.
for slug in subscriptions occasions gallery journal delivery about contact; do
  PID="$(WP post list --post_type=page --name="$slug" --field=ID 2>/dev/null | tr -d '\r' | head -n1 || true)"
  if [ -n "$PID" ]; then WP menu item add-post Primary "$PID" >/dev/null 2>&1 || true; fi
done
WP menu location assign Primary primary >/dev/null 2>&1 || true

echo ""
echo "✅ Done!"
echo "   Site:   $URL"
echo "   Admin:  $URL/wp-admin   ($ADMIN_USER / $ADMIN_PASS)"
echo ""
echo "Later, to enable the shop sections:"
echo "   docker compose run --rm wpcli plugin install woocommerce --activate"
echo "   docker compose run --rm wpcli plugin install seo-by-rank-math --activate"
