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

if [ "$IS_LARAVEL" = "true" ]; then
  if should_skip_migrations; then
    echo "Skipping database migrations (RAILPACK_SKIP_MIGRATIONS is set)."
  else
    echo "Running migrations and seeding database ..."
    php artisan migrate --force
  fi

  php artisan storage:link
  php artisan optimize:clear
  php artisan optimize

  echo "Starting Laravel server ..."
fi

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
