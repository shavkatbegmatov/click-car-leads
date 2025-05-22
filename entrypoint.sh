#!/bin/bash

echo "🚀 Starting Laravel..."

# Laravel permission
chmod -R 775 storage bootstrap/cache

# Artisan komandalar
php artisan config:clear
php artisan route:clear
php artisan view:clear

# APP_KEY mavjud bo‘lmasa → generate
if grep -q "APP_KEY=" .env && [ -z "$(grep APP_KEY .env | cut -d '=' -f2)" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

# Link va migrate
php artisan storage:link || true
php artisan migrate --force || true

# Laravelni ishga tushirish
php artisan serve --host=0.0.0.0 --port=8000
