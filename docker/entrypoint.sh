#!/usr/bin/env sh
set -e

cd /var/www

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ "${RUN_COMPOSER_INSTALL:-false}" = "true" ]; then
    if [ ! -f vendor/autoload.php ] || [ ! -f vendor/symfony/deprecation-contracts/function.php ]; then
        echo "Installing composer dependencies..."
        composer install --no-interaction --prefer-dist
    fi
else
    i=0
    while [ ! -f vendor/autoload.php ] || [ ! -f vendor/symfony/deprecation-contracts/function.php ]; do
        i=$((i + 1))
        if [ "$i" -gt 120 ]; then
            echo "Timed out waiting for composer dependencies to be ready."
            exit 1
        fi
        sleep 1
    done
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