#!/bin/bash

echo "Starting Laravel..."

chmod -R 775 storage bootstrap/cache

php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan optimize:clear

php artisan event:clear

if grep -q "APP_KEY=" .env && [ -z "$(grep APP_KEY .env | cut -d '=' -f2)" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

php artisan storage:link || true
php artisan migrate --force || true

php artisan cache:clear

php artisan queue:work --sleep=3 --tries=3 &
php artisan serve --host=0.0.0.0 --port=8000
