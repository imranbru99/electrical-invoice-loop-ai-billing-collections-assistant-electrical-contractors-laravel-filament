#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist --ignore-platform-reqs
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

php artisan package:discover --ansi || true
php artisan filament:assets || true
php artisan config:clear || true
php artisan migrate --force
php artisan db:seed --force

exec "$@"
