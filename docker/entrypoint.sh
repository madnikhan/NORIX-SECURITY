#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

PORT="${PORT:-80}"

# Render assigns a dynamic port — Apache must listen on it.
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
if grep -q "<VirtualHost" /etc/apache2/sites-available/000-default.conf; then
  sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

# Laravel expects APP_KEY like "base64:....". Render generateValue is often a raw secret.
if [[ -z "${APP_KEY:-}" || "${APP_KEY}" != base64:* ]]; then
  export APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"
  echo "Generated a valid Laravel APP_KEY for this boot."
fi

# Prefer Laravel's DB_URL; Render provides DATABASE_URL.
if [[ -z "${DB_URL:-}" && -n "${DATABASE_URL:-}" ]]; then
  export DB_URL="${DATABASE_URL}"
  echo "Mapped DATABASE_URL -> DB_URL"
fi

# Render Postgres expects SSL.
export DB_SSLMODE="${DB_SSLMODE:-require}"

echo "Discovering packages..."
php artisan package:discover --ansi || true

echo "Publishing Filament assets..."
php artisan filament:assets --ansi || true

echo "Caching Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database (safe to re-run)..."
php artisan db:seed --force

echo "Syncing hired applications to guard roster..."
php artisan norix:sync-hired-guards || true

echo "Linking storage..."
php artisan storage:link || true

if [[ ! -f public/build/manifest.json ]]; then
  echo "WARNING: public/build/manifest.json missing — CSS/JS may be broken."
fi

echo "Starting Apache on port ${PORT}..."
exec docker-php-entrypoint "$@"
