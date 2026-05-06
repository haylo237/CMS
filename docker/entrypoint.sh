#!/usr/bin/env sh
set -e

cd /var/www

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

mkdir -p storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ -f .env ]; then
    if ! grep -q '^APP_KEY=base64:' .env; then
        php artisan key:generate --force
    fi
fi

if [ "${RUN_MIGRATIONS}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"