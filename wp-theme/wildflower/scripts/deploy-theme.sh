#!/usr/bin/env bash

# Deploy only the Wildflower theme. WooCommerce products, media and the database
# are intentionally outside this deployment boundary.
set -euo pipefail

: "${WF_SSH_TARGET:?Set WF_SSH_TARGET, for example u330980060@89.116.192.79}"
: "${WF_SSH_KEY:?Set WF_SSH_KEY to the SSH private-key path}"

WF_SSH_PORT="${WF_SSH_PORT:-65002}"
WF_WP_ROOT="${WF_WP_ROOT:-/home/u330980060/domains/yellowgreen-wolf-950046.hostingersite.com/public_html}"
SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
REMOTE_THEME_DIR="${WF_WP_ROOT}/wp-content/themes/wildflower"

ssh_options=(-i "${WF_SSH_KEY}" -p "${WF_SSH_PORT}" -o StrictHostKeyChecking=accept-new)

ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "THEME_DIR='${REMOTE_THEME_DIR}' BACKUP_ROOT='${WF_WP_ROOT}/wp-content/uploads/agent-backups' bash -s" <<'REMOTE'
set -euo pipefail
theme_dir="${THEME_DIR:?}"
backup_root="${BACKUP_ROOT:?}"
stamp="$(date +%Y%m%d_%H%M%S)"
mkdir -p "$backup_root"
if [ -d "$theme_dir" ]; then
  cp -a "$theme_dir" "$backup_root/wildflower-theme-$stamp"
fi
REMOTE

rsync -az --delete \
  --exclude '.git/' \
  --exclude 'node_modules/' \
  --exclude '.DS_Store' \
  -e "ssh -i ${WF_SSH_KEY} -p ${WF_SSH_PORT} -o StrictHostKeyChecking=accept-new" \
  "${THEME_DIR}/" "${WF_SSH_TARGET}:${REMOTE_THEME_DIR}/"

ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "cd '${WF_WP_ROOT}' && wp theme activate wildflower && wp rewrite flush && wp cache flush && wp litespeed-purge all"

printf '%s\n' 'Wildflower theme deployed. WooCommerce products, database and uploads were not touched.'
