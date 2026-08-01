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

# ============================================================
# WooCommerce — full auto-provision (idempotent)
# Run:  WITH_WOO=1 ./setup.sh    (or just ./setup.sh — runs by default)
# Builds: Woo + classic Cart/Checkout, attributes+terms, demo bouquets &
# add-ons, cross-sells — a styled, screenshot-ready shop in one command.
# ============================================================
if [ "${WITH_WOO:-1}" = "1" ]; then
  echo ""
  echo "🛒 Provisioning WooCommerce..."

  if ! WP plugin is-active woocommerce >/dev/null 2>&1; then
    echo "   installing WooCommerce..."
    WP plugin install woocommerce --activate
  else
    echo "   WooCommerce already active."
  fi

  # Force CLASSIC cart/checkout (shortcodes) so the theme CSS fully applies.
  ensure_wc_page() {
    local title="$1" slug="$2" shortcode="$3" option="$4" id
    id="$(WP post list --post_type=page --name="$slug" --field=ID 2>/dev/null | tr -d '\r' | head -n1 || true)"
    if [ -z "$id" ]; then
      id="$(WP post create --post_type=page --post_status=publish --post_title="$title" --post_name="$slug" --post_content="$shortcode" --porcelain | tr -d '\r')"
      echo "   + $title page"
    else
      WP post update "$id" --post_content="$shortcode" >/dev/null 2>&1 || true
    fi
    [ -n "$id" ] && WP option update "$option" "$id" >/dev/null 2>&1 || true
  }
  ensure_wc_page "Shop"     "shop"     "" "woocommerce_shop_page_id"
  ensure_wc_page "Cart"     "cart"     "[woocommerce_cart]"     "woocommerce_cart_page_id"
  ensure_wc_page "Checkout" "checkout" "[woocommerce_checkout]" "woocommerce_checkout_page_id"
  MYACC="$(WP post list --post_type=page --name="my-account" --field=ID 2>/dev/null | tr -d '\r' | head -n1 || true)"
  [ -z "$MYACC" ] && ensure_wc_page "My account" "my-account" "[woocommerce_my_account]" "woocommerce_myaccount_page_id"

  echo "🏷  Registering product attributes..."
  ensure_attr() {  # name slug  -> echoes attribute id
    local name="$1" slug="$2" id
    id="$(WP wc product_attribute list --user=admin --field=id --slug="pa_$slug" 2>/dev/null | tr -d '\r' | head -n1 || true)"
    if [ -z "$id" ]; then
      id="$(WP wc product_attribute create --name="$name" --slug="$slug" --user=admin --porcelain 2>/dev/null | tr -d '\r' || true)"
    fi
    echo "$id"
  }
  add_terms() {  # taxonomy  term...
    local tax="pa_$1"; shift
    for t in "$@"; do WP term create "$tax" "$t" >/dev/null 2>&1 || true; done
  }
  ensure_attr "Flower Type" "flower-type" >/dev/null
  ensure_attr "Palette"     "palette"     >/dev/null
  ensure_attr "Occasion"    "occasion"    >/dev/null
  ensure_attr "Size"        "size"        >/dev/null
  ensure_attr "Availability" "availability" >/dev/null
  add_terms flower-type "Garden Roses" "Peonies" "Ranunculus & Anemones" "Tulips" "Hydrangeas" "Lilies" "Mixed Flowers"
  add_terms palette "White, Cream & Ivory" "Blush, Pink & Pastel" "Burgundy & Plum" "Red & Crimson" "Peach & Coral"
  add_terms occasion "Birthday" "Anniversary" "Love & Romance" "Thank You" "Sympathy" "Just Because"
  add_terms size "Petite" "Classic" "Grand"
  add_terms availability "Same-day"

  echo "🗂  Product categories..."
  WP term create product_cat "Bouquets" >/dev/null 2>&1 || true
  WP term create product_cat "Add-ons"  >/dev/null 2>&1 || true

  echo "💐 Seeding demo products (idempotent)..."
  make_product() {  # title price sale featured category
    local title="$1" price="$2" sale="$3" feat="$4" cat="$5" id slug
    slug="$(echo "$title" | tr '[:upper:] ' '[:lower:]-')"
    id="$(WP post list --post_type=product --name="$slug" --field=ID 2>/dev/null | tr -d '\r' | head -n1 || true)"
    if [ -z "$id" ]; then
      id="$(WP wc product create --name="$title" --type=simple --regular_price="$price" --user=admin --porcelain 2>/dev/null | tr -d '\r' || true)"
      [ -n "$sale" ] && WP wc product update "$id" --sale_price="$sale" --user=admin >/dev/null 2>&1 || true
      [ "$feat" = "1" ] && WP wc product update "$id" --featured=true --user=admin >/dev/null 2>&1 || true
      [ -n "$id" ] && WP post term set "$id" product_cat "$cat" >/dev/null 2>&1 || true
      echo "   + $title"
    fi
    echo "$id"
  }
  B1="$(make_product "Pink Peony Dream" 49 44 1 Bouquets)"
  B2="$(make_product "Sunday Market" 50 "" 1 Bouquets)"
  B3="$(make_product "Wildfield" 52 "" 0 Bouquets)"
  B4="$(make_product "Garden Blush" 58 "" 1 Bouquets)"
  B5="$(make_product "Meadow Light" 54 48 0 Bouquets)"
  B6="$(make_product "Rosa Bianca" 60 "" 0 Bouquets)"
  A1="$(make_product "Glass Vase" 18 "" 0 Add-ons)"
  A2="$(make_product "Soy Candle" 24 "" 0 Add-ons)"
  A3="$(make_product "Belgian Truffles" 16 "" 0 Add-ons)"
  A4="$(make_product "Handwritten Card" 0 "" 0 Add-ons)"

  echo "🎁 Linking add-ons as cross-sells (cart up-sell)..."
  CS="[$A1,$A2,$A3,$A4]"
  for B in "$B1" "$B2" "$B3" "$B4" "$B5" "$B6"; do
    [ -n "$B" ] && WP wc product update "$B" --cross_sell_ids="$CS" --user=admin >/dev/null 2>&1 || true
    [ -n "$B" ] && WP wc product update "$B" --upsell_ids="[$A1,$A2,$A3]" --user=admin >/dev/null 2>&1 || true
  done

  WP wc tool run regenerate_product_lookup_tables --user=admin >/dev/null 2>&1 || true
  WP rewrite flush --hard >/dev/null 2>&1 || true
  echo "   shop ready → $URL/shop/"
fi
