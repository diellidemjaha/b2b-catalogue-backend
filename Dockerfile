# =========================
# Base image with PHP extensions
# =========================
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    nginx \
    supervisor \
    mysql-client \
    && docker-php-ext-install \
      pdo \
      pdo_mysql \
      mbstring \
      zip \
      intl \
      opcache

# Install Composer manually (more reliable)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# =========================
# Copy composer files first
# =========================
COPY composer.json composer.lock ./

# Disable scripts for now (prevents env errors)
RUN composer install \
  --no-dev \
  --prefer-dist \
  --no-interaction \
  --no-progress \
  --optimize-autoloader \
  --no-scripts

# =========================
# Copy the rest of the app
# =========================
COPY . .

# Now run scripts safely
RUN composer dump-autoload --optimize

# Permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

# =========================
# Nginx + Supervisor
# =========================
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 10000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
