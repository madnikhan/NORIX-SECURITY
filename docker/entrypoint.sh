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

echo "Linking storage..."
php artisan storage:link || true

echo "Starting Apache on port ${PORT}..."
exec docker-php-entrypoint "$@"
