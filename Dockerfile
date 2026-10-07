FROM php:8.2-cli-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client

RUN docker-php-ext-install pdo pdo_mysql bcmath

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev || true

COPY . .

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/media bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "composer install --no-interaction --prefer-dist --optimize-autoloader && php artisan migrate --force && php -S 0.0.0.0:8000 -t public"]