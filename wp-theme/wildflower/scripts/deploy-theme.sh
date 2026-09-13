#!/usr/bin/env bash

# Deploy only the Wildflower theme. WooCommerce products, media and the database
# are intentionally outside this deployment boundary.
set -euo pipefail

: "${WF_SSH_TARGET:?Set WF_SSH_TARGET to the Wildflower production SSH target}"
: "${WF_SSH_KEY:?Set WF_SSH_KEY to the SSH private-key path}"
: "${WF_CONFIRM_DEPLOY:?Set WF_CONFIRM_DEPLOY to boston-wildflower.com}"

EXPECTED_SSH_TARGET='u797234100@157.173.208.81'
EXPECTED_SSH_PORT='65002'
EXPECTED_WP_ROOT='/home/u797234100/domains/boston-wildflower.com/public_html'
EXPECTED_HOME='https://boston-wildflower.com'
REMOTE_BACKUP_ROOT='/home/u797234100/boston-wildflower-theme-backups'
WF_SSH_PORT="${WF_SSH_PORT:-65002}"
WF_WP_ROOT="${WF_WP_ROOT:-${EXPECTED_WP_ROOT}}"

if [[ "${WF_SSH_TARGET}" != "${EXPECTED_SSH_TARGET}" ]]; then
  printf '%s\n' "Refusing unexpected SSH target: ${WF_SSH_TARGET}" >&2
  exit 1
fi
if [[ "${WF_SSH_PORT}" != "${EXPECTED_SSH_PORT}" ]]; then
  printf '%s\n' "Refusing unexpected SSH port: ${WF_SSH_PORT}" >&2
  exit 1
fi
if [[ "${WF_WP_ROOT}" != "${EXPECTED_WP_ROOT}" ]]; then
  printf '%s\n' "Refusing unexpected WordPress root: ${WF_WP_ROOT}" >&2
  exit 1
fi
if [[ "${WF_CONFIRM_DEPLOY}" != 'boston-wildflower.com' ]]; then
  printf '%s\n' 'Refusing deployment without the exact production confirmation.' >&2
  exit 1
fi
if [[ ! -r "${WF_SSH_KEY}" ]]; then
  printf '%s\n' "SSH key is not readable: ${WF_SSH_KEY}" >&2
  exit 1
fi

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd -- "${SCRIPT_DIR}/.." && pwd)"
REPO_ROOT="$(git -C "${THEME_DIR}" rev-parse --show-toplevel)"
THEME_RELATIVE="${THEME_DIR#"${REPO_ROOT}/"}"
REMOTE_THEME_DIR="${WF_WP_ROOT}/wp-content/themes/wildflower"

if [[ "${THEME_RELATIVE}" == "${THEME_DIR}" ]]; then
  printf '%s\n' 'The Wildflower theme is not inside the detected Git repository.' >&2
  exit 1
fi
if [[ -n "$(git -C "${REPO_ROOT}" status --porcelain --untracked-files=normal -- "${THEME_RELATIVE}")" ]]; then
  printf '%s\n' 'Refusing to deploy a dirty Wildflower theme. Commit or deliberately preserve local work first.' >&2
  exit 1
fi

ssh_options=(-i "${WF_SSH_KEY}" -p "${WF_SSH_PORT}" -o StrictHostKeyChecking=accept-new)

remote_home="$(ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "cd '${WF_WP_ROOT}' && wp option get home")"
if [[ "${remote_home%/}" != "${EXPECTED_HOME}" ]]; then
  printf '%s\n' "Refusing unexpected live WordPress URL: ${remote_home}" >&2
  exit 1
fi

ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "THEME_DIR='${REMOTE_THEME_DIR}' BACKUP_ROOT='${REMOTE_BACKUP_ROOT}' bash -s" <<'REMOTE'
set -euo pipefail
theme_dir="${THEME_DIR:?}"
backup_root="${BACKUP_ROOT:?}"
stamp="$(date +%Y%m%d_%H%M%S)"
mkdir -p "$backup_root"
if [ -d "$theme_dir" ]; then
  backup_file="$backup_root/wildflower-theme-$stamp.tar.gz"
  tar -C "$(dirname "$theme_dir")" -czf "$backup_file" "$(basename "$theme_dir")"
  test -s "$backup_file"
fi
REMOTE

rsync -az --delete-delay --itemize-changes \
  --exclude '.git/' \
  --exclude 'node_modules/' \
  --exclude '.DS_Store' \
  -e "ssh -i ${WF_SSH_KEY} -p ${WF_SSH_PORT} -o StrictHostKeyChecking=accept-new" \
  "${THEME_DIR}/" "${WF_SSH_TARGET}:${REMOTE_THEME_DIR}/"

ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "cd '${WF_WP_ROOT}' && wp theme activate wildflower && wp rewrite flush && wp cache flush && wp litespeed-purge all"

verified_home="$(ssh "${ssh_options[@]}" "${WF_SSH_TARGET}" "cd '${WF_WP_ROOT}' && wp option get home")"
if [[ "${verified_home%/}" != "${EXPECTED_HOME}" ]]; then
  printf '%s\n' 'Post-deployment site verification failed.' >&2
  exit 1
fi

printf '%s\n' 'Wildflower theme deployed. WooCommerce products, database and uploads were not touched.'
