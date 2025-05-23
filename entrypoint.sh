#!/usr/bin/env sh
set -e

echo "▶️  Starting Laravel bootstrap..."

# Ensure directories are writeable
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Clear any old caches
php artisan optimize:clear

# Generate APP_KEY if missing
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
  echo "🔑 Generating APP_KEY..."
  php artisan key:generate
fi

# Link storage and run migrations
php artisan storage:link --force
php artisan migrate --force

# Optionally cache config/routes in production
# php artisan config:cache
# php artisan route:cache

# Start queue worker and scheduler in the background
php artisan queue:work --sleep=3 --tries=3 &
php artisan schedule:work &

# Finally, start the HTTP server
echo "🌐  Launching PHP server on 0.0.0.0:8000"
exec php artisan serve --host=0.0.0.0 --port=8000
