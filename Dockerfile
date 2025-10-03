# syntax=docker/dockerfile:1.6

## ---------- Composer dependencies ----------
FROM composer:2.8 AS vendor
WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

COPY . ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction

## ---------- Frontend build ----------
FROM node:20 AS frontend
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY resources ./resources
COPY tailwind.config.js postcss.config.js vite.config.js ./
COPY public ./public
RUN npm run build

## ---------- Runtime image ----------
FROM php:8.3-apache
WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APACHE_LISTEN_PORT=8002

RUN apt-get update && apt-get install -y \
        git \
        curl \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        bcmath \
        exif \
        gd \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && a2enmod rewrite \
    && sed -ri "s!80!${APACHE_LISTEN_PORT}!g" /etc/apache2/ports.conf \
    && sed -ri "s!80!${APACHE_LISTEN_PORT}!g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g" /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

COPY . ./
COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /app/composer.lock ./composer.lock
COPY --from=vendor /app/composer.json ./composer.json
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p storage/database \
    && touch storage/database/database.sqlite \
    && mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false

EXPOSE 8002
HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 CMD curl -f http://localhost:${APACHE_LISTEN_PORT}/ || exit 1

CMD ["apache2-foreground"]
