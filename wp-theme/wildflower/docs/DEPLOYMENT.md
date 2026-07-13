# Wildflower production deployment

## Scope boundary

The repository owns only the theme in `wp-theme/wildflower/`.

WooCommerce products, product attributes, orders, product images, attachment metadata and all files under `wp-content/uploads/` live in WordPress/MySQL. They are not versioned in this repository and must never be removed, restored or overwritten by a theme deployment.

## Required procedure

1. Confirm the active target is Wildflower: `yellowgreen-wolf-950046.hostingersite.com` on the Hostinger `.79` server.
2. Inspect `git status --short`; preserve unrelated local work.
3. Back up the current remote theme directory.
4. Run `scripts/deploy-theme.sh` with the target SSH environment variables. It syncs only `wp-theme/wildflower/` to `wp-content/themes/wildflower/`.
5. Never run database imports, `wp db reset`, `wp db import`, `wp media regenerate --yes` on all media, or a broad rsync into `public_html` as part of a theme deploy.
6. Activate the theme, flush rewrites and clear WP/LiteSpeed cache.
7. Verify `/shop/`, one new product and one existing add-on; confirm product photos still render.

## Production command

```bash
export WF_SSH_TARGET='u330980060@89.116.192.79'
export WF_SSH_KEY='/root/.ssh/winfix_ed25519'
export WF_SSH_PORT='65002'
export WF_WP_ROOT='/home/u330980060/domains/yellowgreen-wolf-950046.hostingersite.com/public_html'
./wp-theme/wildflower/scripts/deploy-theme.sh
```

The script creates a dated remote theme backup under `wp-content/uploads/agent-backups/` before synchronizing. Its rsync destination is the theme directory only; it cannot touch `uploads` or the database.
