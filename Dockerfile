############################
# Stage 1: Composer Dependencies
############################
FROM composer:2.8 AS vendor

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (production mode)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --no-progress

# Copy source code and dump autoload
COPY . .
RUN composer dump-autoload --optimize --no-scripts

############################
# Stage 2: Frontend Build
############################
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./

# Install ALL dependencies (including dev) for build - CHANGED
RUN npm ci

# Copy source files needed for build
COPY resources ./resources
COPY public ./public
COPY tailwind.config.js postcss.config.js vite.config.js ./

# Build assets
RUN npm run build

############################
# Stage 3: PHP-FPM Runtime
############################
FROM php:8.2-fpm

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        intl \
        bcmath \
        opcache

# Configure OPcache for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.fast_shutdown=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Configure PHP
RUN { \
    echo 'memory_limit=512M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=20M'; \
    echo 'max_execution_time=300'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Copy application code
COPY --chown=www-data:www-data . /var/www

# Copy vendor from composer stage
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/vendor

# Copy built assets from frontend stage
COPY --from=frontend --chown=www-data:www-data /app/public/build /var/www/public/build

# Create necessary directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD php-fpm-healthcheck || exit 1

# Run PHP-FPM
CMD ["php-fpm"]
