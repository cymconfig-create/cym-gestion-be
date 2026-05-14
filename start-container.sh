#!/bin/bash
#
# Override for Railpack / FrankenPHP (see core/providers/php/start-container.sh upstream).
# Upstream only skips migrations when RAILPACK_SKIP_MIGRATIONS is the literal string "true".
# We accept true/1/yes/on (any case) so Railway env vars like "1" work.
#
set -e

should_skip_migrations() {
  val=$(printf '%s' "${RAILPACK_SKIP_MIGRATIONS:-}" | tr '[:upper:]' '[:lower:]')
  case "$val" in
    true|1|yes|on) return 0 ;;
    *) return 1 ;;
  esac
}

should_run_migrations() {
  val=$(printf '%s' "${RAILPACK_RUN_MIGRATIONS:-}" | tr '[:upper:]' '[:lower:]')
  case "$val" in
    true|1|yes|on) return 0 ;;
    *) return 1 ;;
  esac
}

if [ "$IS_LARAVEL" = "true" ]; then
  if should_skip_migrations; then
    echo "Skipping database migrations (RAILPACK_SKIP_MIGRATIONS is set)."
  elif should_run_migrations; then
    echo "Running migrations (RAILPACK_RUN_MIGRATIONS is set) ..."
    php artisan migrate --force
  else
    echo "Skipping database migrations by default. Set RAILPACK_RUN_MIGRATIONS=true to enable."
  fi

  php artisan storage:link
  php artisan optimize:clear
  php artisan optimize

  echo "Starting Laravel server ..."
fi

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
