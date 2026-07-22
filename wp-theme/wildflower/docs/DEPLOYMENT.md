# Wildflower production deployment

## Scope boundary

The repository owns only the theme in `wp-theme/wildflower/`.

WooCommerce products, product attributes, orders, product images, attachment metadata and all files under `wp-content/uploads/` live in WordPress/MySQL. They are not versioned in this repository and must never be removed, restored or overwritten by a theme deployment.

## Required procedure

1. Confirm the only production target is `https://boston-wildflower.com` on the Hostinger `.81` server. The old `.79` preview site is not a deployment target.
2. Inspect `git status --short`. The deploy script refuses to run when `wp-theme/wildflower/` has tracked or untracked changes, so unrelated local work cannot be uploaded accidentally.
3. Set the exact production connection values and the explicit confirmation value shown below.
4. Run `scripts/deploy-theme.sh`. It verifies the SSH target, WordPress root and live `home` URL before doing anything.
5. The script backs up the remote theme outside `public_html`, then syncs only `wp-theme/wildflower/` to `wp-content/themes/wildflower/`.
6. Never use FTP/SFTP mirror, broad rsync, or repository checkout against `public_html`. Never run `wp db reset`, `wp db import`, or a blanket media regeneration as part of theme deployment.
7. Verify `/shop/`, a variable rose product and an existing add-on; confirm product photos and variation selectors still render.

## Production command

```bash
export WF_SSH_TARGET='u797234100@157.173.208.81'
export WF_SSH_KEY='/root/.ssh/winfix_ed25519'
export WF_SSH_PORT='65002'
export WF_WP_ROOT='/home/u797234100/domains/boston-wildflower.com/public_html'
export WF_CONFIRM_DEPLOY='boston-wildflower.com'
./wp-theme/wildflower/scripts/deploy-theme.sh
```

The script creates a dated backup under `/home/u797234100/boston-wildflower-theme-backups/`. Its rsync destination is the theme directory only; it cannot touch WooCommerce products, MySQL, orders or `wp-content/uploads/`.

Products are live content, not Git content. A theme commit or deployment must never attempt to restore a product snapshot from the repository. Catalog changes require their own verified database backup and purpose-built importer.
