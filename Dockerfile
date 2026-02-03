# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# Stage 2: PHP app
FROM php:8.2-cli-alpine
RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libpq-dev \
    sqlite-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pdo_sqlite \
        zip \
        gd \
        intl \
        mbstring \
        opcache \
        pcntl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build /var/www/html/public/build
RUN composer install --no-dev --no-interaction --optimize-autoloader
EXPOSE 8000
CMD ["sh", "-c", "php artisan migrate --force && (php artisan storage:link || true) && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
