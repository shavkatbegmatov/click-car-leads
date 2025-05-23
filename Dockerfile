# Stage 1: Composer dependencies
FROM composer:2 AS deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Stage 2: App runtime
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
      git curl zip unzip libzip libpng libjpeg-turbo oniguruma libxml2 postgresql-client freetype-dev \
    && docker-php-ext-configure gd --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring exif pcntl bcmath gd

# Copy composer deps
WORKDIR /var/www
COPY --from=deps /app/vendor /var/www/vendor

# Copy the rest of the application
COPY . /var/www

# Entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose HTTP port
EXPOSE 8000

# Run entrypoint
ENTRYPOINT ["entrypoint.sh"]
