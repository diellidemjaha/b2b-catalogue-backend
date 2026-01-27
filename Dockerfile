# ====== Build stage: install composer deps ======
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader
COPY . .

# ====== Runtime stage: PHP-FPM + Nginx ======
FROM php:8.2-fpm-alpine

# System deps + PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    mysql-client \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl opcache

# Configure PHP
RUN { \
  echo "memory_limit=512M"; \
  echo "upload_max_filesize=50M"; \
  echo "post_max_size=50M"; \
  echo "max_execution_time=120"; \
} > /usr/local/etc/php/conf.d/laravel.ini

WORKDIR /var/www/html

# Copy app with vendor from build stage
COPY --from=vendor /app /var/www/html

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Supervisor config to run nginx + php-fpm
COPY docker/supervisord.conf /etc/supervisord.conf

# Permissions (storage + cache)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
  && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Render provides PORT env var; expose 10000 by convention
EXPOSE 10000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
