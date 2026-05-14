#!/bin/sh
set -e

echo "Setting storage permissions..."
# Only chmod/chown directories (not files) to avoid git filemode changes on the host
find storage bootstrap/cache -type d -exec chown www-data:www-data {} \;
find storage bootstrap/cache -type d -exec chmod 775 {} \;

echo "Installing Composer dependencies..."
composer install --no-interaction

# Generate app key if not set
if [ -z "$APP_KEY" ] && grep -q "^APP_KEY=$" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate
fi

echo "Running migrations..."
php artisan migrate --force

echo "Starting PHP-FPM..."
exec php-fpm
